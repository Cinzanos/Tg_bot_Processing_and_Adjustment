@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Редактировать замечание</h1>
        <form action="{{ route('admin.remarks.update', $remark) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Инициатор -->
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Инициатор</label>
                    <select name="user_id" id="user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                    <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">Смена</label>
                    <select name="shift_id" id="shift_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                    <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Участок</label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-1">Оборудование</label>
                    <select name="equipment_id" id="equipment_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите оборудование</option>
                        @if ($remark->equipment)
                            <option value="{{ $remark->equipment->id }}" {{ old('equipment_id', $remark->equipment_id) == $remark->equipment->id ? 'selected' : '' }}>
                                {{ $remark->equipment->machine_number }}
                            </option>
                        @endif
                    </select>
                </div>

                <!-- Тип -->
                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип</label>
                    <select name="type" id="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach (\App\Models\Remark::types() as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $remark->type) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Время создания -->
                <div class="mb-4">
                    <label for="created_at" class="block text-sm font-medium text-gray-700 mb-1">Время создания</label>
                    <input type="datetime-local" name="created_at" id="created_at" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ old('created_at', $remark->created_at ? $remark->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <!-- Текст -->
            <div class="mb-6">
                <label for="text" class="block text-sm font-medium text-gray-700 mb-1">Текст замечания</label>
                <textarea name="text" id="text" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('text', $remark->text) }}</textarea>
            </div>

            <!-- Улучшенный блок загрузки фото -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Фотографии</label>

                <div class="space-y-4">
                    <!-- Текущее фото -->
                    @if ($remark->photo)
                        <div id="current-photo" class="mb-4">
                            <p class="text-sm text-gray-500">Текущее фото:</p>
                            <div class="relative group">
                                <img src="{{ $remark->photo }}" alt="Current Photo" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                <div class="absolute top-1 right-1 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ $remark->photo }}" target="_blank" class="bg-blue-500 text-white rounded-full p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" class="bg-red-500 text-white rounded-full p-1" onclick="removeCurrentPhoto()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mb-4">Фото отсутствует</p>
                    @endif

                    <!-- Основной загрузчик -->
                    <div class="flex items-center justify-center w-full">
                        <label for="photo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Нажмите для загрузки</span> или перетащите фото
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG (макс. 5MB)</p>
                            </div>
                            <input id="photo" name="photo" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>

                    <!-- Превью загруженных фото -->
                    <div id="photo-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden">
                        <div class="relative group">
                            <img id="preview-image" src="" alt="Preview" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" onclick="removePhoto()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Обновить
                </button>
                <a href="{{ route('admin.remarks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Функция для удаления выбранного фото
        function removePhoto() {
            const photoInput = document.getElementById('photo');
            const previewImage = document.getElementById('preview-image');
            const photoPreview = document.getElementById('photo-preview');

            // Сброс значения input
            photoInput.value = '';

            // Очистка превью и скрытие блока
            previewImage.src = '';
            photoPreview.classList.add('hidden');
        }

        // Функция для удаления текущего фото
        function removeCurrentPhoto() {
            const currentPhoto = document.getElementById('current-photo');
            const photoInput = document.getElementById('photo');

            // Сброс значения input и скрытие текущего фото
            photoInput.value = '';
            currentPhoto.classList.add('hidden');
            // Добавляем скрытое поле для указания удаления фото
            const removePhotoInput = document.createElement('input');
            removePhotoInput.type = 'hidden';
            removePhotoInput.name = 'remove_photo';
            removePhotoInput.value = '1';
            document.querySelector('form').appendChild(removePhotoInput);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const shiftSelect = document.getElementById('shift_id');
            const sectionSelect = document.getElementById('section_id');
            const equipmentSelect = document.getElementById('equipment_id');
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('photo-preview');
            const previewImage = document.getElementById('preview-image');

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

            // Превью загружаемого фото
            photoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        photoPreview.classList.remove('hidden');
                        // Скрыть текущее фото, если оно есть
                        const currentPhoto = document.getElementById('current-photo');
                        if (currentPhoto) {
                            currentPhoto.classList.add('hidden');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });

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
                                option.textContent = 'Оборудование не найдено';
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
