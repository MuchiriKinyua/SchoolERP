@extends('layouts.home')

@section('content')
@push('polls-styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
@endpush

@push('polls-scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
    <div class="container">

        <h4 class="center">
            {{$poll->title}}
        </h4>

        <h6>
           {{$poll->EndDateFormat}}
        </h6>

        <form action="{{route('poll.vote',[$poll])}}" method="post">
            @csrf

            @foreach($poll->options as $option)

               <p>
                <label>
                  <input name="option_id" type="radio" value="{{$option->id}}" @if ($selectedOption == $option->id) checked @endif />
                  <span>{{$option->content}}  {{$option->votes_count}}</span>
                </label>
            </p>
            @endforeach

            <button class="waves-effect waves-light btn info darken-2" type="submit">
                vote
            </button>
        </form>
    </div>
@endsection
