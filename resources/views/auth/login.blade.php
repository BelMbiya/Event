@extends('default')
@section('css')
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
@endsection
@section('content')
    <form class="form-signin" action="{{ route('login') }}" method="post">
        @csrf
        <div class="text-center mb-4">
            <h1 class="h3 mb-3 font-weight-normal">Se connecter</h1>
        </div>

        <div class="form-label-group">
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
            <label for="inputEmail">Adresse e-mail</label>
        </div>

        <div class="form-label-group">
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
            <label for="inputPassword">Mot de passe</label>
        </div>

        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Se souvenir
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
    </form>
@endsection
