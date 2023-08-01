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
                        <li class="nav-item"><a class="nav-link" href="/event/{{$event->id}}">Overview</a></li>
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
                        <h2 class="h4">Create new ticket</h2>
                    </div>
                </div>

                <form class="needs-validation" novalidate method="POST" action="/event/{{$event->id}}/ticket">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputName">Name</label>
                            <!-- adding the class is-invalid to the input, shows the invalid feedback below -->
                            <input type="text" class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}" id="inputName" name="name" placeholder="" value={{old('name')}}>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{$errors->first('name')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputCost">Cost</label>
                            <input type="number" class="form-control" id="inputCost" name="cost" placeholder="" value={{old('cost')}}>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="selectSpecialValidity">Special Validity</label>
                            <select class="form-control  {{$errors->has('special_validity') ? 'is-invalid' : ''}}"
                                    id="selectSpecialValidity"
                                    name="special_validity">
                                <option value="" {{ old('special_validity') == "" ? 'selected' : '' }}>None</option>
                                <option value="amount"  {{ old('amount') == "" ? 'selected' : '' }}>Limited amount</option>
                                <option value="date"  {{ old('date') == "" ? 'selected' : '' }}>Purchaseable till date</option>
                            </select>

                            @if($errors->has('special_validity'))
                                <div class="invalid-feedback">
                                    {{$errors->first('special_validity')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputAmount">Maximum amount of tickets to be sold</label>
                            <input type="number" class="form-control  {{$errors->has('amount') ? 'is-invalid' : ''}}" id="inputAmount" name="amount" placeholder="" value="0">

                            @if($errors->has('amount'))
                                <div class="invalid-feedback">
                                    {{$errors->first('amount')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <label for="inputValidTill">Tickets can be sold until</label>
                            <input type="text"
                                   class="form-control  {{$errors->has('valid_until') ? 'is-invalid' : ''}}"
                                   id="inputValidTill"
                                   name="valid_until"
                                   placeholder="yyyy-mm-dd HH:MM"
                                   value={{ old('valid_until') }}>

                            @if($errors->has('valid_until'))
                                <div class="invalid-feedback">
                                    {{$errors->first('valid_until')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="mb-4">
                    <button class="btn btn-primary" type="submit">Save ticket</button>
                    <a href="/" class="btn btn-link">Cancel</a>
                </form>

            </main>
        </div>
    </div>


@endsection
