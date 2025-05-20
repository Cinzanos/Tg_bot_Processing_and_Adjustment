@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать замечание</h1>
        <form action="{{ route('admin.remarks.update', $remark) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Инициатор -->
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700">Инициатор</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите инициатора</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $remark->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Смена -->
            <div class="mb-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Смена</label>
                <select name="shift_id" id="shift_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите смену</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id', $remark->shift_id) == $shift->id ? 'selected' : '' }}>
                            {{ $shift->shift_number }} ({{ $shift->date }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Участок -->
            <div class="mb-4">
                <label for="section_id" class="block text-sm font-medium text-gray-700">Участок</label>
                <select name="section_id" id="section_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите участок</option>
                    @if ($remark->shift && $remark->shift->section)
                        <option value="{{ $remark->shift->section->id }}" {{ old('section_id', $remark->equipment->section_id ?? '') == $remark->shift->section->id ? 'selected' : '' }}>
                            {{ $remark->shift->section->name }}
                        </option>
                    @endif
                </select>
            </div>

            <!-- Оборудование -->
            <div class="mb-4">
                <label for="equipment_id" class="block text-sm font-medium text-gray-700">Оборудование</label>
                <select name="equipment_id" id="equipment_id" class="mt-1 block w-full border rounded p-2">
                    <option value="">Выберите оборудование</option>
                    @if ($remark->equipment)
                        <option value="{{ $remark->equipment->id }}" {{ old('equipment_id', $remark->equipment_id) == $remark->equipment->id ? 'selected' : '' }}>
                            {{ $remark->equipment->machine_number }}
                        </option>
                    @endif
                </select>
            </div>

            <!-- Текст -->
            <div class="mb-4">
                <label for="text" class="block text-sm font-medium text-gray-700">Текст</label>
                <textarea name="text" id="text" class="mt-1 block w-full border rounded p-2">{{ old('text', $remark->text) }}</textarea>
            </div>

            <!-- Фото -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Текущее фото</label>
                @if ($remark->photo)
                    <div class="mb-2">
                        <a href="{{ $remark->photo }}" target="_blank" class="text-blue-500 hover:underline">Просмотреть фото</a>
                    </div>
                @else
                    <p class="mb-2">Фото отсутствует</p>
                @endif
                <label for="photo" class="block text-sm font-medium text-gray-700">Загрузить новое фото</label>
                <input type="file" name="photo" id="photo" class="mt-1 block w-full border rounded p-2">
            </div>

            <!-- Тип -->
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Тип</label>
                <select name="type" id="type" class="mt-1 block w-full border rounded p-2">
                    @foreach (\App\Models\Remark::types() as $value => $label)
                        <option value="{{ $value }}" {{ old('type', $remark->type) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Время создания -->
            <div class="mb-4">
                <label for="created_at" class="block text-sm font-medium text-gray-700">Время создания</label>
                <input type="datetime-local" name="created_at" id="created_at" class="mt-1 block w-full border rounded p-2" value="{{ old('created_at', $remark->created_at ? $remark->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Обновить
                </button>
                <a href="{{ route('admin.remarks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
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

            // Предзаполнение начальных значений
            const initialShiftId = '{{ $remark->shift_id }}';
            const initialSectionId = '{{ $remark->equipment->section_id ?? '' }}';
            const initialEquipmentId = '{{ $remark->equipment_id }}';

            if (initialShiftId) {
                fetch(`/admin/shift/${initialShiftId}/section`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.id && data.name) {
                            sectionSelect.innerHTML = `<option value="${data.id}">${data.name}</option>`;
                            if (initialSectionId) {
                                sectionSelect.dispatchEvent(new Event('change'));
                            }
                        }
                    })
                    .catch(() => {
                        sectionSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                    });
            }

            if (initialSectionId && initialEquipmentId) {
                fetch(`/admin/section/${initialSectionId}/equipment`)
                    .then(response => response.json())
                    .then(data => {
                        equipmentSelect.innerHTML = '';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(e => {
                                const option = document.createElement('option');
                                option.value = e.id;
                                option.textContent = e.machine_number;
                                if (e.id == initialEquipmentId) option.selected = true;
                                equipmentSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(() => {
                        equipmentSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                    });
            }

            // Динамическая загрузка при изменении
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
                                sectionSelect.innerHTML = '<option value="">Участок не найден</option>';
                                equipmentSelect.innerHTML = '';
                            }
                        })
                        .catch(() => {
                            sectionSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                            equipmentSelect.innerHTML = '';
                        });
                } else {
                    sectionSelect.innerHTML = '<option value="">Выберите участок</option>';
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
                                option.textContent = 'Оборудование не найден';
                                equipmentSelect.appendChild(option);
                            }
                        })
                        .catch(() => {
                            equipmentSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                        });
                } else {
                    equipmentSelect.innerHTML = '';
                }
            });
        });
    </script>
@endpush
