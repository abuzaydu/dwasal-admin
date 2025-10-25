<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Session;
use App\Models\Company;
use App\Models\User;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Holiday;
use App\Jobs\EventInviteJob;
use Log;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Events';
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $is_post_query = false;
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $company = Company::find(Session::get('company_id'));
        $users = $company->users()->select('users.id as id', 'first_name', 'last_name')->get();
        $events = Event::where('company_id', $company->id)->whereBetween('start', [$start, $end])->join('users', 'users.id', '=', 'events.user_id')->join('event_members', 'event_members.event_id', '=', 'events.id')->where('event_members.user_id', Auth::user()->id)->select('events.id as id', 'first_name', 'last_name', 'phone', 'email', 'title', 'location', 'category', 'start', 'end', 'ca_id')->get();
        return view('hr.events.index' , compact(['page', 'users', 'events', 'is_post_query', 'start_date', 'end_date']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $company = Company::find(Session::get('company_id'));
        $events = Event::where('company_id', $company->id)->join('users', 'users.id', '=', 'events.user_id')->join('event_members', 'event_members.event_id', '=', 'events.id')->where('event_members.user_id', Auth::user()->id)->select('events.id as id', 'first_name', 'last_name', 'phone', 'email', 'title', 'location', 'category', 'start', 'end', 'ca_id')->get();
        $eventArr = array();
        foreach ($events as $key => $event) {
            $emembers = EventMember::where('event_id', $event->id)->join('users', 'users.id', '=', 'event_members.user_id')->select('first_name', 'last_name')->get();
            $names = array();
            foreach ($emembers as $key => $value) {
                array_push($names, $value->first_name.' '.$value->last_name);
            }
            array_push($eventArr, ['id' => $event->id, 'author' => $event->first_name.' '.$event->last_name, 'phone' => $event->phone,'email' => $event->email, 'title' => $event->title, 'location' => $event->location, 'category' => $event->category, 'start' => $event->start, 'end' => $event->end, 'ca_id' => $event->ca_id,  'names' => $names]);
        }
        // Log::info(json_encode($eventArr));
        $holidays = Holiday::all();
        foreach($holidays as $value){
            array_push($eventArr, ['id' => $value->id, 'author' => 'Unknown', 'phone' => '','email' => '', 'title' => $value->name, 'location' => 'None', 'category' => 'allday', 'start' => $value->date, 'end' => $value->date, 'ca_id' => 3,  'names' => ['Al']]);
        }
        return json_encode($eventArr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $event = new Event();
        $event->company_id = $company->id;
        $event->user_id = Auth::user()->id;
        $event->title = $request['title'];
        $event->location = $request['location'];
        $event->category = $request['category'];
        $event->start = $request['start'];
        $event->end = $request['end'];
        $event->ca_id = $request['ca_id'];
        $event->body = $request['body'];
        $event->save();

        if (!empty($request->members)) {
            foreach ($request->members as $key => $user_id) {
                $emember = new EventMember();
                $emember->event_id = $event->id;
                $emember->user_id = $user_id;
                $emember->save();
            }

            dispatch(new EventInviteJob($request->members, $event));
            return redirect()->back()->with('success', 'Event Created successfuly');
        }else{
            $emember = new EventMember();
            $emember->event_id = $event->id;
            $emember->user_id = Auth::user()->id;
            $emember->save();
            return json_encode($event);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Event Details';
        $users = User::select('id', 'name')->get();
        $event = Event::where('events.id', decrypt($id))->join('users', 'users.id', '=', 'events.user_id')->join('event_members', 'event_members.event_id', '=', 'events.id')->where('event_members.user_id', Auth::user()->id)->select('events.id as id', 'name', 'phone', 'email', 'title', 'location', 'category', 'start', 'end', 'ca_id', 'events.created_at as created_at')->first();
    
        $emembers = EventMember::where('event_id', $event->id)->join('users', 'users.id', '=', 'event_members.user_id')->select('event_members.id as id', 'name')->get();


        return view('hr.events.show', compact('page', 'event', 'emembers', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request , $id)
    {
        $event = Event::find(decrypt($id));
        $event->title = $request['title'];
        $event->location = $request['location'];
        $event->category = $request['category'];
        $event->start = $request['start'];
        $event->end = $request['end'];
        $event->ca_id = $request['ca_id'];
        $event->body = $request['body'];
        $event->save();

        if (!empty($request->members)) {
            $memberIds = array();
            foreach ($request->members as $key => $user_id) {
                $emember = EventMember::where('user_id', $user_id)->where('event_id', $event->id)->first();
                if(is_null($emember)){
                    $emember = new EventMember();
                    $emember->event_id = $event->id;
                    $emember->user_id = $user_id;
                    $emember->save();
                    array_push($memberIds, $user_id);
                }
            }

            dispatch(new EventInviteJob($memberIds, $event));
        }
        return redirect()->back()->with('success', 'Event updated successfully');
    }

    public function removeParticipants(Request $request)
    {
        $event = Event::find($request['event_id']);
        if (!empty($request->members)) {
            foreach ($request->members as $key => $id) {
                $emember = EventMember::find($id);
                if(!is_null($emember) && $emember->user_id != $event->user_id){
                    $emember->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Event Participants removed successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateEvent(Request $request , $id)
    {
        $event = Event::find($id);
        if(!is_null($event)){
            $event->title =  $request->title == 'no' ?  $event->title: $request->title ;
            $event->location = $request->location == 'no' ? $event->location:$request->location;
            $event->category =  $request->category == 'no' ? $event->category : $request->category;
            if ($request->start != 'no') {
                $event->start = Carbon::parse($request->start)->addHours(3);
            }
            if ($request->end != 'no') {
                $event->end = Carbon::parse($request->end)->addHours(3);
            }
            $event->save();
            return json_encode($event);        
        }else{
            return json_encode('Event doesnt exist');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        if(!is_null($event)){
            $event->delete();

            return json_encode('success event deleted');
        }else{
            return json_encode('Event doesnt exist');
        }
        
    }
}
