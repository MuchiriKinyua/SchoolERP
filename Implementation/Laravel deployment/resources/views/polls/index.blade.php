@extends('layouts.home')

@section('content')
@push('polls-styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
@endpush

@push('polls-scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Polls</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('polls.create') }}">
                        Add New
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <h1 class="center">
            All Polls
        </h1>
        <div class="row">
            <a class="waves-effect waves-light btn info darken-2" href="{{route('poll.create')}}">
            new poll &plus;
            </a>
        </div>
  <table class="centered">
        <thead>
          <tr>
              <th>Title</th>
              <th>Status</th>
              <th>Actions</th>
          </tr>
        </thead>

        <tbody>
            @foreach($polls as $poll)
            <tr>
                <td>{{$poll->title}}</td>
                <td>{{$poll->status}}</td>
                <td>
                    <a class="waves-effect waves-light btn info darken-2" href="{{route('poll.edit',[$poll])}}">
                    update
                    </a>

                    <a class="waves-effect waves-light btn red darken-2" href="{{route('poll.delete',[$poll])}}">
                    delete
                    </a>

                    <a class="waves-effect waves-light btn green lighten-0" href="{{route('poll.show',[$poll])}}">
                    show
                    </a>
                </td>
              </tr>

            @endforeach

        </tbody>
      </table>
    </div>
    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('polls.table')
        </div>
    </div>

@endsection
