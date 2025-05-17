<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Обработка и Наладка</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white p-4">
        <h2 class="text-2xl font-bold mb-6">Админ-панель</h2>
        <nav>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"></path></svg>
                        Главная
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                        Пользователи
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.shifts.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-5H3v5a2 2 0 002 2z"></path></svg>
                        Смены
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipment.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.75 17L15 21v-6.75L9.75 17zM5 3a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V3z"></path></svg>
                        Оборудование
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.processings.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v16h16V4H4zm2 2h12v12H6V6z"></path></svg>
                        Обработка
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.adjustments.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6l4 2"></path><path d="M20 12a8 8 0 11-16 0 8 8 0 0116 0z"></path></svg>
                        Наладка
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.adjustment-waitings.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"></path><circle cx="12" cy="12" r="10"></circle></svg>
                        Ожидание наладки
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.downtimes.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 12v.01M8 12h.01M16 12h.01M9 16h6"></path><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"></path></svg>
                        Простои
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.remarks.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M3 6h18M3 12h12M3 18h6"></path></svg>
                        Замечания
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v18H3V3z"></path><path d="M8 3v18M16 3v18M3 8h18M3 16h18"></path></svg>
                        Отчеты
                    </a>
                </li>
                @auth
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left py-2 px-4 hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"></path><path d="M3 21h4a2 2 0 002-2V5a2 2 0 00-2-2H3"></path></svg>
                                Выйти
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
    </div>
    <!-- Content -->
    <div class="flex-1 p-6">
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
</body>
</html>
