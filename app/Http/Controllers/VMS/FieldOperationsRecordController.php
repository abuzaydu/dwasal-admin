<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FieldOperationsRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FieldOperationsRecordController extends Controller
{
    private const ROW_COUNT = 24;

    public function index(Request $request)
    {
        $is_post_query = $request->filled('start_date');
        $start_date = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->input('end_date', now()->format('Y-m-d'));

        $records = $this->recordQuery()
            ->withCount('rows')
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('record_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('record_date', '<=', $request->end_date))
            ->latest('record_date')
            ->latest('id')
            ->get();

        return view('vms.field-operations.index', [
            'page' => 'Field Operations Records',
            'records' => $records,
            'is_post_query' => $is_post_query,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function create()
    {
        return view('vms.field-operations.form', [
            'page' => 'New Field Operations Record',
            'record' => new FieldOperationsRecord([
                'record_date' => now()->toDateString(),
                'record_no' => $this->nextRecordNo(),
            ]),
            'company' => $this->company(),
            'rows' => $this->blankRows(),
            'formAction' => route('field-operations.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $record = DB::transaction(function () use ($validated) {
            $company = Company::whereKey(Session::get('company_id'))->lockForUpdate()->firstOrFail();
            $record = FieldOperationsRecord::create(array_merge($validated['record'], [
                'company_id' => $company->id,
                'created_by' => auth()->id(),
                'record_no' => $this->nextRecordNo($company->id),
            ]));
            $this->saveRows($record, $validated['rows']);
            return $record;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Field operations record saved successfully.',
                'update_url' => route('field-operations.autosave', $record),
                'record_url' => route('field-operations.show', $record),
            ]);
        }

        return redirect()->route('field-operations.show', $record)->with('success', 'Field operations record created successfully.');
    }

    public function show($id)
    {
        $record = $this->recordQuery()->with('rows')->findOrFail($id);

        return view('vms.field-operations.show', compact('record') + ['page' => 'Field Operations Record']);
    }

    public function edit($id)
    {
        $record = $this->recordQuery()->with('rows')->findOrFail($id);

        return view('vms.field-operations.form', [
            'page' => 'Edit Field Operations Record',
            'record' => $record,
            'company' => $this->company(),
            'rows' => $this->formRows($record),
            'formAction' => route('field-operations.update', $record),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = $this->recordQuery()->findOrFail($id);
        $validated = $this->validated($request);

        DB::transaction(function () use ($record, $validated) {
            $record->update($validated['record']);
            $record->rows()->delete();
            $this->saveRows($record, $validated['rows']);
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Field operations record saved successfully.']);
        }

        return redirect()->route('field-operations.show', $record)->with('success', 'Field operations record updated successfully.');
    }

    public function destroy($id)
    {
        $record = $this->recordQuery()->findOrFail($id);
        $record->delete();

        return redirect()->route('field-operations.index')->with('success', 'Field operations record deleted successfully.');
    }

    public function print($id)
    {
        $record = $this->recordQuery()->with('rows')->findOrFail($id);
        return view('vms.field-operations.report', compact('record'));
    }

    public function pdf($id)
    {
        $record = $this->recordQuery()->with('rows')->findOrFail($id);
        $pdf = Pdf::loadView('vms.field-operations.report', compact('record'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('field-operations-' . $record->record_no . '.pdf');
    }

    public function sample()
    {
        return view('vms.field-operations.report', [
            'record' => $this->sampleRecord(),
            'sample' => true,
        ]);
    }

    public function samplePdf()
    {
        $pdf = Pdf::loadView('vms.field-operations.report', [
            'record' => $this->sampleRecord(),
            'sample' => true,
        ])->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('daily-field-operations-sample.pdf');
    }

    private function sampleRecord(): FieldOperationsRecord
    {
        $record = new FieldOperationsRecord();
        $record->setRelation('company', $this->company());
        $record->setRelation('rows', collect());

        return $record;
    }

    private function recordQuery()
    {
        return FieldOperationsRecord::where('company_id', Session::get('company_id'));
    }

    private function company(): Company
    {
        return Company::findOrFail(Session::get('company_id'));
    }

    private function nextRecordNo(?int $companyId = null): string
    {
        $companyId ??= Session::get('company_id');
        $largest = 0;

        FieldOperationsRecord::where('company_id', $companyId)
            ->pluck('record_no')
            ->each(function ($recordNo) use (&$largest) {
                $number = (int) preg_replace('/\D+/', '', (string) $recordNo);
                $largest = max($largest, $number);
            });

        return str_pad((string) ($largest + 1), 4, '0', STR_PAD_LEFT);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'record_date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'sign_in_time' => ['nullable', 'date_format:H:i'],
            'sign_out_time' => ['nullable', 'date_format:H:i'],
            'fuel_in' => ['nullable', 'numeric', 'min:0'],
            'fuel_out' => ['nullable', 'numeric', 'min:0'],
            'quantity_of_trips' => ['nullable', 'integer', 'min:0'],
            'machine_condition' => ['nullable', 'string'],
            'signature' => ['nullable', 'string', 'max:255'],
            'rows' => ['array', 'max:' . self::ROW_COUNT],
            'rows.*.truck_no' => ['nullable', 'string', 'max:100'],
            'rows.*.amount_per_trip' => ['nullable', 'numeric', 'min:0'],
            'rows.*.trip_count' => ['nullable', 'integer', 'min:0'],
            'rows.*.cubic' => ['nullable', 'numeric', 'min:0'],
            'rows.*.expenditure_entries_json' => ['nullable', 'string'],
            'rows.*.cash_entries_json' => ['nullable', 'string'],
        ]);

        $rows = collect($validated['rows'] ?? [])->map(function (array $row) {
            $row['expenditure_entries'] = $this->decodeEntries($row['expenditure_entries_json'] ?? null);
            $row['cash_entries'] = $this->decodeEntries($row['cash_entries_json'] ?? null);
            unset($row['expenditure_entries_json'], $row['cash_entries_json']);
            return $row;
        })->all();

        return [
            'record' => collect($validated)->except('rows')->all(),
            'rows' => $rows,
        ];
    }

    private function decodeEntries(?string $entries): array
    {
        if (!$entries) {
            return [];
        }

        $decoded = json_decode($entries, true);
        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)->map(fn (array $entry) => [
            'amount' => (float) ($entry['amount'] ?? 0),
            'details' => trim((string) ($entry['details'] ?? '')),
        ])->filter(fn (array $entry) => $entry['amount'] > 0 || $entry['details'] !== '')->values()->all();
    }

    private function saveRows(FieldOperationsRecord $record, array $rows): void
    {
        foreach ($rows as $index => $row) {
            if (!$this->hasRowValue($row)) {
                continue;
            }
            $record->rows()->create(array_merge($row, ['row_number' => $index + 1]));
        }
    }

    private function hasRowValue(array $row): bool
    {
        return collect($row)->contains(function ($value) {
            return is_array($value) ? count($value) > 0 : $value !== null && $value !== '';
        });
    }

    private function blankRows(): array
    {
        return array_fill(0, self::ROW_COUNT, []);
    }

    private function formRows(FieldOperationsRecord $record): array
    {
        $rows = $this->blankRows();
        foreach ($record->rows as $row) {
            $rows[$row->row_number - 1] = collect($row->toArray())
                ->only([
                    'truck_no',
                    'cubic',
                    'trip_count',
                    'amount_per_trip',
                    'expenditure_entries',
                    'cash_entries',
                ])
                ->all();
        }
        return $rows;
    }
}