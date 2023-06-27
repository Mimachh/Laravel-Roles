@extends('layout.admin')

@section('content')
<section class="admin-form-container">
    <h1>Modifier un utilisateur</h1>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" required>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Laissez vide pour ne pas modifier le mot de passe">
        </div>

        <div>
            <label>Rôles</label> <br>
            <div>
                @foreach($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" id="role{{ $role->id }}" value="{{ $role->id }}" {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</section>
@endsection
