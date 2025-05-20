<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $roles = Role::query()
            ->orderBy($sort, $direction)
            ->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Role::create($request->all());
        return redirect()->route('admin.roles.index')->with('success', 'Роль создана успешно.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->update($request->all());
        return redirect()->route('admin.roles.index')->with('success', 'Роль обновлена успешно.');
    }

    public function destroy(Role $role)
    {
        // Проверяем, есть ли пользователи с этой ролью
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Нельзя удалить роль, которая используется пользователями.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Роль удалена успешно.');
    }
}
