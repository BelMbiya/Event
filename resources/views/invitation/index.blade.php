@extends('admin')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des invitations</h1>
    <a href="{{ route("invitation.create") }}" class="btn btn-primary">Créer une nouvelle invitation</a>
</div>

    <!-- Ici tu peux mettre tes cards, tables, etc. -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i>
                    Liste des invitations
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Number of guest</th>
                                    <th>Code</th>
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Envoyé a</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemple de ligne d'invitation -->
                                <tr>
                                    <td>Mariage</td>
                                    <td>2</td>
                                    <td>ABC123</td>
                                    <td>http://example.com/invitation/ABC123</td>
                                    <td>Envoyée</td>
                                <tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection