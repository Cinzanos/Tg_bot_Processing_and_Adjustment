<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'telegram_id' => 'required|string|unique:users,telegram_id',
            'role' => 'required|in:master,brigadier,operator,adjuster,admin',
        ];

        if ($request->role === 'admin') {
            $rules['login'] = 'required|string|unique:users,login';
            $rules['password'] = 'required|string|min:8';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        if ($request->role === 'admin' && $request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Пользователь создан успешно.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'telegram_id' => 'required|string|unique:users,telegram_id,' . $user->id,
            'role' => 'required|in:master,brigadier,operator,adjuster,admin',
        ];

        if ($request->role === 'admin') {
            $rules['login'] = 'required|string|unique:users,login,' . $user->id;
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        if ($request->role === 'admin' && $request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->role !== 'admin') {
            $data['login'] = null;
            $data['password'] = null;
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Пользователь обновлен успешно.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь удален успешно.');
    }
}
