@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Добавить смену</h1>
        <form action="{{ route('admin.shifts.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="shift_number" class="block text-sm font-medium text-gray-700">Номер смены</label>
                <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ old('shift_number') }}">
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ old('date') }}">
            </div>
            <div class="mb-4">
                <label for="section" class="block text-sm font-medium text-gray-700">Участок</label>
                <input type="text" name="section" id="section" class="mt-1 block w-full border rounded p-2" value="{{ old('section') }}">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Сохранить
                </button>
                <a href="{{ route('admin.shifts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection
