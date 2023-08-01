<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\APILoginRequest;
use App\Http\Requests\APIRegisterRequest;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Organizer;
use App\Models\Registration;
use App\Models\SessionRegistration;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function events() {
        $events = Event::with(['organizer'])->get();

        return response()->json([
            'events' => $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'slug' => $event->slug,
                    'date' => $event->date,
                    'organizer' => [
                        'id' => $event->organizer->id,
                        'name' => $event->organizer->name,
                        'slug' => $event->organizer->slug
                    ]
                ];
            })
        ])->setStatusCode(200);
    }

    public function login(APILoginRequest $request) {
        $lastname = $request->input('lastname');
        $registrationCode = $request->input('registration_code');

        $attendee = Attendee::query()->where('lastname', $lastname)->where('registration_code', $registrationCode)->first();

        if(!$attendee) {
            return \response()->json([
                'message' => 'Invalid login'
            ], 401);
        }

        $attendee->login_token = md5($attendee->username);
        $attendee->save();

        return response()->json([
            'firstname' => $attendee->firstname,
            'lastname' => $attendee->lastname,
            'username' => $attendee->username,
            'email' => $attendee->email,
            'token' => $attendee->login_token
        ]);
    }

    public function logout() {
        $attendee = auth()->user();
        $attendee->login_token = '';
        $attendee->save();

        return \response()->json([
            'message' => 'Logout success'
        ]);
    }

    public function register(APIRegisterRequest $request) {
        $ticketID = $request->input('ticket_id');
        $ticket = EventTicket::query()->where('id', $request->input('ticket_id'))->first();

        if(!$ticket || !$ticket->isTicketAvailable()) {
            return \response()->json([
                'message' => 'Ticket is no longer available'
            ], 401);
        }

        $attendee = auth()->user();
        $existsRegistration = Registration::query()->where('attendee_id', $attendee->id)->where('ticket_id', $ticketID)->first();

        if($existsRegistration) {
            return \response()->json([
                'message' => 'User already registered'
            ], 401);
        }

        $registration = new Registration();
        $registration->attendee_id = $attendee->id;
        $registration->ticket_id = $ticketID;
        $registration->registration_time = now();
        $registration->save();

        foreach($request->input('session_ids') as $session_id) {
            $sr = new SessionRegistration();
            $sr->registration_id = $registration->id;
            $sr->session_id = $session_id;
            $sr->save();
        }

        return \response()->json([
            'message' => 'Registration successful'
        ]);
    }

    public function view(string $organizerSlug, string $eventSlug) {
        $organizer = Organizer::query()->where('slug', $organizerSlug)->first();

        if($organizer == null) {
            return \response()->json([
                'message' => 'Organizer not found'
            ], 404);
        }

        $event = Event::query()
            ->with(['channels.rooms.sessions', 'tickets'])
            ->where('organizer_id', $organizer->id)->where('slug', $eventSlug)->first();

        if($event == null) {
            return \response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'id' => $event->id,
            'name' => $event->name,
            'slug' => $event->slug,
            'data' => $event->date,
            'channels' => $event->channels->map(function($i) {
                return [
                    'id' => $i->id,
                    'name' => $i->name,
                    'rooms' => $i->rooms->map(function($r) {
                        return [
                            'id' => $r->id,
                            'name' => $r->name,
                            'sessions' => $r->sessions->map(function($s) {
                                return [
                                    'id' => $s->id,
                                    'title' => $s->title,
                                    'description' => $s->description,
                                    'speaker' => $s->speaker,
                                    'start' => $s->start,
                                    'end' => $s->end,
                                    'type' => $s->type,
                                    'cost' => $s->cost ?? null,
                                ];
                            })
                        ];
                    })
                ];
            }),
            'tickets' => $event->tickets->map(function($i) {
                return [
                    'id' => $i->id,
                    'name' => $i->name,
                    'description' => $i->specialValidity(),
                    'cost' => $i->cost,
                    'available' => $i->isTicketAvailable()
                ];
            })
        ]);
    }

    public function registrations() {
        $attendee = auth()->user();
        $registrations = Registration::query()->with(['ticket.event.organizer'])->where('attendee_id', $attendee->id)->get();

        return response()->json([
            'registrations' => $registrations->map(function($i) {
                return [
                    'event' => [
                        'id' => $i->ticket->event->id,
                        'name' => $i->ticket->event->name,
                        'slug' => $i->ticket->event->slug,
                        'date' => $i->ticket->event->date,
                        'organizer' => [
                            'id' => $i->ticket->event->organizer->id,
                            'name' => $i->ticket->event->organizer->name,
                            'slug' => $i->ticket->event->organizer->slug,
                        ],
                        'session_ids' => SessionRegistration::query()->where('registration_id', $i->id)->get()->map(function($i) { return $i->session_id; })
                    ],
                ];
            })
        ]);
    }
}
