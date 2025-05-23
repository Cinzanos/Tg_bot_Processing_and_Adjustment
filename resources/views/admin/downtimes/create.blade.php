@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Добавить простой</h1>
        <form action="{{ route('admin.downtimes.store') }}" method="POST">
            @csrf

            {{-- Инициатор --}}
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700">Инициатор</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите инициатора</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Смена --}}
            <div class="mb-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Смена</label>
                <select name="shift_id" id="shift_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите смену</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                            {{ $shift->shift_number }} ({{ $shift->date }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Участок --}}
            <div class="mb-4">
                <label for="section_id" class="block text-sm font-medium text-gray-700">Участок</label>
                <select name="section_id" id="section_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите участок</option>
                </select>
            </div>

            {{-- Оборудование --}}
            <div class="mb-4">
                <label for="equipment_id" class="block text-sm font-medium text-gray-700">Оборудование</label>
                <select name="equipment_id" id="equipment_id" class="mt-1 block w-full border rounded p-2">
                    <!-- Загружается по участку -->
                </select>
            </div>

            {{-- Время начала --}}
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Время начала</label>
                <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full border rounded p-2" value="{{ old('start_time') }}">
            </div>

            {{-- Время завершения --}}
            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700">Время завершения</label>
                <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full border rounded p-2" value="{{ old('end_time') }}">
            </div>

            {{-- Причина --}}
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700">Причина</label>
                <select name="reason" id="reason" class="mt-1 block w-full border rounded p-2">
                    @foreach ($reasons as $reason)
                        <option value="{{ $reason }}" {{ old('reason') == $reason ? 'selected' : '' }}>{{ $reason }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Сохранить
                </button>
                <a href="{{ route('admin.downtimes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shiftSelect = document.getElementById('shift_id');
            const sectionSelect = document.getElementById('section_id');
            const equipmentSelect = document.getElementById('equipment_id');

            shiftSelect.addEventListener('change', function () {
                const shiftId = this.value;

                if (shiftId) {
                    fetch(`/admin/shift/${shiftId}/section`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.id && data.name) {
                                sectionSelect.innerHTML = `<option value="${data.id}">${data.name}</option>`;
                                sectionSelect.dispatchEvent(new Event('change'));
                            } else {
                                sectionSelect.innerHTML = `<option value="">Участок не найден</option>`;
                                equipmentSelect.innerHTML = '';
                            }
                        })
                        .catch(() => {
                            sectionSelect.innerHTML = `<option value="">Ошибка загрузки</option>`;
                            equipmentSelect.innerHTML = '';
                        });
                } else {
                    sectionSelect.innerHTML = `<option value="">Выберите участок</option>`;
                    equipmentSelect.innerHTML = '';
                }
            });

            sectionSelect.addEventListener('change', function () {
                const sectionId = this.value;

                if (sectionId) {
                    fetch(`/admin/section/${sectionId}/equipment`)
                        .then(response => response.json())
                        .then(data => {
                            equipmentSelect.innerHTML = '';
                            if (Array.isArray(data) && data.length > 0) {
                                data.forEach(e => {
                                    const option = document.createElement('option');
                                    option.value = e.id;
                                    option.textContent = e.machine_number;
                                    equipmentSelect.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.textContent = 'Оборудование не найдено';
                                equipmentSelect.appendChild(option);
                            }
                        })
                        .catch(() => {
                            equipmentSelect.innerHTML = '';
                            const option = document.createElement('option');
                            option.textContent = 'Ошибка загрузки';
                            equipmentSelect.appendChild(option);
                        });
                } else {
                    equipmentSelect.innerHTML = '';
                }
            });
        });
    </script>
@endpush
