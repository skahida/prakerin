<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tera Prakerin') — Sistem Informasi Prakerin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

    <style>
        :root {
            --workspace-bg: #fdfdfe;
            --sidebar-dark: #0f172a;
            --brand-primary: #0d9488;
        }

        body {
            background-color: var(--workspace-bg);
            color: #334155;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .sidebar-container {
            width: 280px;
            background: var(--sidebar-dark);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: 20px 0 50px -10px rgba(15, 23, 42, 0.1);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin: 2px 16px;
            border-radius: 12px;
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #f1f5f9;
        }

        .nav-link.active {
            background: var(--brand-primary);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(13, 148, 136, 0.3);
        }

        .nav-link.active .material-icons-round {
            color: white;
        }

        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin 0.4s ease;
        }

        .glass-header {
            background: rgba(253, 253, 254, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f1f5f9;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            margin: 24px 32px 8px;
        }

        @media (max-width: 1024px) {
            .sidebar-container {
                transform: translateX(-100%);
            }

            .sidebar-container.open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }

        .page-enter {
            animation: fadeInSlide 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="user-data" data-username="{{ session('name', Auth::user()->name ?? 'Guest') }}"></div>

    <div id="sidebar-overlay"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300">
    </div>

    {{-- ===================== SIDEBAR ===================== --}}
    <aside id="sidebar" class="sidebar-container">
        <div class="h-20 flex items-center px-8 mb-2">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 bg-teal-500 rounded-lg flex items-center justify-center shadow-lg shadow-teal-900/20">
                    <span class="material-icons-round text-white text-xl">school</span>
                </div>
                <span class="text-xl font-extrabold text-white tracking-tighter font-['Plus_Jakarta_Sans']">
                    Tera <span class="text-teal-500">Prakerin</span>
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto pb-8 custom-scrollbar">
            <nav>
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-icons-round text-[20px]">grid_view</span>
                    <span>Dashboard</span>
                </a>

                @if (auth()->user()->role === 'super-admin')
                    <p class="nav-section-label">Master Data</p>
                    <a href="{{ route('class') }}" class="nav-link {{ request()->routeIs('class') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">meeting_room</span>
                        <span>Kelas</span>
                    </a>
                    <a href="{{ route('department') }}"
                        class="nav-link {{ request()->routeIs('department') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">account_tree</span>
                        <span>Jurusan</span>
                    </a>
                    <a href="{{ route('batch') }}" class="nav-link {{ request()->routeIs('batch') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">waves</span>
                        <span>Gelombang</span>
                    </a>
                    <a href="{{ route('student') }}"
                        class="nav-link {{ request()->routeIs('student') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">face</span>
                        <span>Siswa</span>
                    </a>
                    <a href="{{ route('mentor') }}"
                        class="nav-link {{ request()->routeIs('mentor') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">supervisor_account</span>
                        <span>Pembimbing</span>
                    </a>
                    <a href="{{ route('dudi') }}" class="nav-link {{ request()->routeIs('dudi') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">business</span>
                        <span>DUDI</span>
                    </a>
                    <a href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">admin_panel_settings</span>
                        <span>Admin</span>
                    </a>

                    <p class="nav-section-label">Operasional</p>
                    <a href="{{ route('telegram') }}"
                        class="nav-link {{ request()->routeIs('telegram') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">send</span>
                        <span>Setting Telegram</span>
                    </a>
                    <a href="{{ route('history.presence') }}"
                        class="nav-link {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">history</span>
                        <span>Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('jurnal.index') }}"
                        class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">menu_book</span>
                        <span>Jurnal Harian</span>
                    </a>
                    <a href="{{ route('report') }}"
                        class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">description</span>
                        <span>Laporan Prakerin</span>
                    </a>
                    <a href="{{ route('monitoring') }}"
                        class="nav-link {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">videocam</span>
                        <span>Monitoring</span>
                    </a>
                    <a href="{{ route('map-monitoring.index') }}"
                        class="nav-link {{ request()->routeIs('map-monitoring.index') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">map</span>
                        <span>Peta Monitoring</span>
                    </a>

                    <p class="nav-section-label">Arsip</p>
                    <a href="{{ route('student.archive') }}"
                        class="nav-link {{ request()->routeIs('student.archive') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">inventory_2</span>
                        <span>Arsip Siswa</span>
                    </a>
                    <a href="{{ route('admin.archive') }}"
                        class="nav-link {{ request()->routeIs('admin.archive') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">inventory_2</span>
                        <span>Arsip Admin</span>
                    </a>
                    <a href="{{ route('mentor.archive') }}"
                        class="nav-link {{ request()->routeIs('mentor.archive') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">inventory_2</span>
                        <span>Arsip Pembimbing</span>
                    </a>
                @elseif (auth()->user()->role === 'admin')
                    <p class="nav-section-label">Menu Admin</p>
                    <a href="{{ route('student') }}"
                        class="nav-link {{ request()->routeIs('student') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">face</span>
                        <span>Siswa</span>
                    </a>
                    <a href="{{ route('monitoring') }}"
                        class="nav-link {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">videocam</span>
                        <span>Monitoring</span>
                    </a>
                    <a href="{{ route('history.presence') }}"
                        class="nav-link {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">history</span>
                        <span>Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('jurnal.index') }}"
                        class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">menu_book</span>
                        <span>Jurnal Harian</span>
                    </a>
                    <a href="{{ route('report') }}"
                        class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">description</span>
                        <span>Laporan Prakerin</span>
                    </a>
                @elseif (auth()->user()->role === 'student')
                    <p class="nav-section-label">Menu Siswa</p>
                    <a href="{{ route('presence') }}"
                        class="nav-link {{ request()->routeIs('presence') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">history</span>
                        <span>Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('jurnal.index') }}"
                        class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">menu_book</span>
                        <span>Jurnal Harian</span>
                    </a>
                    <a href="{{ route('report') }}"
                        class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">description</span>
                        <span>Laporan Prakerin</span>
                    </a>
                @elseif (auth()->user()->role === 'mentor')
                    <p class="nav-section-label">Menu Pembimbing</p>
                    <a href="{{ route('monitoring') }}"
                        class="nav-link {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">videocam</span>
                        <span>Monitoring</span>
                    </a>
                    <a href="{{ route('history.presence') }}"
                        class="nav-link {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">history</span>
                        <span>Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('jurnal.index') }}"
                        class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">menu_book</span>
                        <span>Jurnal Harian</span>
                    </a>
                    <a href="{{ route('report') }}"
                        class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                        <span class="material-icons-round text-[20px]">description</span>
                        <span>Laporan Prakerin</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-6 bg-slate-950/50">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[10px] font-bold text-slate-500 tracking-[0.2em] uppercase italic">System
                    Authenticated</span>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <div class="main-wrapper">
        <header class="glass-header h-20 px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle"
                    class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-500 bg-slate-100 rounded-xl">
                    <span class="material-icons-round">sort</span>
                </button>
                <div class="hidden sm:block">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem Informasi Prakerin
                    </p>
                    <h2 class="text-sm font-bold text-slate-800">SMK NU AL HIDAYAH KUDUS</h2>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="relative">
                    <button id="user-dropdown-trigger"
                        class="group flex items-center gap-3 p-1.5 pr-4 bg-slate-100/50 border border-slate-200/60 rounded-full transition-all hover:bg-white hover:shadow-sm">
                        <div
                            class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs uppercase shadow-inner shadow-white/10">
                            {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                        </div>
                        <div class="text-left leading-tight hidden md:block">
                            <p class="text-[11px] font-extrabold text-slate-800">{{ Auth::user()->name ?? 'Guest' }}
                            </p>
                            <p class="text-[9px] font-medium text-slate-500 uppercase">{{ Auth::user()->role ?? '-' }}
                            </p>
                        </div>
                        <span
                            class="material-icons-round text-slate-400 text-sm group-hover:text-slate-600 transition-transform group-hover:translate-y-0.5">expand_more</span>
                    </button>

                    <div id="user-dropdown"
                        class="absolute right-0 mt-4 w-60 bg-white rounded-2xl shadow-2xl border border-slate-100 hidden overflow-hidden transform origin-top-right transition-all z-50">
                        <div class="p-5 border-b border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Identitas
                                Sesi</p>
                            <p class="font-bold text-slate-800 text-sm">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email ?? '-' }}</p>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-rose-500 hover:bg-rose-50 transition-colors">
                                    <span class="material-icons-round text-sm">logout</span>
                                    AKHIRI SESI
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 page-enter">
            @yield('content')
        </main>

        <footer class="p-8 border-t border-slate-100">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">© 2026 Tera Prakerin • SMK
                    NU AL HIDAYAH KUDUS</p>
                <div class="flex items-center gap-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Core v3.2.0</span>
                    <span class="text-[10px] font-bold text-teal-600 uppercase">Cloud Sync Active</span>
                </div>
            </div>
        </footer>
    </div>

    {{-- ===================== CORE JS ===================== --}}
    <script>
        // Sidebar toggle
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        const toggleSidebar = () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
            setTimeout(() => overlay.classList.toggle('opacity-100'), 10);
        };

        toggle?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);

        // User dropdown
        const profileTrigger = document.getElementById('user-dropdown-trigger');
        const dropdown = document.getElementById('user-dropdown');

        profileTrigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', () => dropdown?.classList.add('hidden'));
    </script>

    {{-- ===================== SEMUA SCRIPT ASLI ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const archiveButtons = document.querySelectorAll('.archive-btn');
            archiveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const Id = button.getAttribute('data-id');
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data ini akan diarsipkan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, arsipkan!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/user/${Id}/archive`, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => {
                                    if (response.ok) {
                                        Swal.fire('Berhasil!',
                                                'Data berhasil diarsipkan.', 'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Gagal!',
                                            'Terjadi kesalahan saat mengarsipkan data.',
                                            'error');
                                    }
                                })
                                .catch(() => Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.', 'error'));
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activateButtons = document.querySelectorAll('.archive-active-btn');
            activateButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = button.getAttribute('data-id');
                    Swal.fire({
                        title: 'Aktifkan Kembali',
                        text: 'Apakah Anda yakin ingin mengaktifkan kembali pengguna ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, aktifkan!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/activate-user/${userId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        user_id: userId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Berhasil!',
                                                'Pengguna telah berhasil diaktifkan kembali.',
                                                'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Gagal!',
                                            'Terjadi kesalahan saat mengaktifkan pengguna.',
                                            'error');
                                    }
                                })
                                .catch(() => Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.', 'error'));
                        }
                    });
                });
            });
        });
    </script>

    <script>
        $('.selectpicker').selectpicker();
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#student_select').change(function() {
                var studentId = $(this).val();
                if (studentId) {
                    $.ajax({
                        url: '/get-student-location/' + studentId,
                        method: 'GET',
                        success: function(response) {
                            if (response.latitude && response.longitude) {
                                $('#latitude').val(response.latitude);
                                $('#longitude').val(response.longitude);
                            } else {
                                $('#latitude').val('');
                                $('#longitude').val('');
                            }
                        },
                        error: function() {
                            $('#latitude').val('');
                            $('#longitude').val('');
                        }
                    });
                } else {
                    $('#latitude').val('');
                    $('#longitude').val('');
                }
            });
        });
    </script>

    <script>
        function showOnlineUsers() {
            fetch('/online-users')
                .then(response => response.json())
                .then(users => {
                    let tableHtml =
                        '<table class="table"><thead><tr><th>#</th><th>User</th><th>Status</th><th>Last Activity</th></tr></thead><tbody>';
                    users.forEach((user, index) => {
                        let lastActivityTime = new Date(user.last_activity * 1000);
                        let formattedTime = lastActivityTime.toLocaleString();
                        tableHtml += `<tr>
                            <td>${index + 1}</td>
                            <td>${user.name}</td>
                            <td><span class="badge badge-primary">Online</span></td>
                            <td>${formattedTime}</td>
                        </tr>`;
                    });
                    tableHtml += '</tbody></table>';
                    Swal.fire({
                        title: 'Online Users',
                        html: tableHtml,
                        showCloseButton: true,
                        showCancelButton: false,
                        focusConfirm: false,
                        confirmButtonText: 'Close',
                        width: '80%',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    });
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to fetch online users.',
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
                });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordButtons = document.querySelectorAll('.reset-password-btn');
            resetPasswordButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = button.getAttribute('data-id');
                    Swal.fire({
                        title: 'Reset Password',
                        text: 'Apakah Anda yakin ingin mereset password untuk pengguna ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Reset Password',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            return fetch(`/reset-password/${userId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        user_id: userId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Berhasil!',
                                            'Password telah berhasil direset.',
                                            'success');
                                    } else {
                                        Swal.fire('Gagal!',
                                            'Terjadi kesalahan saat mereset password.',
                                            'error');
                                    }
                                })
                                .catch(() => Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.', 'error'));
                        }
                    });
                });
            });
        });
    </script>

    @if (session('requires_password_update'))
        <script>
            function showPasswordUpdateModal() {
                Swal.fire({
                    title: 'Untuk alasan keamanan, Anda harus memperbarui password Anda sebelum melanjutkan.',
                    html: `
                    <input type="text" id="newPassword" class="swal2-input" placeholder="Masukkan password baru" autofocus>
                    <p style="font-size: 12px; color: #777; margin-top: 5px;">
                        Password harus memiliki minimal 6 karakter dan mengandung kombinasi huruf, angka, serta simbol.
                    </p>
                `,
                    confirmButtonText: 'Simpan',
                    showCancelButton: false,
                    preConfirm: () => {
                        const newPassword = document.getElementById('newPassword').value;
                        const passwordPattern =
                            /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"'<>,.?/\\|-]).{6,}$/;
                        if (!newPassword) {
                            Swal.showValidationMessage('Password tidak boleh kosong!');
                        } else if (newPassword.length < 6) {
                            Swal.showValidationMessage('Password harus terdiri dari minimal 6 karakter!');
                        } else if (!passwordPattern.test(newPassword)) {
                            Swal.showValidationMessage(
                                'Password harus mengandung kombinasi huruf, angka, dan simbol!');
                        }
                        return newPassword;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const newPassword = result.value;
                        if (newPassword) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Silakan tunggu sebentar.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            $.ajax({
                                url: '/update-password',
                                method: 'POST',
                                data: {
                                    password: newPassword,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: response.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        timerProgressBar: true
                                    }).then(() => {
                                        window.location.href = '{{ route('logout') }}';
                                    });
                                },
                                error: function(xhr) {
                                    const errorMessage = xhr.responseJSON?.message ||
                                        'Terjadi kesalahan. Silakan coba lagi.';
                                    Swal.fire('Terjadi kesalahan', errorMessage, 'error');
                                }
                            });
                        } else {
                            showPasswordUpdateModal();
                        }
                    } else {
                        showPasswordUpdateModal();
                    }
                });
            }
            showPasswordUpdateModal();
        </script>
    @endif

    @if (session('requires_chat_id_update') && session('ses_role') == 'mentor')
        <script>
            function showChatIdUpdateModal() {
                Swal.fire({
                    title: 'Untuk melanjutkan, Anda perlu mendapatkan Chat ID Telegram Anda.',
                    html: `
                    <p style="font-size: 14px;">Klik link di bawah ini untuk mendapatkan Chat ID Anda dari bot Telegram:</p>
                    <a href="https://t.me/PrakerinTracerBot" target="_blank" style="font-size: 16px; color: #008CBA; text-decoration: underline;">Klik di sini untuk mendapatkan Chat ID</a>
                    <p style="font-size: 12px; color: #777; margin-top: 10px;">Setelah Anda mendapatkan Chat ID, harap kembali ke halaman ini untuk melanjutkan.</p>
                    <p style="font-size: 12px; color: #777; margin-top: 20px;"><strong>Catatan:</strong> Chat ID ini digunakan untuk menerima notifikasi presensi siswa prakerin Anda.</p>
                    <input type="text" id="chat_id" class="swal2-input" placeholder="Masukkan Chat ID Telegram" autofocus>
                `,
                    confirmButtonText: 'Simpan Chat ID',
                    showCancelButton: true,
                    preConfirm: () => {
                        const chatId = document.getElementById('chat_id').value;
                        if (!chatId) Swal.showValidationMessage('Chat ID tidak boleh kosong!');
                        return chatId;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const chatId = result.value;
                        Swal.fire({
                            title: 'Tunggu Sebentar',
                            text: 'Sedang mengirim data...',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });

                        $.ajax({
                            url: "{{ route('mentor.updateChatId') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                chat_id: chatId
                            },
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Chat ID Berhasil Diperbarui!',
                                    text: 'Chat ID Anda telah berhasil disimpan.',
                                    timer: 1500,
                                    willClose: () => window.location.href = "{{ route('logout') }}"
                                });
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message ||
                                    'Terjadi kesalahan. Silakan coba lagi.';
                                Swal.fire('Terjadi kesalahan', errorMessage, 'error');
                            }
                        });
                    } else {
                        showChatIdUpdateModal();
                    }
                });
            }
            showChatIdUpdateModal();
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#telegramForm').on('submit', function(event) {
                event.preventDefault();
                $('#submitButton').prop('disabled', true);
                var botToken = $('#botToken').val();
                var message = $('#message').val();

                Swal.fire({
                    title: 'Apakah Anda yakin dengan Bot Token ini?',
                    text: 'Bot Token yang dimasukkan akan digunakan untuk mengirim pesan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim pesan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mohon ditunggu, sedang diproses...',
                            text: 'Mengirim pesan ke Telegram...',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        $.ajax({
                            url: "{{ route('storeTelegram') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                botToken: botToken,
                                message: message
                            },
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses!',
                                    text: response.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: true
                                });
                                setTimeout(() => location.reload(), 3000);
                            },
                            error: function(xhr) {
                                Swal.close();
                                var errorMessage = xhr.responseJSON?.message ||
                                    'Terjadi kesalahan. Coba lagi.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 3000);
                            },
                            complete: function() {
                                $('#submitButton').prop('disabled', false);
                            }
                        });
                    } else {
                        $('#submitButton').prop('disabled', false);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.status_batch').change(function() {
                var batchId = $(this).data('id');
                var status = $(this).val();
                $.ajax({
                    url: '/batch/' + batchId + '/update-status',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status_batch: status
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Sedang memperbarui status...',
                            text: 'Mohon tunggu sebentar...',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status berhasil diperbarui!',
                            text: response.message,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan!',
                            text: 'Tidak dapat memperbarui status. Coba lagi.',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            if (window.location.pathname === '/dashboard') {
                $.ajax({
                    url: '/user-session',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        var userName = data.user_name;
                        var hours = new Date().getHours();
                        var greeting = hours < 12 ? "Selamat Pagi" : hours < 15 ? "Selamat Siang" :
                            hours < 18 ? "Selamat Sore" : "Selamat Malam";
                        // Jika ada plugin notify, bisa dipakai. Kalau tidak, cukup console atau Swal kecil.
                        console.log(greeting + ", Hallo " + userName);
                    }
                });
            }
        });
    </script>

    {{-- Script showUploadModal, showEditModal, saveButton, attendanceChart, dll tetap bisa ditambahkan di @stack('scripts') dari view masing-masing --}}
    @stack('scripts')

</body>

</html>
