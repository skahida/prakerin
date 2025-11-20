document.addEventListener('DOMContentLoaded', function() {
    // Ambil tombol presensi masuk dan pulang
    var checkInButton = document.getElementById('checkInPresence');
    var checkOutButton = document.getElementById('checkOutPresence');
    
    // Ambil status presensi masuk dan pulang dari atribut data
    var hasCheckedIn = checkInButton.getAttribute('data-checked-in') === 'true';
    var hasCheckedOut = checkOutButton.getAttribute('data-checked-out') === 'true';
    
    // Nonaktifkan tombol Presensi Masuk jika sudah presensi masuk
    if (hasCheckedIn) {
        checkInButton.disabled = true;
        checkInButton.classList.add('btn-secondary');
        checkInButton.classList.remove('btn-info'); // Ganti warna tombol menjadi abu-abu
    }

    // Nonaktifkan tombol Presensi Pulang jika sudah presensi pulang
    if (hasCheckedOut) {
        checkOutButton.disabled = true;
        checkOutButton.classList.add('btn-secondary');
        checkOutButton.classList.remove('btn-info'); // Ganti warna tombol menjadi abu-abu
    }
});

function updateClock() {
    const now = new Date();
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    // Array of day names
    const daysOfWeek = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const day = daysOfWeek[now.getDay()];  // Get the current day (0 - 6)
    
    const timeString = `Hari ${day}, Pukul ${hours}:${minutes}:${seconds} WIB`;

    // Update the text inside the navbar-brand element
    document.getElementById("clock").textContent = timeString;
}

// Update the clock every 1000 milliseconds (1 second)
setInterval(updateClock, 1000);

// Initialize the clock when the page loads
window.onload = updateClock;

