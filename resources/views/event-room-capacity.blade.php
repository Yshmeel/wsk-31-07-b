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
                        <h2 class="h4">Room capacity</h2>
                    </div>
                </div>

                <div class="mt-3">
                    <canvas id="chart"></canvas>
                </div>
            </main>
        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statistics = @json($statistics);

        console.log(Object.values(statistics).map((v) => v[0]));

        const config = {
            type: 'bar',
            data: {
                labels: Object.keys(statistics),
                datasets: [{
                    type: 'bar',
                    label: "Attendees",
                    backgroundColor: "green",
                    data: Object.values(statistics).map((v) => v[0]),
                }, {
                    type: 'bar',
                    label: "Capacity",
                    backgroundColor: "blue",
                    data: Object.values(statistics).map((v) => v[1])
                }]
            },
        };

        console.log({
            labels: Object.keys(statistics),
            dataset: [{
                type: 'bar',
                label: "Attendees",
                data: Object.values(statistics).map((v) => v[0]),
            }, {
                type: 'bar',
                label: "Capacity",
                data: Object.values(statistics).map((v) => v[1])
            }]
        });

        new Chart(document.getElementById('chart').getContext('2d'), config);
    });
</script>
