<?php



?>

@extends('admin')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des invites</h1>
    </div>
<section class="relative py-24 bg-gradient-to-b from-rose-50 via-white to-rose-100">

    <div class="max-w-3xl mx-auto relative z-10 flex flex-col items-center">
        <h2 class="romantic-font text-5xl text-rose-600 text-center mb-12">Ajouter un Invité</h2>

        <div class="glass-effect rounded-3xl shadow-xl p-10 md:p-14">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('guests.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="event_id" class="font-semibold text-rose-600">Événement <span class="text-red-500">*</span></label>
                        <select name="event_id" id="event_id" class="form-control rounded-lg border border-gray-300 p-2" required>
                            <option value="">Sélectionnez un événement</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} - {{ $event->event_date }}</option>
                            @endforeach
                        </select>
                        @error('event_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Guest Type -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="guest_type_id" class="font-semibold text-rose-600">Type d'invité <span class="text-red-500">*</span></label>
                        <select name="guest_type_id" id="guest_type_id" class="form-control rounded-lg border border-gray-300 p-2" required>
                            <option value="">Sélectionnez un type</option>
                            @foreach($guestTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->label }}</option>
                            @endforeach
                        </select>
                        @error('guest_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Prénom -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="first_name" class="font-semibold text-rose-600">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control rounded-lg border border-gray-300 p-2" value="{{ old('first_name') }}" required>
                        @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nom -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="last_name" class="font-semibold text-rose-600">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control rounded-lg border border-gray-300 p-2" value="{{ old('last_name') }}" required>
                        @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="email" class="font-semibold text-rose-600">Email</label>
                        <input type="email" name="email" id="email" class="form-control rounded-lg border border-gray-300 p-2" value="{{ old('email') }}">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="phone" class="font-semibold text-rose-600">Téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control rounded-lg border border-gray-300 p-2" value="{{ old('phone') }}">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- RSVP Status -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="rsvp_status" class="font-semibold text-rose-600">Statut RSVP</label>
                        <select name="rsvp_status" id="rsvp_status" class="form-control rounded-lg border border-gray-300 p-2">
                            <option value="">-- Choisir --</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">confirmed</option>
                            <option value="declined">declined</option>
                        </select>
                        @error('rsvp_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Meal Choice -->
                    <div class="form-group col-xl-6 col-md-6 mb-4">
                        <label for="meal_choice" class="font-semibold text-rose-600">Choix du repas</label>
                        <input type="text" name="meal_choice" id="meal_choice" class="form-control rounded-lg border border-gray-300 p-2 w-full" value="{{ old('meal_choice') }}">
                        @error('meal_choice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bouton -->
                <div class="text-center mt-8">
                    <button type="submit" class="btn btn-primary px-6 py-3 rounded-lg bg-rose-600 text-white font-semibold hover:bg-rose-700 transition">
                        Ajouter l'invité 
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
