<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
        //get all users with pagination
        $users = User::latest()->when($request->input('name'), function($users) use ($request) {
            $users = $users->where('name', 'like', '%'.$request->input('name').'%');
        })
        ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    //create
    public function create()
    {
        return view('pages.users.create');
    }

    //store
    public function store(Request $request)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,staff,user',
        ]);

        //create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        //redirect to users.index
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    //show
    public function show($id)
    {
        $user = User::find($id);
        return view('pages.users.show', compact('user'));
    }

    //edit
    public function edit($id)
    {
        $user = User::find($id);
        return view('pages.users.edit', compact('user'));
    }

    //update
    public function update(Request $request, $id)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required|in:admin,staff,user',
        ]);

        //update user
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        //redirect to users.index
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        //delete user
        $user = User::find($id);
        $user->delete();

        //redirect to users.index
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
