<?php

namespace App\Http\Controllers\AppAPI;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\ChekInNotification;
use App\Notifications\NewVisitorRegisteredNotification;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisitorsController extends Controller
{
    
    public function index(Request $request)
    {
        $visitors = Visitor::where('visitors.shop_id', $request['shop_id'])->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitors.visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname', 'visitors.came_in_with', 'visitors.came_out_with')
        ->orderBy('visitors.created_at', 'desc')
        ->get();

        $visitors->transform(function ($visitor) {
            $visitor->visitor_photo_url = $this->buildVisitorPhotoUrl($visitor->visitor_photo ?? null);
            return $visitor;
        });

        return response()->json($visitors);
    }

    public function create(Request $request)
    {
        $company = Company::find($request['company_id']);
        $departments = Department::where('company_id', $request['company_id'])->select('id', 'name')->get();
        $comemployees = $company->users()->select('id', 'first_name as fname', 'last_name as lname')->get();
        $employees = [['id' => 0, 'name' => 'Select Host ']];
        foreach ($comemployees as $key => $value) {
            array_push($employees, ['id' => $value->id, 'name' => $value->fname.' '.$value->lname]);
        }
        // Log::info($employees);
        return response()->json(['statuscode' => 200, 'employees' => $employees, 'departments' => $departments]);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'shop_id'        => 'required|exists:shops,id',
            'user_id'        => 'required|exists:users,id',
            'host_id'        => 'required|exists:users,id',
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:20',
            'email'          => 'nullable|email',
            'address'        => 'required|string',
            'id_type'        => 'required|string',
            'id_number'      => 'required|string',
            // 'badge_no'       => 'required|exists:badges,badge_number',
            'purpose'        => 'required|string',
            'came_in_with'   => 'nullable|string',
            'came_out_with'  => 'nullable|string',
        ]);

        $visitor = Visitor::where('shop_id', $request['shop_id'])
            ->where('mobile', $request['mobile'])
            ->whereNull('time_out')
            ->first();

        if (is_null($visitor)) {
            $visitor = new Visitor();
            $visitor->shop_id       = $request['shop_id'];
            $visitor->user_id       = $request['user_id'];
            $visitor->host_id       = $request['host_id'];
            $visitor->name          = $request['name'];
            $visitor->mobile        = $request['mobile'];
            $visitor->email         = $request['email'];
            $visitor->address       = $request['address'];
            $visitor->id_type       = $request['id_type'];
            $visitor->id_number     = $request['id_number'];
            // $visitor->badge_no      = $request['badge_no'];
            $visitor->purpose       = $request['purpose'];
            $visitor->status        = 'Awaiting Host permission';
            $visitor->came_in_with  = $request['came_in_with'] ?? null;
            $visitor->came_out_with = $request['came_out_with'] ?? null;
            $visitor->save();

            Badge::where('badge_number', $request['badge_no'])
                ->update(['status' => 'in_use']);

        }

        return response()->json([
            'statusCode' => 200,
            'visitor'    => $visitor,
            'message'    => 'Visitor saved successfully'
        ]);
    }

    public function visitorPhoto(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'photo'      => 'required|file|mimes:jpeg,png|max:5120',
        ]);

        $visitor = Visitor::find($request['visitor_id']);

        if (!is_null($visitor)) {
            $location = null;
            $dir = 'visitors';
            $current = $visitor->visitor_photo;

            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {

                    $old_photo_path = $dir . '/' . $current;
                    if (Storage::disk('public')->exists($old_photo_path)) {
                        Storage::disk('public')->delete($old_photo_path);
                    }

                    $extension = $request->photo->extension();
                    $filename  = $visitor->id . '.' . $extension;

                    $request->photo->storeAs($dir, $filename, 'public');
                    $location = $filename;
                }
            } else {
                $location = $current;
            }

            $visitor->visitor_photo = $location;
            $visitor->save();

            // Notify the host in Laravel (web notification) after photo upload succeeds.
            $host = User::find($visitor->host_id);
            if ($host) {
                $host->notify(new NewVisitorRegisteredNotification($visitor));
            }

            return response()->json([
                'statusCode' => 200,
                'visitor'    => $visitor,
                'message'    => 'Visitor Photo added successfully'
            ]);

        } else {
            return response()->json([
                'statusCode' => 400,
                'message'    => 'Visitor not found'
            ]);
        }
    }

   public function Vcheckinwithbadge(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'qr_data'    => 'required|string',
        ]);

        try {
            $data = decrypt($request['qr_data']);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'Invalid QR code.',
            ], 422);
        }

        $badg = explode('&', $data, 2);

        $bdge = Badge::find($badg[0]);

        if (!$bdge) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Invalid badge.',
            ], 404);
        }

        if ($bdge->badge_number !== $badg[1]) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'Badge number mismatch.',
            ], 422);
        }

        $visitor = Visitor::find($request['visitor_id']);

        if (!$visitor) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Visitor not found',
            ]);
        }

        if ($visitor->status === 'Checked In') {

            if ($visitor->badge_no !== $bdge->badge_number) {
                return response()->json([
                    'statusCode' => 403,
                    'message' => 'Badge mismatch. Please scan the badge used during check-in.',
                ], 403);
            }

            $visitor->status = 'Checked Out';
            $visitor->time_out = Carbon::now();
            $visitor->save();

            $bdge->status = 'available';
            $bdge->save();

            return response()->json([
                'statusCode' => 200,
                'visitor' => $visitor,
                'message' => 'Visitor Checked Out successfully'
            ]);
        }

        if ($bdge->status === 'in_use') {
            return response()->json([
                'statusCode' => 409,
                'message' => 'Badge is already in use.',
            ], 409);
        }

        $visitor->status = 'Checked In';
        $visitor->time_in = Carbon::now();
        $visitor->badge_no = $bdge->badge_number;
        $visitor->save();

        $bdge->status = 'in_use';
        $bdge->save();

        $host = User::find($visitor->host_id);

        if ($host) {
            $host->notify(new ChekInNotification($visitor));
        }

        return response()->json([
            'statusCode' => 200,
            'visitor' => $visitor,
            'message' => 'Visitor Checked In successfully'
        ]);
    }

    public function visitorCheckIn(Request $request)
    {
        $visitor = Visitor::find($request['visitor_id']);
        if(!is_null($visitor)){
            $visitor->status = 'Checked In';
            $visitor->time_in = Carbon::now();
            $visitor->save();
            Badge::where('badge_number', $visitor->badge_no)
            ->update(['status' => 'in_use']);
            
            return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Checked In successfully']);

        }else{
            return response()->json(['statuscode' => 400, 'message' => 'Visitor not found']);
        }
    }

    public function visitorCheckOut(Request $request)
    {
        $visitor = Visitor::find($request['visitor_id']);
        if(!is_null($visitor)){
            $visitor->status = 'Checked Out';
            $visitor->time_out = Carbon::now();
            $visitor->save();
            Badge::where('badge_number', $visitor->badge_no)
            ->update(['status' => 'available']);
            return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Checked Out successfully']);

        }else{
            return response()->json(['statuscode' => 400, 'message' => 'Visitor not found']);
        }
    }

    public function myVisitors(Request $request)
    {
        $visitors = Visitor::where('host_id', $request['host_id'])->where('visitors.shop_id', $request['shop_id'])->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname')->get();
            // Log::info($visitors);
        return response()->json($visitors);
    }

    public function getAvailableBadges(Request $request)
    {
        $companyId = $request->company_id;
        $company = Company::find($companyId);
        $badges = $company->badges()->where('status', 'available')->get();
        
        return response()->json([
            'badges' => $badges
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $visitor = Visitor::where('visitors.id', $id)
            ->leftJoin('users', 'users.id', '=', 'visitors.host_id')
            ->select(
                'visitors.id as id',
                'visitors.name as name',
                'visitors.mobile as mobile',
                'visitors.email as email',
                'visitors.address as address',
                'visitors.id_type',
                'visitors.id_number',
                'visitors.visitor_photo',
                'visitors.badge_no',
                'visitors.purpose',
                'visitors.time_in',
                'visitors.time_out',
                'visitors.status',
                'users.first_name as fname',
                'users.last_name as lname',
                'visitors.came_in_with',
                'visitors.came_out_with',
                'visitors.created_at'
            )
            ->first();

        if (is_null($visitor)) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Visitor not found',
            ], 404);
        }

        $visitor->visitor_photo_url = $this->buildVisitorPhotoUrl($visitor->visitor_photo ?? null);

        return response()->json($visitor);
    }

    public function photo(string $filename): BinaryFileResponse
    {
        $safeFilename = basename($filename);
        $path = 'visitors/' . $safeFilename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $absolutePath = Storage::disk('public')->path($path);
        return response()->file($absolutePath);
    }

    private function buildVisitorPhotoUrl(?string $photo): string
    {
        if (empty($photo)) {
            return asset('assets/img/user-icon.webp');
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        $filename = basename($photo);
        $base = request()->getSchemeAndHttpHost();
        return $base . '/api/visitor-photo-file/' . rawurlencode($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
