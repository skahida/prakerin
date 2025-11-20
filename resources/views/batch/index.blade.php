@extends('layouts._app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Gelombang</h4>
                        </div>
                        <div class="card-body">
                            <!-- Pesan sukses -->
                            @if (session('success'))
                                <div class="alert alert-primary">
                                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                        <i class="nc-icon nc-simple-remove"></i>
                                    </button>
                                    <span>
                                        <b> Sukses - </b> {{ session('success') }}
                                </div>
                            @endif

                            <!-- Pesan error untuk seluruh form -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ isset($batch) ? route('batch.update', $batch->id) : route('batch.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($batch))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Gelombang</label>
                                            <input type="text" name="batch_name" class="form-control"
                                                value="{{ old('batch_name', isset($batch) ? $batch->batch_name : '') }}"
                                                placeholder="Masukan Gelombang" autofocus>
                                            @error('batch_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Mulai Prakerin</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date', isset($batch) ? $batch->start_date : '') }}"
                                                placeholder="Masukan Mulai Prakerin">
                                            @error('start_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Selesai Prakerin</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date', isset($batch) ? $batch->end_date : '') }}"
                                                placeholder="Masukan Selesai Prakerin">
                                            @error('end_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Tahun Pelajaran</label>
                                            <input type="text" name="academic_year" class="form-control"
                                                value="{{ old('academic_year', isset($batch) ? $batch->academic_year : '') }}"
                                                placeholder="Tahun Pelajaran">
                                            @error('academic_year')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-fill">
                                    <i class="fa {{ isset($batch) ? 'fa-edit' : 'fa-save' }}"></i>
                                    {{ isset($batch) ? 'Ubah' : 'Simpan' }}
                                </button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-3">Cari Data Gelombang</h4>
                            <form method="GET">
                                <label>Gelombang:</label>
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by Nama Gelombang" value="{{ $search }}">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <button class="btn btn-md btn-info btn-fill" type="submit">
                                            <i class="nc-icon nc-zoom-split"></i> Search
                                        </button>
                                    </div>
                                    <div class="input-group-append ml-2"> <!-- Added margin-left (ml-2) to create space -->
                                        <a href="{{ route('batch') }}" class="btn btn-md btn-primary btn-fill">
                                            <i class="nc-icon nc-refresh-02"></i> Reload
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <h4 class="card-title">Data Gelombang</h4>
                            <p class="card-category">Here is a subtitle for this table</p>
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>ID</th>
                                    <th>Gelombang</th>
                                    <th>Mulai Prakerin</th>
                                    <th>Selesai Prakerin</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Status Aksi</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @foreach ($batches as $batch)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $batch->batch_name }}</td>
                                            <td>{{ $batch->start_date }}</td>
                                            <td>{{ $batch->end_date }}</td>
                                            <td>{{ $batch->academic_year }}</td>
                                            <td>
                                                <!-- Dropdown untuk memilih status -->
                                                <select class="status_batch form-control" data-id="{{ $batch->id }}">
                                                    <option value="active"
                                                        {{ $batch->status_batch == 'active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="non-active"
                                                        {{ $batch->status_batch == 'non-active' ? 'selected' : '' }}>
                                                        Non-Active</option>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="{{ route('batch.edit', $batch->id) }}"
                                                    class="btn btn-warning btn-fill mr-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-fill mr-2 archive-btn"
                                                    data-id="{{ $batch->id }}">
                                                    <i class="fas fa-archive"></i>
                                                </a>

                                                {{-- Tombol tambah detail --}}
                                                <a href="#" class="btn btn-success btn-fill add-detail-btn"
                                                    data-id="{{ $batch->id }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll(".add-detail-btn").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();

                let batchId = this.dataset.id;
                let tempDetails = []; // hanya satu array untuk semua

                Swal.fire({
                    title: 'Tambah Detail Gelombang',
                    width: 800,
                    html: `
                <form id="detail-form">
                    <input type="hidden" name="batch_id" value="${batchId}">
                    
                    <div class="form-group text-left">
                        <label for="mentor_id">Pilih Mentor</label>
                        <select id="mentor_id" class="form-control">
                            <option value="">-- Pilih Mentor --</option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group text-left mt-2">
                        <label for="place_code">Pilih DUDI</label>
                        <select id="place_code" class="form-control">
                            <option value="">-- Pilih DUDI --</option>
                            @foreach ($places as $place)
                                <option value="{{ $place->code }}">{{ $place->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" id="addTempBtn" class="btn btn-sm btn-primary mt-3">
                        + Tambah ke Tabel
                    </button>

                    <hr>
                    <h5>Data Sementara</h5>
                    <table class="table table-bordered" id="tempTable">
                        <thead>
                            <tr>
                                <th>Mentor</th>
                                <th>DUDI</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan ke Database',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        let savedDetails = [];

                        // Ambil data DB via AJAX
                        fetch(`/batch-details/${batchId}/json`)
                            .then(res => res.json())
                            .then(data => {
                                savedDetails = data.map(d => ({
                                    mentor_name: d.mentor.name,
                                    place_name: d.place.name,
                                    mentor_id: d.mentor_id,
                                    place_code: d.place_code,
                                    batch_id: d.internship_batch_id,
                                    id: d.id
                                }));
                                renderTempTable();
                            });

                        document.getElementById("addTempBtn").addEventListener(
                            "click",
                            function() {
                                let mentorId = document.getElementById(
                                    "mentor_id").value;
                                let mentorName = document.getElementById(
                                    "mentor_id").selectedOptions[0].text;
                                let placeCode = document.getElementById(
                                    "place_code").value;
                                let placeName = document.getElementById(
                                    "place_code").selectedOptions[0].text;

                                if (!mentorId || !placeCode) {
                                    Swal.showValidationMessage(
                                        "Mentor dan DUDI wajib dipilih");
                                    return;
                                }

                                tempDetails.push({
                                    batch_id: batchId,
                                    mentor_id: mentorId,
                                    mentor_name: mentorName,
                                    place_code: placeCode,
                                    place_name: placeName
                                });

                                renderTempTable();
                            });

                        function renderTempTable() {
                            let tbody = document.querySelector("#tempTable tbody");
                            tbody.innerHTML = "";

                            let allDetails = [...savedDetails, ...tempDetails];

                            allDetails.forEach((d, index) => {
                                tbody.innerHTML += `
                                <tr>
                                    <td>${d.mentor_name}</td>
                                    <td>${d.place_name}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger removeBtn" data-index="${index}">Hapus</button>
                                    </td>
                                </tr>
                            `;
                            });

                            document.querySelectorAll(".removeBtn").forEach(btn => {
                                btn.addEventListener("click", function() {
                                    let i = this.dataset.index;

                                    if (i >= savedDetails.length) {
                                        // hapus dari tempDetails
                                        tempDetails.splice(i -
                                            savedDetails.length,
                                            1);
                                        renderTempTable();
                                    } else {
                                        // hapus dari DB via AJAX
                                        let detailId = savedDetails[
                                            i].id;
                                        Swal.fire({
                                            title: 'Hapus Data?',
                                            text: "Data ini akan dihapus permanen!",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Ya, hapus',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result
                                                .isConfirmed
                                            ) {
                                                fetch(`/batch-details/${detailId}`, {
                                                        method: 'DELETE',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            'Accept': 'application/json',
                                                            'Content-Type': 'application/json'
                                                        },
                                                    })
                                                    .then(
                                                        res => {
                                                            if (!
                                                                res
                                                                .ok
                                                            )
                                                                throw new Error(
                                                                    `HTTP error! status: ${res.status}`
                                                                );
                                                            return res
                                                                .json();
                                                        })
                                                    .then(
                                                        data => {
                                                            if (data
                                                                .success
                                                            ) {
                                                                Swal.fire(
                                                                        'Berhasil',
                                                                        data
                                                                        .message,
                                                                        'success'
                                                                    )
                                                                    .then(
                                                                        () => {
                                                                            // Hapus dari array local
                                                                            savedDetails
                                                                                .splice(
                                                                                    i,
                                                                                    1
                                                                                );

                                                                            // Cek dulu apakah tbody masih ada sebelum render ulang
                                                                            const
                                                                                tbody =
                                                                                document
                                                                                .querySelector(
                                                                                    "#tempTable tbody"
                                                                                );
                                                                            if (
                                                                                tbody
                                                                            ) {
                                                                                renderTempTable
                                                                                    (); // update tabel hanya jika modal masih terbuka
                                                                            }
                                                                        }
                                                                    );
                                                            } else {
                                                                Swal.fire(
                                                                    'Gagal',
                                                                    data
                                                                    .message,
                                                                    'error'
                                                                );
                                                            }
                                                        })
                                                    .catch(
                                                        err => {
                                                            console
                                                                .error(
                                                                    err
                                                                ); // log error asli ke console
                                                            Swal.fire(
                                                                'Error',
                                                                'Terjadi kesalahan server: ' +
                                                                err
                                                                .message,
                                                                'error'
                                                            );
                                                        });

                                            }
                                        });
                                    }
                                });
                            });
                        }
                    },
                    preConfirm: () => {
                        if (tempDetails.length === 0) {
                            Swal.showValidationMessage(
                                "Minimal 1 detail harus ditambahkan");
                            return false;
                        }
                        return tempDetails;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('batch-details.bulk-store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    details: result.value
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Berhasil!', data.message, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal!', data.message, 'error');
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error!', 'Terjadi kesalahan server',
                                    'error');
                            });
                    }
                });

            });
        });

    });
</script>
