@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Добавить замечание</h1>
        <form action="{{ route('admin.remarks.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700">Инициатор</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="equipment_id" class="block text-sm font-medium text-gray-700">Оборудование</label>
                <select name="equipment_id" id="equipment_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($equipment as $item)
                        <option value="{{ $item->id }}" {{ old('equipment_id') == $item->id ? 'selected' : '' }}>{{ $item->section }} / {{ $item->machine_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Смена</label>
                <select name="shift_id" id="shift_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift_number }} ({{ $shift->date }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="text" class="block text-sm font-medium text-gray-700">Текст</label>
                <textarea name="text" id="text" class="mt-1 block w-full border rounded p-2">{{ old('text') }}</textarea>
            </div>
            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700">Фото (URL)</label>
                <input type="text" name="photo" id="photo" class="mt-1 block w-full border rounded p-2" value="{{ old('photo') }}">
            </div>
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Тип</label>
                <select name="type" id="type" class="mt-1 block w-full border rounded p-2">
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Сохранить
                </button>
                <a href="{{ route('admin.remarks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection
