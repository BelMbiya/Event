@extends('admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Laisser un message au couple</h1>

        <form action="{{ route('book.store') }}" method="POST">
            @csrf
            <div class="row">

                <!-- Événement (event_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="event_id">Événement</label>
                    <select name="event_id" id="event_id" class="form-control select2" required>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Invité (guest_id) -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="guest_id">Invité</label>
                    <select name="guest_id" id="guest_id" class="form-control select2">
                        @foreach ($guests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->first_name }} - {{ $guest->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Message -->
                <div class="form-group col-xl-6 col-md-6 mb-4">
                    <label for="message">Message</label>
                    <textarea type="text" name="message" id="message" class="form-control"
                        value="" cols="30" rows="10"></textarea>
                </div>

                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </div>
        </form>
    </div>
@endsection