$(document).ready(function() {
    // Membuat peta Leaflet
    var map = L.map('map').setView([0, 0], 13);

    // Menambahkan layer peta menggunakan OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Variabel untuk memeriksa status geolokasi
    let isLocationChecked = false;

    // Fungsi untuk mendapatkan lokasi terkini menggunakan Geolocation API
    function getLocation() {
        if (navigator.geolocation) {
            // Memeriksa apakah lokasi sudah dicek sebelumnya
            if (isLocationChecked) return;

            navigator.geolocation.getCurrentPosition(function(position) {
                isLocationChecked = true;  // Tandai bahwa lokasi sudah diambil
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;

                // Menampilkan posisi pengguna di peta dengan setView untuk memastikan peta terpusat pada lokasi pengguna
                map.setView([lat, lon], 13);

                // Menambahkan marker di posisi pengguna
                L.marker([lat, lon]).addTo(map)
                    .bindPopup("Lokasi Anda: " + lat.toFixed(4) + ", " + lon.toFixed(4))
                    .openPopup();
            }, function(error) {
                // Menangani error geolokasi dengan SweetAlert2
                handleGeolocationError(error);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Geolocation Tidak Didukung',
                text: 'Browser Anda tidak mendukung geolokasi. Pastikan browser Anda terbaru.'
            });
        }
    }

    // Menangani error geolokasi dengan SweetAlert2
    function handleGeolocationError(error) {
        let errorMessage = '';

        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Akses ke lokasi ditolak. Anda perlu memberikan izin akses lokasi.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Lokasi tidak dapat diakses. Coba lagi atau pastikan Anda terhubung ke jaringan.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Permintaan geolokasi melebihi batas waktu. Coba lagi.';
                break;
            case error.UNKNOWN_ERROR:
                errorMessage = 'Terjadi kesalahan tak terduga saat mengakses lokasi.';
                break;
        }

        // Menampilkan pesan error menggunakan SweetAlert2
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: errorMessage,
            footer: 'Untuk mengaktifkan lokasi, buka Pengaturan > Aplikasi > Browser Anda > Izinkan Lokasi.',
            willClose: () => {
                // Setelah swal ditutup, coba lagi untuk mendapatkan lokasi
                getLocation();
            }
        });
    }

    // Memanggil fungsi untuk mendapatkan lokasi
    getLocation();

    // Menambahkan event listener untuk reload halaman jika koneksi internet stabil
    function checkInternetConnection() {
        // Mengecek status koneksi saat pertama kali halaman dimuat
        if (!navigator.onLine) {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Internet Tidak Stabil',
                text: 'Pastikan koneksi internet Anda stabil sebelum melanjutkan.',
                allowOutsideClick: false, // Menghindari klik luar untuk menutup pop-up
                willClose: () => {
                    location.reload(); // Setelah pesan error, coba lagi untuk mendapatkan lokasi
                }
            });
            return false; // Menghentikan proses jika internet tidak stabil
        }

        // Cek ketika status 'online' berubah (koneksi kembali)
        window.addEventListener('online', function() {
            // Coba ulang untuk mendapatkan lokasi dan reload halaman setelah koneksi kembali
            location.reload(); // Reload halaman untuk memastikan segalanya diperbarui
        });

        return true;
    }

    // Pastikan koneksi internet terdeteksi setelah halaman di-load
    checkInternetConnection();

    // Menambahkan event listeners untuk proses check-in dan check-out dengan geolokasi
    $('#checkInPresence').on('click', function() {
        if (!checkInternetConnection()) return; // Memastikan koneksi internet stabil

        const button = $(this); // Simpan tombol ke variabel
        button.prop('disabled', true); // Menonaktifkan tombol

        // Menampilkan Swal untuk indikator "Sedang memproses..."
        const loadingSwal = Swal.fire({
            title: 'Sedang memproses...',
            text: 'Tunggu sebentar, kami sedang mencatat lokasi Anda...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,  // Tidak bisa menutup dengan klik luar
            willOpen: () => {
                Swal.showLoading();  // Menampilkan animasi loading
            }
        });

        // Geolokasi untuk check-in
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const _token = $('meta[name="csrf-token"]').attr('content');

                // Validasi: pastikan latitude dan longitude bukan null
                if (latitude === null || longitude === null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Geolocation tidak ditemukan',
                        text: 'Mohon pastikan geolocation diaktifkan di perangkat Anda.',
                    });
                    loadingSwal.close(); // Tutup Swal loading
                    button.prop('disabled', false); // Aktifkan kembali tombol
                    return;
                }

                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: checkInUrl,
                    type: 'POST',
                    data: {
                        _token: _token,
                        check_in_latitude: latitude,
                        check_in_longitude: longitude
                    },
                    success: function(response) {
                        // Jika berhasil, tampilkan pesan sukses dan langsung reload halaman
                        Swal.fire({
                            icon: 'success',
                            title: 'Presensi Masuk Berhasil!',
                            text: 'Lokasi Anda telah berhasil dicatat.',
                            timer: 1500,  // Menambahkan waktu delay sebelum halaman di-reload
                            willClose: () => {
                                location.reload();  // Halaman akan di-reload setelah Swal ditutup
                            }
                        });
                    },
                     error: function(xhr, status, error) {
                        let errorMessage = 'Ada masalah saat melakukan presensi masuk, coba lagi nanti.';

                        // Coba ambil pesan error dari respon JSON server
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Presensi',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        loadingSwal.close(); // Menutup Swal loading setelah proses selesai
                        button.prop('disabled', false); // Aktifkan kembali tombol setelah AJAX selesai
                    }
                });
            }, function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocation Gagal',
                    text: 'Tidak dapat mengakses lokasi Anda, pastikan geolocation diaktifkan.',
                });
                loadingSwal.close(); // Tutup Swal loading
                button.prop('disabled', false); // Aktifkan kembali tombol
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Geolocation Tidak Didukung',
                text: 'Perangkat Anda tidak mendukung geolokasi.',
            });
            loadingSwal.close(); // Tutup Swal loading
            button.prop('disabled', false); // Aktifkan kembali tombol
        }
    });

    // Proses yang sama untuk check-out
    $('#checkOutPresence').on('click', function() {
        if (!checkInternetConnection()) return;

        const button = $(this);
        button.prop('disabled', true);

        // Menampilkan indikator loading
        const loadingSwal = Swal.fire({
            title: 'Sedang memproses...',
            text: 'Tunggu sebentar, kami sedang mencatat lokasi Anda...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const _token = $('meta[name="csrf-token"]').attr('content');

                // Validasi koordinat
                if (latitude === null || longitude === null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Geolocation tidak ditemukan',
                        text: 'Mohon pastikan geolocation diaktifkan di perangkat Anda.',
                    });
                    loadingSwal.close();
                    button.prop('disabled', false);
                    return;
                }

                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: checkOutUrl,
                    type: 'POST',
                    data: {
                        _token: _token,
                        check_out_latitude: latitude,
                        check_out_longitude: longitude
                    },
                    success: function(response) {
                        // Jika berhasil, tampilkan pesan sukses dan reload halaman
                        Swal.fire({
                            icon: 'success',
                            title: 'Presensi Pulang Berhasil!',
                            text: 'Lokasi Anda telah berhasil dicatat.',
                            timer: 1500,
                            willClose: () => {
                                location.reload();
                            }
                        });
                    },
                     error: function(xhr, status, error) {
                        let errorMessage = 'Ada masalah saat melakukan presensi pulang, coba lagi nanti.';

                        // Coba ambil pesan error dari respon JSON server
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Presensi',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        loadingSwal.close();
                        button.prop('disabled', false);
                    }
                });
            }, function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocation Gagal',
                    text: 'Tidak dapat mengakses lokasi Anda.',
                });
                loadingSwal.close();
                button.prop('disabled', false);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Geolocation Tidak Didukung',
                text: 'Perangkat Anda tidak mendukung geolokasi.',
            });
            loadingSwal.close();
            button.prop('disabled', false);
        }
    });
});

// Swal.fire({
//     title: 'Login Berhasil!',
//     text: 'Silakan perbarui password Anda untuk melanjutkan.',
//     icon: 'success',
//     showCancelButton: false, // Menonaktifkan tombol cancel
//     showConfirmButton: false, // Menonaktifkan tombol OK
//     allowOutsideClick: false, // Menonaktifkan klik di luar untuk menutup swal
//     allowEscapeKey: false, // Menonaktifkan ESC untuk menutup swal
//     didOpen: () => {
//       // Tampilkan form untuk mengganti password
//       Swal.fire({
//         title: 'Perbarui Password',
//         html: `
//           <input type="password" id="newPassword" class="swal2-input" placeholder="Masukkan password baru">
//         `,
//         confirmButtonText: 'Simpan',
//         showCancelButton: true,
//         cancelButtonText: 'Batal',
//         preConfirm: () => {
//           const newPassword = document.getElementById('newPassword').value;
//           if (!newPassword) {
//             Swal.showValidationMessage('Password tidak boleh kosong!');
//           }
//           return newPassword;
//         }
//       }).then((result) => {
//         if (result.isConfirmed) {
//           // Proses untuk mengganti password (misalnya panggil API)
//           Swal.fire({
//             icon: 'success',
//             title: 'Password Berhasil Diperbarui!',
//             text: 'Password Anda telah berhasil diperbarui.',
//             showConfirmButton: true,
//           });
//         }
//       });
//     }
// });
  