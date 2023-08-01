<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNewChannelRequest;
use App\Http\Requests\CreateNewEventRequest;
use App\Http\Requests\CreateNewRoomRequest;
use App\Http\Requests\CreateNewSessionRequest;
use App\Http\Requests\CreateNewTicketRequest;
use App\Http\Requests\EditEventRequest;
use App\Http\Requests\EditSessionRequest;
use App\Models\Channel;
use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Registration;
use App\Models\Room;
use App\Models\Session;
use App\Models\SessionRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class EventsController extends Controller
{
    public function index() {
        $organizer = auth()->user();
        $events = Event::query()->with(['tickets', 'tickets.registrations'])->where('organizer_id', $organizer->id)->get();

        return view('events', [
            'events' => $events
        ]);
    }

    public function create(CreateNewEventRequest $request) {
        $name = $request->input('name');
        $slug = $request->input('slug');
        $date = $request->input('date');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $request->rules(), $request->messages());

        $eventWithSlug = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('slug', $slug)->first();

        if($eventWithSlug != null) {
            $validator->messages()->add('slug', 'Slug is already used');
            return redirect('/events/create')->withErrors($validator)->withInput($request->all());
        }

        $event = new Event();
        $event->organizer_id = auth()->user()->id;
        $event->slug = $slug;
        $event->name = $name;
        $event->date = $date;
        $event->save();

        return redirect('/event/' . $event->id)->with('success', 'Event successfully created');
    }

    public function one(int $id) {
        $organizer = auth()->user();
        $event = Event::query()
            ->with(['tickets'])
            ->where('organizer_id', $organizer->id)->where('id', $id)->first();

        if($event == null) {
            return redirect('/');
        }

        $channels = Channel::query()->with(['rooms'])->where('event_id', $event->id)->get();
        $rooms = Room::query()->where('channel_id', $channels->map(function($i) {
            return $i->id;
        })->all())->get();
        $sessions = Session::query()->where('room_id', $rooms->map(function($i) {
            return $i->id;
        })->all())->get();

        return view('event', [
            'event' => $event,
            'sessions' => $sessions,
            'channels' => $channels,
            'rooms' => $rooms,
            'tickets' => $event->tickets
        ]);
    }

    public function editEventView(Request $request, int $id) {
        $organizer = auth()->user();
        $event = Event::query()->where('organizer_id', $organizer->id)->where('id', $id)->first();

        if($event == null) {
            return redirect('/');
        }

        return view('edit-event', [
            'event' => $event,
            'name' => $event->name,
            'slug' => $event->slug,
            'date' => $event->date
        ]);
    }

    public function editEvent(EditEventRequest $request, int $id) {
        $name = $request->input('name');
        $slug = $request->input('slug');
        $date = $request->input('date');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $request->rules(), $request->messages());

        $eventWithSlug = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('slug', $slug)->first();

        if($eventWithSlug != null && $eventWithSlug->id != $id) {
            $validator->messages()->add('slug', 'Slug is already used');
            return redirect('/event/' . $id . '/edit')->withErrors($validator)->withInput($request->all());
        }

        $event = Event::query()->where('id', $id)->where('organizer_id', auth()->user()->id)->first();

        if($event == null) {
            return redirect('/');
        }

        $event->organizer_id = auth()->user()->id;
        $event->slug = $slug;
        $event->name = $name;
        $event->date = $date;
        $event->save();

        return redirect('/event/' . $event->id)->with('success', 'Event successfully updated');
    }

    public function newTicket(CreateNewTicketRequest $request, int $id) {
        $name = $request->input('name');
        $cost = $request->input('cost');
        $specialValidity = $request->input('special_validity');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(),
            $specialValidity == 'amount' ? [
                'amount' => 'required'
            ] : [
                'valid_until' => 'required'
            ]);

        try {
            $validator->validate();

            $ticket = new EventTicket();
            $ticket->name = $name;
            $ticket->cost = $cost;
            $ticket->event_id = $id;

            switch($specialValidity) {
                case 'amount':
                    $ticket->special_validity = json_encode([
                        'type' => 'amount',
                        'amount' => intval($request->input('amount'))
                    ]);
                    break;
                case 'date':
                    $ticket->special_validity = json_encode([
                        'type' => 'date',
                        'date' => $request->input('valid_until')
                    ]);
                    break;
            }

            $ticket->save();
            return redirect('/event/' . $id)->with('success', 'Ticket successfully created');
        } catch(ValidationException $e) {
            return redirect('/event/' . $id . '/ticket')->withInput($request->input())->withErrors($e->validator);
        }
    }

    public function newSessionView(int $id) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        $channels = Channel::query()->with(['rooms'])->where('event_id', $id)->get();

        $rooms = [];

        foreach($channels as $channel) {
            foreach($channel->rooms as $room) {
                $room->channel = $channel;
                $rooms[] = $room;
            }
        }

        return view('create-new-session', [
            'rooms' => $rooms,
            'event' => $event
        ]);
    }

    public function newTicketView(int $id) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        return view('create-new-ticket', [
            'event' => $event,
        ]);
    }

    public function newChannelView(int $id) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        return view('create-new-channel', [
            'event' => $event,
        ]);
    }


    public function newRoomView(int $id) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        $channels = Channel::query()->where('event_id', $id)->get();
        return view('create-new-room', [
            'event' => $event,
            'channels' => $channels
        ]);
    }

    public function editSessionView(int $id, int $sessionID) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        $session = Session::query()->where('id', $sessionID)->first();

        if($session == null) {
            return redirect('/event/' . $id);
        }

        $channels = Channel::query()->with(['rooms'])->where('event_id', $id)->get();
        $rooms = [];

        foreach($channels as $channel) {
            foreach($channel->rooms as $room) {
                $room->channel = $channel;
                $rooms[] = $room;
            }
        }

        return view('edit-session', [
            'event' => $event,
            'rooms' => $rooms,
            'session' => $session
        ]);
    }

    public function newSession(CreateNewSessionRequest $request, int $id) {
        $type = $request->input('type');
        $title = $request->input('title');
        $description = $request->input('description');
        $speaker = $request->input('speaker');
        $room = $request->input('room');
        $cost = $request->input('cost', 0);
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $existSession = Session::query()
            ->where('start', '>=', $startDate)->where('end', '<=', $endDate)
            ->first();

        if($existSession != null) {
            return back()->withInput($request->input())->with('error', 'Room already booked during this time');
        }

        if(strtotime($startDate) > strtotime($endDate)) {
            return back()->withInput($request->input())->with('error', 'Start date is after end date');
        }

        $session = new Session();
        $session->type = $type;
        $session->title = $title;
        $session->speaker = $speaker;
        $session->room_id = $room;

        if($type !== 'talk') {
            $session->cost = $cost;
        }

        $session->start = $startDate;
        $session->end = $endDate;

        $session->description = $description;
        $session->save();
        return redirect('/event/' . $id)->with('success', 'Session successfully created');
    }

    public function editSession(EditSessionRequest $request, int $id, int $sessionID) {
        $type = $request->input('type');
        $title = $request->input('title');
        $description = $request->input('description');
        $speaker = $request->input('speaker');
        $room = $request->input('room');
        $cost = $request->input('cost', 0);
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $session = Session::query()->where('id', $sessionID)->first();

        $existSessionInDateRange = Session::query()
            ->where('start', '>=', $startDate)->where('end', '<=', $endDate)
            ->where('id', '!=', $sessionID)
            ->first();

        if($existSessionInDateRange != null) {
            return back()->withInput($request->input())->with('error', 'Room already booked during this time');
        }

        if(strtotime($startDate) > strtotime($endDate)) {
            return back()->withInput($request->input())->with('error', 'Start date is after end date');
        }

        $session->type = $type;
        $session->title = $title;
        $session->speaker = $speaker;
        $session->room_id = $room;

        if($type !== 'talk') {
            $session->cost = $cost;
        }

        $session->start = $startDate;
        $session->end = $endDate;

        $session->description = $description;
        $session->save();
        return redirect('/event/' . $id)->with('success', 'Session successfully updated');
    }

    public function newChannel(CreateNewChannelRequest $request, int $id) {
        $name = $request->input('name');

        $channel = new Channel();
        $channel->name = $name;
        $channel->event_id = $id;
        $channel->save();

        return redirect('/event/' . $id)->with('success', 'Channel successfully created');
    }

    public function newRoom(CreateNewRoomRequest $request, int $id) {
        $name = $request->input('name');
        $channelID = $request->input('channel');
        $capacity = $request->input('capacity');

        $room = new Room();
        $room->name = $name;
        $room->channel_id = $channelID;
        $room->capacity = $capacity;
        $room->save();

        return redirect('/event/' . $id)->with('success', 'Room successfully created');
    }

    public function roomCapacity(int $id) {
        $event = Event::query()
            ->where('organizer_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if($event == null) {
            return redirect('/');
        }

        $channels = Channel::query()->where('event_id', $event->id)->get();

        $tickets = EventTicket::query()->where('event_id', $event->id)->get();
        $registrations = Registration::query()->whereIn('ticket_id', $tickets->map(function($i) { return $i->id; })->all())->get();
        $sessionRegistrations = SessionRegistration::query()->whereIn(
            'registration_id', $registrations->map(function($i) { return $i->id; })->all()
        )->get();
        $rooms = Room::query()->whereIn('channel_id', $channels->map(function($i) { return $i->id; })->all())->get();
        $sessions = Session::query()->whereIn('room_id', $rooms->map(function($i) { return $i->id; })->all())->get();

        $statistics = [];

        foreach($sessions as $session) {
            // attendee, capacity
            $statistics[$session->title] = [0, 0];

            foreach($registrations as $registration) {
                $sr = null;

                foreach($sessionRegistrations as $r) {
                    if($registration->id == $r->registration_id && $r->session_id == $session->id) {
                        $sr = $r;
                        break;
                    }
                }

                if(!$sr) {
                    continue;
                }


                $statistics[$session->title][0] += 1;
            }

            $ro = null;

            foreach($rooms as $room) {
                if($room->id == $session->room_id) {
                    $ro = $room;
                    break;
                }
            }

            if($ro != null) {
                $statistics[$session->title][1] = $ro->capacity;
            }
        }

        return view('event-room-capacity', [
            'event' => $event,
            'statistics' => $statistics
        ]);
    }
}
