@extends('layouts.main')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="rooms-grid">
        @if(empty($rooms))
            <p>No rooms available at the moment.</p>
        @else
            @foreach($rooms as $room)
                @component('components.room-card', ['room' => $room])
            @endforeach
        @endif
    </div>
</div>
@endsection

