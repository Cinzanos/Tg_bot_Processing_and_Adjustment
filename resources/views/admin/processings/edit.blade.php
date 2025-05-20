@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать обработку</h1>
        <form action="{{ route('admin.processings.update', $processing) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Оператор --}}
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700">Оператор</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full border rounded p-2">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $processing->user_id) == $user->id ? 'selected' : '' }}>
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
                        <option value="{{ $shift->id }}" {{ old('shift_id', $processing->shift_id) == $shift->id ? 'selected' : '' }}>
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
                    <option value="">Выберите оборудование</option>
                </select>
            </div>

            {{-- Время начала --}}
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Время начала</label>
                <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full border rounded p-2"
                       value="{{ old('start_time', \Carbon\Carbon::parse($processing->start_time)->format('Y-m-d\TH:i')) }}">
            </div>

            {{-- Время завершения --}}
            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700">Время завершения</label>
                <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full border rounded p-2"
                       value="{{ old('end_time', $processing->end_time ? \Carbon\Carbon::parse($processing->end_time)->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Обновить
                </button>
                <a href="{{ route('admin.processings.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
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

            const currentSectionId = '{{ old('section_id', optional($processing->equipment->section)->id) }}';
            const currentEquipmentId = '{{ old('equipment_id', $processing->equipment_id) }}';

            function loadSection(shiftId, callback) {
                fetch(`/admin/shift/${shiftId}/section`)
                    .then(response => response.json())
                    .then(data => {
                        sectionSelect.innerHTML = '';
                        if (data && data.id && data.name) {
                            const option = document.createElement('option');
                            option.value = data.id;
                            option.textContent = data.name;
                            option.selected = (data.id == currentSectionId);
                            sectionSelect.appendChild(option);
                            sectionSelect.dispatchEvent(new Event('change'));
                            if (callback) callback(data.id);
                        } else {
                            sectionSelect.innerHTML = `<option value="">Участок не найден</option>`;
                            equipmentSelect.innerHTML = '';
                        }
                    });
            }

            function loadEquipment(sectionId) {
                fetch(`/admin/section/${sectionId}/equipment`)
                    .then(response => response.json())
                    .then(data => {
                        equipmentSelect.innerHTML = '';
                        if (Array.isArray(data)) {
                            data.forEach(e => {
                                const option = document.createElement('option');
                                option.value = e.id;
                                option.textContent = e.machine_number;
                                if (e.id == currentEquipmentId) option.selected = true;
                                equipmentSelect.appendChild(option);
                            });
                        } else {
                            equipmentSelect.innerHTML = `<option value="">Оборудование не найдено</option>`;
                        }
                    });
            }

            shiftSelect.addEventListener('change', function () {
                const shiftId = this.value;
                if (shiftId) {
                    loadSection(shiftId);
                } else {
                    sectionSelect.innerHTML = `<option value="">Выберите участок</option>`;
                    equipmentSelect.innerHTML = '';
                }
            });

            sectionSelect.addEventListener('change', function () {
                const sectionId = this.value;
                if (sectionId) {
                    loadEquipment(sectionId);
                } else {
                    equipmentSelect.innerHTML = '';
                }
            });

            // Автозагрузка при открытии страницы
            const initialShiftId = shiftSelect.value;
            if (initialShiftId) {
                loadSection(initialShiftId);
            }
        });
    </script>
@endpush
