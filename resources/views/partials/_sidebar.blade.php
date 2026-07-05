<div class="sidebar" data-image="../assets/img/banner/smk.jpeg">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ route('dashboard') }}" class="simple-text">
                Prakerin Tracer
            </a>
        </div>
        <ul class="nav">
            {{-- Dashboard selalu aktif jika route-nya dashboard --}}
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            @if (auth()->user()->role === 'super-admin')
                <li class="nav-item {{ request()->routeIs('class') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('class') }}"><i class="nc-icon nc-bank"></i>
                        <p>Kelas</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('department') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('department') }}"><i class="nc-icon nc-atom"></i>
                        <p>Jurusan</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('batch') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('batch') }}"><i class="nc-icon nc-atom"></i>
                        <p>Gelombang</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('student') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student') }}"><i class="nc-icon nc-single-02"></i>
                        <p>Siswa</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mentor') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('mentor') }}"><i class="nc-icon nc-circle-09"></i>
                        <p>Pembimbing</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('dudi') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dudi') }}"><i class="nc-icon nc-square-pin"></i>
                        <p>Dudi</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin') }}"><i class="nc-icon nc-circle-09"></i>
                        <p>Admin</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('telegram') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('telegram') }}"><i class="nc-icon nc-spaceship"></i>
                        <p>Setting Telegram</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('history.presence') }}"><i class="nc-icon nc-notes"></i>
                        <p>Riwayat Presensi</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('report') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Laporan Prakerin</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('student.archive') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student.archive') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Arsip Siswa</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.archive') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.archive') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Arsip Admin</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mentor.archive') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('mentor.archive') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Arsip Pembimbing</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('monitoring') }}"><i class="nc-icon nc-camera-20"></i>
                        <p>Monitoring</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('map-monitoring.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('map-monitoring.index') }}"><i class="nc-icon nc-pin-3"></i>
                        <p>Peta Monitoring</p>
                    </a>
                </li>
            @elseif (auth()->user()->role === 'admin')
                <li class="nav-item {{ request()->routeIs('student') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student') }}"><i class="nc-icon nc-single-02"></i>
                        <p>Siswa</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('monitoring') }}"><i class="nc-icon nc-camera-20"></i>
                        <p>Monitoring</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('history.presence') }}"><i class="nc-icon nc-notes"></i>
                        <p>Riwayat Presensi</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('report') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Laporan Prakerin</p>
                    </a>
                </li>
            @elseif (auth()->user()->role === 'student')
                <li class="nav-item {{ request()->routeIs('presence') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('presence') }}"><i class="nc-icon nc-notes"></i>
                        <p>Riwayat Presensi</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('report') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Laporan Prakerin</p>
                    </a>
                </li>
            @elseif (auth()->user()->role === 'mentor')
                <li class="nav-item {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('monitoring') }}"><i class="nc-icon nc-camera-20"></i>
                        <p>Monitoring</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('history.presence') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('history.presence') }}"><i class="nc-icon nc-notes"></i>
                        <p>Riwayat Presensi</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('report') }}"><i class="nc-icon nc-single-copy-04"></i>
                        <p>Laporan Prakerin</p>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
