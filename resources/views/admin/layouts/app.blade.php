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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-65 bg-gray-800 text-white p-4 flex-shrink-0 min-h-full">
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
                        Сотрудники
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        Роли
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sections.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h16.5v16.5H3.75V3.75zM9 3.75v16.5M15 3.75v16.5M3.75 9h16.5M3.75 15h16.5" />
                        </svg>
                        Участки
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.shifts.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-2a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 9v2a4 4 0 004 4h4m0 0l-3-3m3 3l-3 3"></path></svg>
                        Смены
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipment.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a2.5 2.5 0 0 0 2.39-1.73l.5-2.5a2.5 2.5 0 0 0-1.73-2.39 2.5 2.5 0 0 0-2.608.329M9.195 6.615a2.5 2.5 0 0 1 2.39-1.73 2.5 2.5 0 0 1 2.39 1.73 2.5 2.5 0 0 1-1.73 2.39 2.5 2.5 0 0 1-2.608-.329L9.195 6.615z" />
                        </svg>
                        Оборудование
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.processings.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"></path></svg>
                        Обработка
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.adjustments.index') }}" class="flex items-center py-2 px-4 hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                        </svg>
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
        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                {{ session('error') }}
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
        @yield('scripts')
    });
</script>
@stack('scripts')
</body>
</html>
