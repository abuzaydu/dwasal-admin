<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\DocumentAccessLog;
use App\Models\DocumentType;
use App\Models\LegalDocument;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Tanzania VMS - Legal Documents (Static): Vehicle, Driver, Business, Safety.
 */
class LegalDocumentController extends Controller
{
    private function ensureVehicleDocumentTypes(int $companyId): void
    {
        foreach (LegalDocument::REQUIRED_VEHICLE_DOCS as $name) {
            DocumentType::firstOrCreate(
                ['company_id' => $companyId, 'dt_name' => $name],
                ['active' => true]
            );
        }
    }

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tanzania: Vehicle-wise legal documents status dashboard.
     * Admin can select a vehicle and see each required document status.
     */
    public function vehicleStatus(Request $request)
    {
        $companyId = Session::get('company_id');

        $this->ensureVehicleDocumentTypes($companyId);

        $page = 'Vehicle Legal Document Status';
        $requiredDocs = LegalDocument::REQUIRED_VEHICLE_DOCS;

        $status = $request->get('status'); // valid|expiring|expired
        $vehicleId = $request->get('vehicle_id');
        $vehicles = Vehicle::where('company_id', $companyId)->orderBy('plate_no')->get();

        // Load only docs for the required Tanzania document types.
        $documentTypeIds = DocumentType::where('company_id', $companyId)
            ->whereIn('dt_name', $requiredDocs)
            ->pluck('id')
            ->all();

        $allDocuments = collect();
        $validDocuments = collect();
        $expiringSoonDocuments = collect();
        $expiredDocuments = collect();
        $missingRows = collect();

        // If we are NOT in checklist mode, preload all tabs so switching tabs does not reload the page.
        if (empty($vehicleId)) {
            $baseQuery = LegalDocument::with(['vehicle', 'documentType'])
                ->where('company_id', $companyId)
                ->whereIn('document_type_id', $documentTypeIds);

            $today = now()->toDateString();
            $soon = now()->addDays(7)->toDateString();

            $allDocuments = (clone $baseQuery)
                ->orderBy('expire_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            $expiredDocuments = (clone $baseQuery)
                ->whereDate('expire_date', '<', $today)
                ->orderBy('expire_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            $expiringSoonDocuments = (clone $baseQuery)
                ->whereNotNull('expire_date')
                ->whereDate('expire_date', '>=', $today)
                ->whereDate('expire_date', '<=', $soon)
                ->orderBy('expire_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            $validDocuments = (clone $baseQuery)
                ->where(function ($sub) use ($soon) {
                    $sub->whereNull('expire_date')
                        ->orWhereDate('expire_date', '>', $soon);
                })
                ->orderBy('expire_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Missing tab: show every required document that is absent per vehicle.
            $allRequiredDocs = LegalDocument::with(['documentType'])
                ->where('company_id', $companyId)
                ->whereIn('document_type_id', $documentTypeIds)
                ->orderBy('created_at')
                ->get()
                ->groupBy('vehicle_id');

            foreach ($vehicles as $vehicle) {
                $byTypeName = $allRequiredDocs->get($vehicle->id, collect())
                    ->keyBy(fn ($doc) => $doc->documentType?->dt_name);

                foreach ($requiredDocs as $docTypeName) {
                    if (!$byTypeName->get($docTypeName)) {
                        $missingRows->push([
                            'vehicle' => $vehicle,
                            'documentTypeName' => $docTypeName,
                        ]);
                    }
                }
            }
        }

        $docsByTypeName = collect();
        $selectedVehicle = null;
        if (!empty($vehicleId)) {
            $selectedVehicle = $vehicles->firstWhere('id', (int) $vehicleId);
            $vehicleDocs = LegalDocument::with(['documentType'])
                ->where('company_id', $companyId)
                ->where('vehicle_id', $vehicleId)
                ->whereIn('document_type_id', $documentTypeIds)
                ->orderBy('created_at')
                ->get();

            // Keep the latest document per required type.
            $docsByTypeName = $vehicleDocs->keyBy(fn ($doc) => $doc->documentType?->dt_name);
        }

        return view('vms.legal-documents.status', compact(
            'page',
            'vehicleId',
            'requiredDocs',
            'docsByTypeName',
            'missingRows',
            'status',
            'selectedVehicle',
            'allDocuments',
            'validDocuments',
            'expiringSoonDocuments',
            'expiredDocuments'
        ));
    }

    public function index(Request $request)
    {
        $companyId = Session::get('company_id');
        $now = Carbon::now(); 
        $start = $now->copy()->startOfMonth()->format('Y-m-d');
        $end = \Carbon\Carbon::now()->format('Y-m-d');
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $this->ensureVehicleDocumentTypes($companyId);

        $docName = $request->get('doc_name');

        $q = trim((string) $request->get('q', ''));
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(5, min(100, $perPage));

        $tabNames = LegalDocument::REQUIRED_VEHICLE_DOCS;
        //dd($tabNames);

        $baseQuery = LegalDocument::with(['vehicle', 'documentType'])
            ->where('company_id', $companyId);

        if (!empty($q)) {
            $baseQuery->where(function ($sub) use ($q) {
                $sub->whereHas('vehicle', function ($v) use ($q) {
                    $v->where('plate_no', 'like', '%' . $q . '%');
                })->orWhereHas('documentType', function ($d) use ($q) {
                    $d->where('dt_name', 'like', '%' . $q . '%');
                });
            });
        }

        $order = fn ($query) => $query
            ->orderBy('expire_date', 'asc')
            ->orderBy('created_at', 'desc');

        $allDocuments = $order(clone $baseQuery)->get();

        $documentsByTab = collect();
        foreach ($tabNames as $name) {
            $documentsByTab[$name] = $order(
                (clone $baseQuery)->whereHas('documentType', function ($qq) use ($name) {
                    $qq->where('dt_name', $name);
                })
            )->get();
        }
        $page = 'Legal Documents (Tanzania)';

        return view('vms.legal-documents.index', compact('page','start_date','end_date','is_post_query','tabNames','docName','q','perPage', 'allDocuments', 'documentsByTab' ));
    }

    public function create()
    {
        $companyId = Session::get('company_id');

        $this->ensureVehicleDocumentTypes($companyId);

        $docName = request('doc_name');
        $vehicleId = request('vehicle_id');

        $vehicles = Vehicle::where('company_id', $companyId)->get();

        $documentTypes = DocumentType::where('company_id', $companyId)
            ->whereIn('dt_name', LegalDocument::REQUIRED_VEHICLE_DOCS)
            ->orderBy('dt_name')
            ->get();

        $selectedDocumentTypeId = null;
        if ($docName) {
            $selectedDocumentTypeId = $documentTypes->firstWhere('dt_name', $docName)?->id;
        }

        $page = 'Add Legal Document';

        return view('vms.legal-documents.create', compact(
            'page',
            'vehicles',
            'documentTypes',
            'selectedDocumentTypeId',
            'vehicleId',
            'docName'
        ));
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');

        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'charge_paid' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'doc_attachment' => 'required|file|mimes:pdf|max:5120',
        ]);

        $documentType = DocumentType::findOrFail($request->document_type_id);

        $path = $request->file('doc_attachment')->store('documents', 'local');

        LegalDocument::create([
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'vehicle_id' => $request->vehicle_id,
            'document_type_id' => $request->document_type_id,
            'last_issue_date' => $request->issue_date,
            'expire_date' => $request->expiry_date,
            'charge_paid' => $request->charge_paid ?? 0,
            'commission' => $request->commission ?? 0,
            'doc_attachment' => $path,
        ]);

        return redirect()->route('legal-documents.index', [
            'doc_name' => $documentType->dt_name,
            'vehicle_id' => $request->vehicle_id,
        ])->with('success', 'Document added.');
    }

    public function show(string $id)
    {
        $document = LegalDocument::with(['vehicle', 'documentType'])->findOrFail(decrypt($id));
        $page = 'Document Details';
        return view('vms.legal-documents.show', compact('page', 'document'));
    }

    public function edit(string $id)
    {
        $companyId = Session::get('company_id');
        $document = LegalDocument::with(['vehicle', 'documentType'])->findOrFail(decrypt($id));
        $vehicles = Vehicle::where('company_id', $companyId)->get();
        $documentTypes = DocumentType::where('company_id', $companyId)
            ->whereIn('dt_name', LegalDocument::REQUIRED_VEHICLE_DOCS)
            ->orderBy('dt_name')
            ->get();
        $page = 'Edit Document';
        return view('vms.legal-documents.edit', compact('page', 'document', 'vehicles', 'documentTypes'));
    }

    public function update(Request $request, string $id)
    {
        $document = LegalDocument::findOrFail(decrypt($id));
        $companyId = Session::get('company_id');

        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'charge_paid' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'doc_attachment' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $documentType = DocumentType::findOrFail($request->document_type_id);

        $data = [
            'document_type_id' => $request->document_type_id,
            'vehicle_id' => $request->vehicle_id,
            'last_issue_date' => $request->issue_date,
            'expire_date' => $request->expiry_date,
            'charge_paid' => $request->charge_paid ?? 0,
            'commission' => $request->commission ?? 0,
        ];

        if ($request->hasFile('doc_attachment')) {
            if (Storage::disk('local')->exists($document->doc_attachment)) {
                Storage::disk('local')->delete($document->doc_attachment);
            }
            $data['doc_attachment'] = $request->file('doc_attachment')->store('documents', 'local');
        }

        $document->update($data);

        return redirect()->route('legal-documents.index', [
            'doc_name' => $documentType->dt_name,
            'vehicle_id' => $request->vehicle_id,
        ])->with('success', 'Document updated.');
    }

    public function destroy(string $id)
    {
        $document = LegalDocument::with('documentType')->findOrFail(decrypt($id));

        if (Storage::disk('local')->exists($document->doc_attachment)) {
            Storage::disk('local')->delete($document->doc_attachment);
        }

        $docName = $document->documentType?->dt_name;
        $vehicleId = $document->vehicle_id;

        $document->delete();

        return redirect()->route('legal-documents.index', array_filter([
            'doc_name' => $docName,
            'vehicle_id' => $vehicleId,
        ]))->with('success', 'Document deleted.');
    }

    public function download(Request $request, string $id)
    {
        $document = LegalDocument::with('documentType')->findOrFail(decrypt($id));

        if (!Storage::disk('local')->exists($document->doc_attachment)) abort(404);

        // Guard in case the audit-log table is not migrated yet.
        if (Schema::hasTable('document_access_logs')) {
            DocumentAccessLog::create([
                'user_id' => Auth::id(),
                'document_type' => 'document',
                'document_id' => $document->id,
                'action' => 'download',
                'ip_address' => $request->ip(),
            ]);
        }

        $fileBase = Str::slug($document->documentType?->dt_name ?? 'legal-document');
        return Storage::disk('local')->download($document->doc_attachment, $fileBase . '.pdf');
    }
}
