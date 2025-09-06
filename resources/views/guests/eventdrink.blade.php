@extends('admin')
@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des invites</h1>
    <a href="{{ route("guests.create") }}" class="btn btn-primary">Créer une nouvelle invite</a>
</div>
<section class="relative py-24 bg-gradient-to-b from-rose-50 via-white to-rose-100">

    <div class="max-w-6xl mx-auto relative z-10">
        <h2 class="romantic-font text-5xl text-rose-600 text-center mb-12">Liste des Invités</h2>

        <div class="glass-effect rounded-3xl shadow-xl p-10 md:p-14 overflow-x-auto">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-sm">
                <thead class="bg-rose-100 text-rose-600 font-semibold text-left">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Prénom</th>
                        <th class="px-6 py-3">Nom</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Téléphone</th>
                        <th class="px-6 py-3">Événement</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">RSVP</th>
                        <th class="px-6 py-3">Repas</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr class="border-b border-gray-200 hover:bg-rose-50 transition">
                            <td class="px-6 py-4">{{ $guest->id }}</td>
                            <td class="px-6 py-4">{{ $guest->first_name }}</td>
                            <td class="px-6 py-4">{{ $guest->last_name }}</td>
                            <td class="px-6 py-4">{{ $guest->email ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $guest->phone ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $guest->event->title ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $guest->guestType->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ ucfirst($guest->rsvp_status ?? 'pending') }}</td>
                            <td class="px-6 py-4">{{ $guest->meal_choice ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('guests.edit', $guest->id) }}" class="px-3 py-1 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition">Éditer</a>
                                <form action="{{ route('guests.destroy', $guest->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cet invité ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">Aucun invité trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $guests->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
