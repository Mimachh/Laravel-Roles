@extends('layout.admin')

@section('content')
<section class="admin-form-container">
    <h1>Créer un nouvel utilisateur</h1>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div>
            <label>Rôles :</label><br>
            @foreach($roles as $role)
                <label>
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                    {{ $role->name }}
                </label><br>
            @endforeach
        </div>

        <button type="submit">Créer</button>
    </form>
</section>
@endsection