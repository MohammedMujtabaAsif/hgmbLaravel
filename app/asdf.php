<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Event;
use DB;

class EventsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except'=> ['index', 'show', 'email']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return the events page sorted from newest to oldest (default)
        $events = Event::orderBy('created_at', 'dsc')->paginate(2);
        return view('events.index')->with('events', $events);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Check value of sort option
        if($request->has('event_sort')){
            //if date option is selected, sort from newest to oldest (default)
            if($request->input('event_sort')=='date'){
                $events = Event::orderBy('created_at', 'dsc')->paginate(2);
            }
            //if name option is selected, sort alphabetically from a-z
            elseif($request->input('event_sort')=='name'){
                $events = Event::orderBy('title', 'asc')->paginate(2);
            }
            //if likes option is selected, sort from highest liked to lowest
            elseif($request->input('event_sort')=='likes'){
                $events = Event::orderBy('event_like_counter', 'dsc')->paginate(2);
            }
            //return the view with the corresponding list of events
                return view('events.index')->with('events', $events);
        }
        //check that the request was to like a post
        if($request->has('like_event')){

            //find the event that is being liked
            $event = $request->input('like_event');
            $selectedEvent = Event::find($event);

            //add a like to the likes database
            $selectedEvent->likes()->create([
                'user_id' => Auth::user()->id,
                'event_id' => $event,
            ]);
            
            //find out how many likes that event has
            $like_count = \App\EventLike::where('event_id', $selectedEvent->id)->count();
            //update and save the event_like_counter field for that event
            $selectedEvent->event_like_counter = $like_count;
            $selectedEvent->save();
            //redirect back to page they were on
            return redirect()->back()->with('success', 'Event Liked Successfully');

        }
        else{

            //Ensure entered data is suitable
            $this->validate($request,
            [
                'eventTitle' => 'required',
                'eventBody' => 'required',
                'eventLocation' => 'required',
                'eventImage' => 'image|nullable|max:1999'
            ]);

            //Manage Event Image Upload
            if($request->hasFile('eventImage')){
                //Get file name with extension
                $imageNameWithExt = $request->file('eventImage')->getClientOriginalName();
                //Get file name without extension
                $imageName = pathinfo($imageNameWithExt, PATHINFO_FILENAME);
                //Get extension of image file
                $imageExtension = $request->file('eventImage')->getClientOriginalExtension();
                //Set up the unique name for stored image
                $storedFileName = $imageName.'_'.time().'.'.$imageExtension;
                //Upload image to set location
                $path = $request->file('eventImage')->storeAs('public/eventImages', $storedFileName);
            }else{
                $storedFileName = 'defaultImage.jpg';
            }


            //Get data for start date and set up start date variable
            $startDay = $request->input('eventStartDay');
            $startMonth = $request->input('eventStartMonth');
            $startYear = $request->input('eventStartYear');
            $startHour = $request->input('eventStartHour');
            $startMinute = $request->input('eventStartMinute');
            $startDateString = $startYear.'-'.$startMonth.'-'.$startDay.' '.$startHour.':'.$startMinute;
            $startDate = date("Y-m-d H:i",strtotime($startDateString));

            //Get data for end date and set up end date variable
            $endDay = $request->input('eventEndDay');
            $endMonth = $request->input('eventEndMonth');
            $endYear = $request->input('eventEndYear');
            $endHour = $request->input('eventEndHour');
            $endMinute = $request->input('eventEndMinute');
            $endDateString = $endYear.'-'.$endMonth.'-'.$endDay.' '.$endHour.':'.$endMinute;
            $endDate = date("Y-m-d H:i",strtotime($endDateString));


            //Create new event in Events database
            $event = new Event;
            $event->title = $request->input('eventTitle');
            $event->body = $request->input('eventBody');
            $event->event_start = $startDate;
            $event->event_end = $endDate;
            $event->event_location = $request->input('eventLocation');
            $event->event_type = $request->input('eventType');
            $event->user_id = auth()->user()->id;
            $event->event_image = $storedFileName;
            //find out how many likes that event has
            $like_count =0;
            //update and save the event_like_counter field for that event
            $event->event_like_counter = $like_count;
            $event->save();

            //redirect to the events page
            return redirect('/events')->with('success', 'Event Posted Successfully');
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
        $event = Event::find($id);
        return view('events.show')->with('event', $event);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::find($id);
        //Check user id matches author id
        if(auth()->user()->id!==$event->user_id){
            return redirect('/events')->with('error', 'You are not the author');
        }
        return view('events.edit')->with('event', $event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        //Ensure entered data is suitable
        $this->validate($request,
        [
            'eventTitle' => 'required',
            'eventBody' => 'required',
            'eventLocation' => 'required',
            'eventImage' => 'image|nullable|max:1999'
        ]);

        //Manage Event Image Upload
        if ($request->hasFile('eventImage')) {
        //Get file name with extension
        $imageNameWithExt = $request->file('eventImage')->getClientOriginalName();
        //Get file name without extension
        $imageName = pathinfo($imageNameWithExt, PATHINFO_FILENAME);
        //Get extension of image file
        $imageExtension = $request->file('eventImage')->getClientOriginalExtension();
        //Set up the unique name for stored image
        $storedFileName = $imageName.'_'.time().'.'.$imageExtension;
        //Upload image to set location
        $path = $request->file('eventImage')->storeAs('public/eventImages', $storedFileName);
        }

        //Get data for start date and set up start date variable
        $startDay = $request->input('eventStartDay');
        $startMonth = $request->input('eventStartMonth');
        $startYear = $request->input('eventStartYear');
        $startHour = $request->input('eventStartHour');
        $startMinute = $request->input('eventStartMinute');
        $startDateString = $startYear.'-'.$startMonth.'-'.$startDay.' '.$startHour.':'.$startMinute;
        $startDate = date("Y-m-d H:i",strtotime($startDateString));

        //Get data for end date and set up end date variable
        $endDay = $request->input('eventEndDay');
        $endMonth = $request->input('eventEndMonth');
        $endYear = $request->input('eventEndYear');
        $endHour = $request->input('eventEndHour');
        $endMinute = $request->input('eventEndMinute');
        $endDateString = $endYear.'-'.$endMonth.'-'.$endDay.' '.$endHour.':'.$endMinute;
        $endDate = date("Y-m-d H:i",strtotime($endDateString));


        //Updating existing event in the Events database
        $event = Event::find($id);
        $event->title = $request->input('eventTitle');
        $event->body = $request->input('eventBody');
        $event->event_start = $startDate;
        $event->event_end = $endDate;
        $event->event_location = $request->input('eventLocation');
        $event->event_type = $request->input('eventType');
        $event->user_id = auth()->user()->id;

        if ($request->hasFile('eventImage')) {
            if ($event->event_image != 'default.png') {
                Storage::delete('public/eventImages/'.$event->event_image);
            }
            $event->event_image = $storedFileName;
        }

        $event->save();

        return redirect('/dash')->with('success', 'Event Updated Successfully');
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

        //Check user id matches author id
        if(auth()->user()->id !== $event->user_id){
            return redirect('/events')->with('error', 'You are not the author');
        }

        //Check the event has a custom image and remove it if it does
        if ($event->event_image != 'defaultImage.png') {
            Storage::delete('public/eventImages/'.$event->event_image);
        }

        //Delete the event from the database
        $event->delete();

        //Return to the dashboard
        return redirect('/dash')->with('success', 'Event Removed');
    }
}
?>