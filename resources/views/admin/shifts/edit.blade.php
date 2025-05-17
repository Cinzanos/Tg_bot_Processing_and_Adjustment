@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать смену</h1>
        <form action="{{ route('admin.shifts.update', $shift) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="shift_number" class="block text-sm font-medium text-gray-700">Номер смены</label>
                <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ old('shift_number', $shift->shift_number) }}">
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ old('date', $shift->date) }}">
            </div>
            <div class="mb-4">
                <label for="section" class="block text-sm font-medium text-gray-700">Участок</label>
                <input type="text" name="section" id="section" class="mt-1 block w-full border rounded p-2" value="{{ old('section', $shift->section) }}">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Обновить
                </button>
                <a href="{{ route('admin.shifts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection
