<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserController extends Controller
{
    use SoftDeletes;

    public function index()
    {
        $users = User::with('roles')
        ->whereDoesntHave('roles', function ($query) {
            $query->where('role_id', 1);
        })
        ->get();

        $trashedUsers = User::onlyTrashed()->get();

        return view('admin.users.index', compact('users', 'trashedUsers'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'roles' => 'array' // Ajoutez d'autres validations si nécessaire
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        $roles = $request->input('roles');
        if (!empty($roles)) {
            $user->roles()->attach($roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès');
    }


    public function edit($id)
    {
        $this->authorize('update', User::class);

        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
    
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', User::class);

        $user = User::findOrFail($id);
    
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:6',
            'roles' => 'array',
        ]);
    
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
    
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
    
        $user->save();
    
        $user->roles()->sync($validatedData['roles']);
    
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function trash()
    {
        $this->authorize('viewInTrash', User::class);

        $trashedUsers = User::onlyTrashed()->get();

        return view('admin.users.trash', compact('trashedUsers'));
    }
    
    public function destroyMultiple(Request $request)
    {
        
        $this->authorize('delete', User::class);

        $userIds = json_decode($request->input('selectedUsers', '[]'), true);
       
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        $count = count($userIds);
        // dd($count);
        if ($count > 0) {
            User::whereIn('id', $userIds)->delete();
        
            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur(s) supprimé(s) avec succès.');
        }
    
        return redirect()->route('admin.users.index')
            ->with('error', 'Aucun utilisateur sélectionné.');
    }

    public function forceDestroyMultiple(Request $request)
    {
        // dd($request);
        $this->authorize('forceDelete', User::class);
        // $userIds = $request->input('selectedUsers', []);

        $userIds = json_decode($request->input('selectedUsers', '[]'), true);

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }
        // dd($userIds);
        $count = count($userIds);
        // dd($count);
        if ($count > 0) {
            User::whereIn('id', $userIds)->forceDelete();
        
            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur(s) supprimé(s) avec succès.');
        }
    
        return redirect()->route('admin.users.index')
            ->with('error', 'Aucun utilisateur sélectionné.');
    }

    public function restoreMultiple(Request $request)
    {
        $this->authorize('restore', User::class);

        $userIds = json_decode($request->input('selectedUsers'));

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        $count = count($userIds);

        if ($count > 0) {
            User::whereIn('id', $userIds)->restore();
        
            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur(s) restauré(s) avec succès.');
        }

        return redirect()->back()->with('error', 'Aucun utilisateur sélectionné.');;
    }
}
