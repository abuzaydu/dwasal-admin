<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\HourMeter;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

class HourMeterController extends Controller
{
    public function store(Request $request)
    {
        $company_id = session('company_id');
        $shop_id = session('shop_id');
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'date' => 'required|date',
            'start_hr' => 'required|numeric|min:0',
            'end_hr' => 'required|numeric|min:0',
        ]);

        $device = Device::find($request->device_id);
        $hourMeter = $device->hourMeters()->create([
            'start_hr' => $request->start_hr,
            'end_hr' => $request->end_hr,
            'date' => $request->date,
            'company_id' => $company_id,
            'shop_id' => $shop_id,
        ]);

        return redirect()->back()->with('success', 'Hour Meter added successfully');
    }

    public function destroy($id)
    {
        $hourMeter = HourMeter::findOrFail($id);
        $hourMeter->delete();

        return redirect()->back()->with('success', 'Hour Meter deleted successfully');
    }

    public function edit($id)
    {
        $hourMeter = HourMeter::findOrFail($id);
        return response()->json($hourMeter);
    }

    public function update(Request $request, $hour_meter)
    {
        try {
            $decryptedId = decrypt($hour_meter);
        } catch (DecryptException $e) {
            abort(404, 'Invalid ID mapping.');
        }
        $record = HourMeter::findOrFail($decryptedId);
        $request->validate([
            'date' => 'required|date',
            'start_hr' => 'required|numeric|min:0',
            'end_hr' => 'required|numeric|min:0',
            'device_id' => 'required|exists:devices,id',
        ]);

        $record->update([
            'start_hr' => $request->start_hr,
            'end_hr' => $request->end_hr,
            'date' => $request->date,
            'device_id' => $request->device_id,
        ]);

        return redirect()->back()->with('success', 'Hour Meter updated successfully');
    }
}

