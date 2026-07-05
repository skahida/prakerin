@extends('layouts._app')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        :root {
            --success-color: #2ec4b6;
            --info-color: #00b4d8;
            --warning-color: #ffb703;
            --danger-color: #e63946;
            --dark-blue: #1d3557;
        }

        /* --- STYLE PRELOADER JELAS & MODERN --- */
        #page-preloader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .spinner-loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 180, 216, 0.1);
            border-radius: 50%;
            border-top-color: var(--info-color);
            animation: spin-animation 1s linear infinite;
        }

        @keyframes spin-animation {
            to {
                transform: rotate(360deg);
            }
        }

        .map-container {
            position: relative;
            /* Menjaga preloader tetap terkurung di dalam kontainer map jika diinginkan */
            display: flex;
            height: calc(100vh - 150px);
            min-height: 650px;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .map-sidebar {
            width: 360px;
            background: #ffffff;
            border-right: 1px solid #eef2f5;
            display: flex;
            flex-direction: column;
            z-index: 999;
        }

        .sidebar-header {
            padding: 15px 20px;
            background: #ffffff;
            border-bottom: 1px solid #eef2f5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 12px;
            text-align: center;
        }

        .stat-box {
            padding: 6px 2px;
            border-radius: 8px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .stat-present {
            background: rgba(46, 196, 182, 0.12);
            color: var(--success-color);
        }

        .stat-sick {
            background: rgba(0, 180, 216, 0.12);
            color: var(--info-color);
        }

        .stat-permission {
            background: rgba(255, 183, 3, 0.12);
            color: var(--warning-color);
        }

        .stat-absent {
            background: rgba(230, 57, 70, 0.12);
            color: var(--danger-color);
        }

        .presence-list {
            overflow-y: auto;
            flex: 1;
            background: #fcfcfd;
        }

        .presence-item {
            padding: 14px 20px;
            border-bottom: 1px solid #f4f6f8;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border-left: 4px solid transparent;
        }

        .presence-item:hover {
            background-color: #f1f5f9;
            border-left-color: var(--dark-blue);
        }

        #map {
            flex: 1;
            height: 100%;
        }

        .popup-card {
            font-family: 'Poppins', sans-serif;
            width: 250px;
            padding: 3px;
        }

        .popup-user {
            font-weight: 700;
            font-size: 14px;
            color: var(--dark-blue);
            margin-bottom: 4px;
        }

        .popup-info-table {
            width: 100%;
            margin-top: 8px;
            font-size: 12px;
        }

        .popup-info-table td {
            padding: 3px 0;
            color: #4a5568;
            vertical-align: top;
        }

        .badge-status {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }

        .bg-present {
            background-color: var(--success-color);
            color: #fff;
        }

        .bg-sick {
            background-color: var(--info-color);
            color: #fff;
        }

        .bg-permission {
            background-color: var(--warning-color);
            color: #fff;
        }

        .bg-absent {
            background-color: var(--danger-color);
            color: #fff;
        }
    </style>

    <div class="content">
        <div class="container-fluid" style="padding: 15px;">
            <div class="map-container">

                <div id="page-preloader">
                    <div class="spinner-loader"></div>
                    <span
                        style="margin-top: 15px; font-weight: 600; color: #718096; font-size: 13px; font-family: 'Poppins', sans-serif;">
                        Menyiapkan Data Peta...
                    </span>
                </div>

                <div class="map-sidebar">
                    <div class="sidebar-header">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 style="margin:0; font-weight:700; color: var(--dark-blue);">Live Monitoring</h5>
                            <span class="badge badge-dark" style="border-radius:6px;">{{ $presences->count() }} Log</span>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-box stat-present">
                                Hadir<br>{{ $presences->where('status', 'present')->count() }}
                            </div>
                            <div class="stat-box stat-sick">
                                Sakit<br>{{ $presences->where('status', 'sick')->count() }}
                            </div>
                            <div class="stat-box stat-permission">
                                Izin<br>{{ $presences->where('status', 'permission')->count() }}
                            </div>
                            <div class="stat-box stat-absent">
                                Alfa<br>{{ $presences->where('status', 'absent')->count() }}
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #f8f9fa;">
                                        <i class="fa fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" id="search-student" class="form-control"
                                    style="border-radius: 0 6px 6px 0;" placeholder="Cari nama mahasiswa...">
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #f8f9fa;">
                                        <i class="fa fa-calendar text-muted"></i>
                                    </span>
                                </div>
                                <select id="filter-batch" class="form-control" style="border-radius: 0 6px 6px 0;">
                                    <option value="">Semua Batch & Angkatan</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->batch_name }}
                                            ({{ $batch->academic_year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-6 pr-1">
                                <div class="form-group mb-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="border-radius: 6px 0 0 6px; background: #f8f9fa;">
                                                <i class="fa fa-folder-open text-muted"></i>
                                            </span>
                                        </div>
                                        <select id="filter-class" class="form-control" style="border-radius: 0 6px 6px 0;">
                                            <option value="">Semua Kelas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="form-group mb-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="border-radius: 6px 0 0 6px; background: #f8f9fa;">
                                                <i class="fa fa-building text-muted"></i>
                                            </span>
                                        </div>
                                        <select id="filter-place" class="form-control" style="border-radius: 0 6px 6px 0;">
                                            <option value="">Semua DUDI</option>
                                            @foreach ($places as $place)
                                                <option value="{{ $place->code }}">{{ $place->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="presence-list" id="presence-list">
                        @foreach ($presences as $index => $presence)
                            @php
                                $studentName = $presence->student->name ?? 'Tanpa Nama';
                                $classCode = $presence->student->class_code ?? '';
                                $batchId = $presence->student->internship_batch_id ?? '0';
                                $placeCode = $presence->student->internship_place_code ?? '';
                                $dudi = $presence->student->internshipPlace->name ?? 'DUDI tidak diketahui';
                                $time = \Carbon\Carbon::parse($presence->check_in)->format('d M, H:i');

                                $statusClass = 'bg-present';
                                if ($presence->status == 'sick') {
                                    $statusClass = 'bg-sick';
                                }
                                if ($presence->status == 'permission') {
                                    $statusClass = 'bg-permission';
                                }
                                if ($presence->status == 'absent') {
                                    $statusClass = 'bg-absent';
                                }
                            @endphp
                            <div class="presence-item" data-index="{{ $index }}"
                                data-lat="{{ $presence->check_in_latitude }}"
                                data-lng="{{ $presence->check_in_longitude }}" data-name="{{ strtolower($studentName) }}"
                                data-class="{{ $classCode }}" data-batch="{{ $batchId }}"
                                data-place="{{ $placeCode }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 style="margin:0 0 4px 0; font-weight:600; color:#2c3e50;">{{ $studentName }}</h6>
                                    <small class="text-muted" style="font-size: 11px;">{{ $time }}</small>
                                </div>
                                <p style="margin:0; font-size:12px; color:#7f8c8d;"><i class="fa fa-building-o"></i>
                                    {{ $dudi }}</p>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <span class="badge-status {{ $statusClass }}">{{ $presence->status }}</span>
                                    <small class="text-muted" style="font-size:11px; font-weight:600;"><i
                                            class="fa fa-graduation-cap"></i> {{ $classCode ?: '-' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="map"></div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 1. Setup Base Maps
            var streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            });

            var darkMap = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '© CartoDB'
            });

            var map = L.map('map', {
                zoomControl: false,
                layers: [streetMap]
            }).setView([-2.548926, 118.0148634], 5);

            var baseMaps = {
                "Tampilan Jalan": streetMap,
                "Mode Gelap": darkMap
            };
            L.control.layers(baseMaps, null, {
                position: 'topright'
            }).addTo(map);
            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            // 2. Map JSON Data
            var presencesData = @json($presences);
            var rawClassesData = @json($classes);

            var markers = L.markerClusterGroup();
            var markerList = {};

            function getCustomIcon(status) {
                var iconUrl = 'https://cdn-icons-png.flaticon.com/512/684/684908.png';
                if (status === 'sick') iconUrl = 'https://cdn-icons-png.flaticon.com/512/4311/4311186.png';
                if (status === 'permission') iconUrl = 'https://cdn-icons-png.flaticon.com/512/8157/8157121.png';
                if (status === 'absent') iconUrl = 'https://cdn-icons-png.flaticon.com/512/4812/4812241.png';

                return L.icon({
                    iconUrl: iconUrl,
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -36]
                });
            }

            // 3. Render Marker
            presencesData.forEach(function(presence, index) {
                if (presence.check_in_latitude && presence.check_in_longitude) {
                    var lat = parseFloat(presence.check_in_latitude);
                    var lng = parseFloat(presence.check_in_longitude);

                    var studentName = presence.student ? presence.student.name : 'Unknown';
                    var classCode = presence.student ? presence.student.class_code : '-';
                    var dudiName = (presence.student && presence.student.internship_place) ? presence
                        .student.internship_place.name : '-';
                    var mentorName = (presence.student && presence.student.mentor) ? presence.student.mentor
                        .name : 'Belum Diplot';

                    var badgeColor = 'bg-present';
                    if (presence.status === 'sick') badgeColor = 'bg-sick';
                    if (presence.status === 'permission') badgeColor = 'bg-permission';
                    if (presence.status === 'absent') badgeColor = 'bg-absent';

                    var popupContent = `
                        <div class="popup-card">
                            <div class="popup-user">${studentName}</div>
                            <span class="badge-status ${badgeColor}">${presence.status}</span>
                            <table class="popup-info-table">
                                <tr><td width="30%"><b>Kelas</b></td><td>: ${classCode}</td></tr>
                                <tr><td><b>DUDI</b></td><td>: ${dudiName}</td></tr>
                                <tr><td><b>Mentor</b></td><td>: ${mentorName}</td></tr>
                                <tr><td><b>Waktu</b></td><td>: ${presence.check_in}</td></tr>
                            </table>
                            <div style="margin-top:12px; border-top:1px solid #eee; padding-top:8px; text-align:right;">
                                <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" class="btn btn-xs btn-info" style="font-size:11px; padding:3px 8px; color:#fff; text-decoration:none; background:#00b4d8; border-radius:4px; display:inline-block;">
                                    <i class="fa fa-location-arrow"></i> Google Maps
                                </a>
                            </div>
                        </div>
                    `;

                    var marker = L.marker([lat, lng], {
                        icon: getCustomIcon(presence.status)
                    }).bindPopup(popupContent);
                    markers.addLayer(marker);
                    markerList[index] = marker;
                }
            });

            map.addLayer(markers);

            if (presencesData.length > 0) {
                map.fitBounds(markers.getBounds().pad(0.15));
            }

            // --- JALANKAN PROSES HIDE PRELOADER SETELAH PETA & DATA SELESAI DIMUAT ---
            // Menggunakan event 'tilesloaded' dari Leaflet untuk akurasi rendering aset gambar peta
            streetMap.on('load', hidePreloader);

            // Jaga-jaga jika koneksi internet lambat, preloader otomatis mati dalam 2.5 detik
            var fallbackTimeout = setTimeout(hidePreloader, 2500);

            function hidePreloader() {
                var preloader = document.getElementById('page-preloader');
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    setTimeout(function() {
                        preloader.remove(); // Hapus elemen dari DOM agar menghemat RAM browser
                    }, 500);
                    clearTimeout(fallbackTimeout);
                }
            }

            // 4. Sidebar Klik Event
            var presenceItems = document.querySelectorAll('.presence-item');
            presenceItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    var index = this.getAttribute('data-index');
                    var lat = parseFloat(this.getAttribute('data-lat'));
                    var lng = parseFloat(this.getAttribute('data-lng'));

                    if (lat && lng) {
                        map.flyTo([lat, lng], 16, {
                            animate: true,
                            duration: 1.5
                        });
                        setTimeout(function() {
                            var targetMarker = markerList[index];
                            if (targetMarker) {
                                markers.zoomToShowLayer(targetMarker, function() {
                                    targetMarker.openPopup();
                                });
                            }
                        }, 1200);
                    }
                });
            });

            // 5. GLOBAL MULTI-LEVEL FILTER
            var batchSelect = document.getElementById('filter-batch');
            var classSelect = document.getElementById('filter-class');
            var placeSelect = document.getElementById('filter-place');
            var searchInput = document.getElementById('search-student');

            populateClassOptions();

            batchSelect.addEventListener('change', function() {
                populateClassOptions();
                applyFilters();
            });

            classSelect.addEventListener('change', applyFilters);
            placeSelect.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);

            function populateClassOptions() {
                var selectedBatchId = batchSelect.value;
                classSelect.innerHTML = '<option value="">Semua Kelas</option>';

                var filteredClasses = rawClassesData.filter(function(c) {
                    return selectedBatchId === "" || c.batch_id == selectedBatchId;
                });

                filteredClasses.forEach(function(c) {
                    var option = document.createElement('option');
                    option.value = c.class_code;
                    option.textContent = c.class_code;
                    classSelect.appendChild(option);
                });
            }

            function applyFilters() {
                var searchValue = searchInput.value.toLowerCase();
                var selectedBatch = batchSelect.value;
                var selectedClass = classSelect.value;
                var selectedPlace = placeSelect.value;

                markers.clearLayers();

                presenceItems.forEach(function(item) {
                    var index = item.getAttribute('data-index');
                    var studentName = item.getAttribute('data-name');
                    var studentClass = item.getAttribute('data-class');
                    var studentBatch = item.getAttribute('data-batch');
                    var studentPlace = item.getAttribute('data-place');

                    var matchesSearch = studentName.includes(searchValue);
                    var matchesBatch = selectedBatch === "" || studentBatch === selectedBatch;
                    var matchesClass = selectedClass === "" || studentClass === selectedClass;
                    var matchesPlace = selectedPlace === "" || studentPlace === selectedPlace;

                    if (matchesSearch && matchesBatch && matchesClass && matchesPlace) {
                        item.style.display = 'block';
                        if (markerList[index]) {
                            markers.addLayer(markerList[index]);
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection
