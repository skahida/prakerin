<div class="sidebar" data-image="../assets/img/banner/smk.jpeg">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ route('dashboard') }}" class="simple-text">
                Prakerin Tracer
            </a>
        </div>
        <ul class="nav">
            {{-- <li>
                <a class="nav-link" href="dashboard.html">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>Dashboard</p>
                </a>
            </li> --}}
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            @if (auth()->user()->role === 'super-admin')
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('class') }}">
                    <i class="nc-icon nc-bank"></i>
                    <p>Kelas</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('department') }}">
                    <i class="nc-icon nc-atom"></i>
                    <p>Jurusan</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('batch') }}">
                    <i class="nc-icon nc-atom"></i>
                    <p>Gelombang</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('student') }}">
                    <i class="nc-icon nc-single-02"></i>
                    <p>Siswa</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('mentor') }}">
                    <i class="nc-icon nc-circle-09"></i>
                    <p>Pembimbing</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dudi') }}">
                    <i class="nc-icon nc-square-pin"></i>
                    <p>Dudi</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('admin') }}">
                    <i class="nc-icon nc-circle-09"></i>
                    <p>Admin</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('telegram') }}">
                    <i class="nc-icon nc-spaceship"></i>
                    <p>Setting Telegram</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('history.presence') }}">
                    <i class="nc-icon nc-notes"></i>
                    <p>Riwayat Presensi</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('report') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Laporan Prakerin</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('student.archive') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Arsip Siswa</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('admin.archive') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Arsip Admin</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('mentor.archive') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Arsip Pembimbing</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('monitoring') }}">
                    <i class="nc-icon nc-camera-20"></i>
                    <p>Monitoring</p>
                </a>
            </li>
            @elseif (auth()->user()->role === 'admin')
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('student') }}">
                    <i class="nc-icon nc-single-02"></i>
                    <p>Siswa</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('monitoring') }}">
                    <i class="nc-icon nc-camera-20"></i>
                    <p>Monitoring</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('history.presence') }}">
                    <i class="nc-icon nc-notes"></i>
                    <p>Riwayat Presensi</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('report') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Laporan Prakerin</p>
                </a>
            </li>
            @elseif (auth()->user()->role === 'student')
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('presence') }}">
                    <i class="nc-icon nc-notes"></i>
                    <p>Riwayat Presensi</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('report') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Laporan Prakerin</p>
                </a>
            </li>
            @elseif (auth()->user()->role === 'mentor')
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('monitoring') }}">
                    <i class="nc-icon nc-camera-20"></i>
                    <p>Monitoring</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('history.presence') }}">
                    <i class="nc-icon nc-notes"></i>
                    <p>Riwayat Presensi</p>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('report') }}">
                    <i class="nc-icon nc-single-copy-04"></i>
                    <p>Laporan Prakerin</p>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>