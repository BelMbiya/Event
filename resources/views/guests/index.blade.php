@extends('admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2"></i>
                Gestion des Invités
            </h1>
            <p class="text-muted">Liste de tous les invités</p>
        </div>
        <div>
            <a href="{{ route('guests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Ajouter un invité
            </a>
        </div>
    </div>

    <!-- Table des invités -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Liste des Invités
            </h6>
        </div>
        <div class="card-body">
            @if($guests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Événement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guests as $guest)
                                <tr>
                                    <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                                    <td>{{ $guest->email }}</td>
                                    <td>{{ $guest->phone }}</td>
                                    <td>{{ $guest->event->title ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('guests.edit', $guest->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('guests.destroy', $guest->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet invité ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $guests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Aucun invité trouvé</h5>
                    <p class="text-gray-500">Commencez par ajouter des invités à vos événements.</p>
                    <a href="{{ route('guests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter le premier invité
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
