@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- Form Monitoring (Upload Foto Bukti) --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Upload Bukti Monitoring
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Pesan sukses -->
                            @if (session('success_monitor'))
                                <div class="alert alert-primary">
                                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                        <i class="nc-icon nc-simple-remove"></i>
                                    </button>
                                    <span>
                                        <b> Sukses - </b> {{ session('success_monitor') }}
                                    </span>
                                </div>
                            @endif

                            <!-- Pesan error -->
                            @if ($errors->has('monitor_photo'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('monitor_photo') }}
                                </div>
                            @endif

                            <form id="monitoring-form" action="{{ route('monitoring.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="place_code">Pilih DUDI</label>
                                    <select name="place_code" id="place_code"
                                        class="form-control form-control-lg selectpicker" required>
                                        <option value="">-- Pilih DUDI --</option>
                                        @foreach ($places as $place)
                                            <option value="{{ $place->code }}">{{ $place->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="place_code">Pilih Status</label>
                                    <select name="status" class="form-control form-control-lg selectpicker" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Penerjunan">Penerjunan</option>
                                        <option value="Monitoring 1">Monitoring 1</option>
                                        <option value="Monitoring 2">Monitoring 2</option>
                                        <option value="Monitoring 3">Monitoring 3</option>
                                        <option value="Penarikan">Penarikan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="date">Pilih Tanggal</label>
                                    <input type="date" name="date"
                                        class="form-control form-control-lg selectpicker required">
                                </div>

                                <div class="form-group mt-3">
                                    <label>Bukti Foto</label>
                                    <input type="file" name="monitor_photo" class="form-control" accept="image/*"
                                        onchange="previewImage(event)">
                                    <small class="form-text text-muted">Upload foto kunjungan (bisa pilih kamera atau
                                        galeri)</small>
                                </div>


                                <!-- Preview Foto -->
                                <div id="preview-container" style="margin-top: 15px; display:none;">
                                    <p><b>Preview Foto:</b></p>
                                    <img id="preview-image" src="" alt="Preview Foto"
                                        style="max-width: 300px; border-radius: 10px; border:1px solid #ddd; padding:4px;">
                                </div>

                                <!-- Hidden fields untuk lokasi -->
                                <input type="hidden" name="check_latitude" id="check_latitude">
                                <input type="hidden" name="check_longitude" id="check_longitude">
                                <input type="hidden" name="check_location_link" id="check_location_link">

                                <button type="submit" id="submit-btn" class="btn btn-success btn-fill mt-3" disabled>
                                    <span id="btn-text"><i class="nc-icon nc-camera-20"></i> Upload Foto</span>
                                    <span id="btn-spinner" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true" style="display:none;"></span>
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- Riwayat Monitoring --}}
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header">
                            <h4 class="card-title">Riwayat Monitoring</h4>
                        </div>
                        <div class="card-body table-responsive">
                            @if ($monitorings->isEmpty())
                                <div class="card border-0 shadow-sm mt-3">
                                    <div class="card-body text-center">
                                        <i class="nc-icon nc-camera-20 text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-2 text-muted" style="font-style: italic;">
                                            Belum ada data monitoring
                                        </h5>
                                    </div>
                                </div>
                            @else
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <!-- <th>Pembimbing</th> -->
                                        <th>Dudi</th>
                                        <th>Status</th>
                                        <th>Foto</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($monitorings as $monitor)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $monitor->created_at->format('d-m-Y H:i') }}</td>
                                                <!-- <td>{{ $monitor->mentor->name ?? '-' }}</td> -->
                                                <td>{{ $monitor->internshipPlace->name ?? '-' }}</td>
                                                <td>{{ $monitor->status ?? '-' }}</td>
                                                <td>
                                                    <img src="{{ asset('storage/monitoring/' . basename($monitor->photo)) }}"
                                                        alt="Foto Monitoring" width="100" style="cursor:pointer;"
                                                        onclick="showImage('{{ asset('storage/monitoring/' . basename($monitor->photo)) }}')">
                                                </td>
                                                <td>
                                                    <form id="delete-form-{{ $monitor->id }}"
                                                        action="{{ route('monitoring.destroy', $monitor->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-fill mr-2"
                                                            onclick="confirmDelete({{ $monitor->id }})">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = "block";
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = "none";
        }
    }

    function showImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Foto Monitoring',
            showCloseButton: true,
            showConfirmButton: false,
            width: 'auto',
            background: '#fff',
        });
    }

    // Ambil lokasi otomatis
    document.addEventListener("DOMContentLoaded", function() {
        const latInput = document.getElementById("check_latitude");
        const lngInput = document.getElementById("check_longitude");
        const linkInput = document.getElementById("check_location_link");
        const submitBtn = document.getElementById("submit-btn");

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    latInput.value = lat;
                    lngInput.value = lng;
                    linkInput.value = `https://www.google.com/maps?q=${lat},${lng}`;

                    submitBtn.disabled = false; // aktifkan submit
                },
                function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Diperlukan',
                        text: 'Harap izinkan akses lokasi di browser untuk melanjutkan.',
                        confirmButtonText: 'OK'
                    });
                    submitBtn.disabled = true;
                }
            );
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Geolocation Tidak Didukung',
                text: 'Browser Anda tidak mendukung pengambilan lokasi otomatis.',
            });
            submitBtn.disabled = true;
        }
    });

    // Animasi saat upload + hitung detik
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('monitoring-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // cegah reload langsung

            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');

            btn.disabled = true;
            btnText.innerHTML = 'Uploading...';
            btnSpinner.style.display = 'inline-block';

            Swal.fire({
                title: 'Mengunggah...',
                html: 'Mohon tunggu sebentar, foto sedang dikirim ke server.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            // beri delay agar swal sempat tampil di HP
            setTimeout(() => {
                form.submit();
            }, 800);
        });
    });





    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data monitoring akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
