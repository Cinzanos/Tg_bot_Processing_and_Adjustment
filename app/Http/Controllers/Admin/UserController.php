<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Фильтрация по ФИО
        if ($request->has('full_name') && !empty($request->input('full_name'))) {
            $query->where('full_name', 'like', '%' . $request->input('full_name') . '%');
        }

        // Фильтрация по роли
        if ($request->has('role_id') && !empty($request->input('role_id'))) {
            $query->where('role_id', $request->input('role_id'));
        }

        // Сортировка по ФИО
        $sort = $request->input('sort', 'full_name');
        $direction = $request->input('direction', 'asc');
        if (in_array($sort, ['full_name']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        }

        $users = $query->paginate(10); // Пагинация с 10 записями на страницу

        // Получаем все роли для выпадающего списка
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $adminRoleId = Role::where('name', 'Администратор')->first()->id;
        return view('admin.users.create', compact('roles', 'adminRoleId'));
    }

    public function store(Request $request)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'telegram_id' => 'required|string|unique:users,telegram_id',
            'role_id' => 'required|exists:roles,id',
        ];

        $adminRole = Role::where('name', 'Администратор')->first();
        if ($request->role_id == $adminRole->id) {
            $rules['login'] = 'required|string|unique:users,login';
            $rules['password'] = 'required|string|min:8';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['full_name', 'telegram_id', 'role_id']);
        if ($request->role_id == $adminRole->id && $request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['login'] = $request->login;
        }

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Пользователь создан успешно.');
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $adminRoleId = Role::where('name', 'Администратор')->first()->id;
        return view('admin.users.edit', compact('user', 'roles', 'adminRoleId'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'telegram_id' => 'required|string|unique:users,telegram_id,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ];

        $adminRole = Role::where('name', 'Администратор')->first();
        if ($request->role_id == $adminRole->id) {
            $rules['login'] = 'required|string|unique:users,login,' . $user->id;
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['full_name', 'telegram_id', 'role_id']);

        if ($request->role_id == $adminRole->id) {
            $data['login'] = $request->login;
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
        } else {
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
