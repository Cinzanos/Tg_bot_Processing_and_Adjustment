@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать наладку</h1>
        <form action="{{ route('admin.adjustments.update', $adjustment) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700">Наладчик</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $adjustment->user_id) == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="equipment_id" class="block text-sm font-medium text-gray-700">Оборудование</label>
                <select name="equipment_id" id="equipment_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($equipment as $item)
                        <option value="{{ $item->id }}" {{ old('equipment_id', $adjustment->equipment_id) == $item->id ? 'selected' : '' }}>{{ $item->machine_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Смена</label>
                <select name="shift_id" id="shift_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id', $adjustment->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->shift_number }} ({{ $shift->date }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Время начала</label>
                <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full border rounded p-2" value="{{ old('start_time', $adjustment->start_time) }}">
            </div>
            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700">Время завершения</label>
                <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full border rounded p-2" value="{{ old('end_time', $adjustment->end_time) }}">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Обновить
                </button>
                <a href="{{ route('admin.adjustments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection
