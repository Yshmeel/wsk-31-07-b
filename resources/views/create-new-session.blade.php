@extends('layout')
@section('content')

    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/">Event Platform</a>
        <span class="navbar-organizer w-100">{{auth()->user()->name}}</span>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" id="logout" href="/signout">Sign out</a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="/">Manage Events</a></li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>{{$event->name}}</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link active" href="/event/{{$event->id}}">Overview</a></li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Reports</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item"><a class="nav-link" href="/event/{{$event->id}}/capacity">Room capacity</a></li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="border-bottom mb-3 pt-3 pb-2">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">{{$event->name}}</h1>
                    </div>
                    <span class="h6">{{$event->date}}</span>
                </div>

                <div class="mb-3 pt-3 pb-2">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h2 class="h4">Create new session</h2>
                    </div>
                </div>

                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{session()->get('error')}}
                    </div>
                @endif

                <form class="needs-validation" novalidate method="POST" action="/event/{{$event->id}}/session">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="selectType">Type</label>
                            <select class="form-control {{$errors->has('type') ? 'is-invalid' : ''}}" id="selectType" name="type">
                                <option value="talk" {{old('type') == 'talk' ? 'selected' : ''}}>Talk</option>
                                <option value="workshop"  {{old('type') == 'workshop' ? 'selected' : ''}}>Workshop</option>
                            </select>

                            @if($errors->has('type'))
                                <div class="invalid-feedback">
                                    {{$errors->first('type')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputTitle">Title</label>
                            <!-- adding the class is-invalid to the input, shows the invalid feedback below -->
                            <input type="text" class="form-control {{$errors->has('title') ? 'is-invalid' : ''}}" id="inputTitle" name="title"
                                   placeholder="" value="{{old('title')}}" />

                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{$errors->first('title')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputSpeaker">Speaker</label>
                            <input type="text" class="form-control {{$errors->has('speaker') ? 'is-invalid' : ''}}"
                                   id="inputSpeaker" name="speaker" placeholder="" value="{{old('speaker')}}">
                            @if($errors->has('speaker'))
                                <div class="invalid-feedback">
                                    {{$errors->first('speaker')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="selectRoom">Room</label>
                            <select class="form-control {{$errors->has('room') ? 'is-invalid' : ''}}" id="selectRoom" name="room">
                                @foreach($rooms as $room)
                                    <option value="{{$room->id}}"  {{ old('room') == $room->id ? 'selected' : '' }}>{{$room->name}} / {{$room->channel->name}}</option>
                                @endforeach
                            </select>

                            @if($errors->has('room'))
                                <div class="invalid-feedback">
                                    {{$errors->first('room')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputCost">Cost</label>
                            <input type="number" class="form-control {{$errors->has('cost') ? 'is-invalid' : ''}}"
                                   id="inputCost" name="cost" placeholder="" value="{{old('cost') ?? '0'}}">

                            @if($errors->has('cost'))
                                <div class="invalid-feedback">
                                    {{$errors->first('cost')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <label for="inputStart">Start</label>
                            <input type="text"
                                   class="form-control {{$errors->has('start') ? 'is-invalid' : ''}}"
                                   id="inputStart"
                                   name="start"
                                   placeholder="yyyy-mm-dd HH:MM"
                                   value="{{old('start')}}">

                            @if($errors->has('start'))
                                <div class="invalid-feedback">
                                    {{$errors->first('start')}}
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label for="inputEnd">End</label>
                            <input type="text"
                                   class="form-control {{$errors->has('end') ? 'is-invalid' : ''}}"
                                   id="inputEnd"
                                   name="end"
                                   placeholder="yyyy-mm-dd HH:MM"
                                   value="{{old('end')}}">

                            @if($errors->has('end'))
                                <div class="invalid-feedback">
                                    {{$errors->first('end')}}
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="textareaDescription">Description</label>
                            <textarea class="form-control" id="textareaDescription" name="description"
                                      placeholder="" rows="5">{{old('description')}}</textarea>

                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{$errors->first('description')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="mb-4">
                    <button class="btn btn-primary" type="submit">Save session</button>
                    <a href="/event/{{$event->id}}" class="btn btn-link">Cancel</a>
                </form>

            </main>
        </div>
    </div>

@endsection
