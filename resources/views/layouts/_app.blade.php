<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials._header')
    @include('partials._css')
</head>

<body>
    <!-- Menyisipkan data session dalam elemen HTML -->
    <div id="user-data" data-username="{{ session('name', 'Guest') }}"></div>

    <div class="wrapper">
        @include('partials._sidebar')
        <div class="main-panel">
            @include('partials._navbar')
            @yield('content')
            <!-- Bagian untuk menambahkan script -->
            @stack('scripts') <!-- Menambahkan script dengan push -->
            @include('partials._footer')
        </div>
    </div>
</body>
@include('partials._js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const archiveButtons = document.querySelectorAll('.archive-btn');

        archiveButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const Id = button.getAttribute(
                    'data-id'); // Ambil ID dari atribut data-id

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
                        // Lakukan request untuk arsipkan ke server
                        fetch(`/user/${Id}/archive`, {
                                method: 'GET', // Menggunakan GET atau POST sesuai kebutuhan
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Token CSRF untuk melindungi request
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire('Berhasil!',
                                            'Data berhasil diarsipkan.', 'success')
                                        .then(() => {
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire('Gagal!',
                                        'Terjadi kesalahan saat mengarsipkan data.',
                                        'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.',
                                    'error');
                            });
                    }
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const activateButtons = document.querySelectorAll('.archive-active-btn');

        activateButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = button.getAttribute(
                    'data-id'); // Ambil ID pengguna dari atribut data-id

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
                        // Jika konfirmasi, lakukan request untuk mengaktifkan kembali
                        fetch(`/activate-user/${userId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Pastikan CSRF token disertakan
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
                                        .then(() => {
                                            // Redirect setelah berhasil
                                            location.reload();
                                        });
                                    // Optionally, reload page or update UI to reflect the change
                                } else {
                                    Swal.fire('Gagal!',
                                        'Terjadi kesalahan saat mengaktifkan pengguna.',
                                        'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.',
                                    'error');
                            });
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
        // // Initialize Select2 for the student name dropdown
        // $('.select2').select2({
        //     placeholder: "Cari Data", // Placeholder text
        //     allowClear: true // Allow clearing the selection
        // });

        // // Apply custom CSS to increase the size of the Select2 input
        // $('.select2-container .select2-selection').css({
        //     'font-size': '16px', // Adjust font size
        //     'height': '40px', // Adjust height
        //     'padding': '5px' // Adjust padding for better alignment
        // });

        // // Adjust the dropdown size for the options list
        // $('.select2-container .select2-dropdown').css({
        //     'font-size': '16px' // Adjust font size for dropdown options
        // });

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
        // Fetch data from the route
        fetch('/online-users')
            .then(response => response.json())
            .then(users => {
                // Create an HTML table to display the users' data
                let tableHtml = '<table class="table">';
                tableHtml +=
                    '<thead><tr><th>#</th><th>User</th><th>Status</th><th>Last Activity</th></tr></thead><tbody>';

                // Loop through the users and create a row for each user
                users.forEach((user, index) => {
                    // Convert the Unix timestamp to a Date object
                    let lastActivityTime = new Date(user.last_activity *
                        1000); // Multiply by 1000 to convert to milliseconds
                    let formattedTime = lastActivityTime.toLocaleString(); // Format as locale string

                    tableHtml += `<tr>
                                    <td>${index + 1}</td>
                                    <td>${user.name}</td>
                                    <td><span class="badge badge-primary">Online</span></td>
                                    <td>${formattedTime}</td>
                                  </tr>`;
                });

                tableHtml += '</tbody></table>';

                // Show the table in a SweetAlert modal
                Swal.fire({
                    title: 'Online Users',
                    html: tableHtml,
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: 'Close',
                    width: '80%',
                    customClass: {
                        popup: 'swal-wide',
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
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
    // Script untuk menampilkan SweetAlert form untuk reset password
    document.addEventListener('DOMContentLoaded', function() {
        const resetPasswordButtons = document.querySelectorAll('.reset-password-btn');

        resetPasswordButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = button.getAttribute(
                    'data-id'); // Ambil ID pengguna dari atribut data-id

                console.log(userId);

                Swal.fire({
                    title: 'Reset Password',
                    text: 'Apakah Anda yakin ingin mereset password untuk pengguna ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Reset Password',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        // Lakukan request untuk reset password ke server
                        return fetch(`/reset-password/${userId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Pastikan untuk menyertakan token CSRF
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
                            .catch(error => {
                                Swal.fire('Gagal!',
                                    'Terjadi kesalahan, coba lagi nanti.',
                                    'error');
                            });
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
                showCancelButton: false, // Menghilangkan tombol batal, hanya tombol simpan
                preConfirm: () => {
                    const newPassword = document.getElementById('newPassword').value;

                    // Validasi password: minimal 6 karakter, mengandung huruf, angka, dan simbol
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
                        // Menampilkan animasi loading tanpa tombol OK dan dengan progress
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Silakan tunggu sebentar.',
                            icon: 'info',
                            showConfirmButton: false, // Menghilangkan tombol konfirmasi
                            allowOutsideClick: false, // Tidak bisa menutup SweetAlert dengan klik luar
                            didOpen: () => {
                                Swal.showLoading(); // Menampilkan animasi loading
                            },
                            willClose: () => {
                                // Callback ketika Swal ditutup
                            }
                        });

                        // Proses AJAX untuk mengupdate password
                        $.ajax({
                            url: '/update-password', // Ganti dengan URL atau route yang sesuai
                            method: 'POST',
                            data: {
                                password: newPassword, // Password baru
                                _token: '{{ csrf_token() }}' // Pastikan token CSRF dikirimkan
                            },
                            success: function(response) {
                                // Progres selesai, tampilkan pesan sukses dan refresh
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false, // Tidak menampilkan tombol OK
                                    timer: 1500, // Delay 1.5 detik sebelum redirect
                                    timerProgressBar: true, // Menampilkan progress bar
                                    didOpen: () => {
                                        Swal.showLoading(); // Menampilkan animasi loading
                                        const timer = Swal.getPopup().querySelector("b");
                                        timerInterval = setInterval(() => {
                                            timer.textContent =
                                                `${Swal.getTimerLeft()}`;
                                        }, 100);
                                    },
                                    willClose: () => {
                                        clearInterval(
                                            timerInterval
                                        ); // Menghapus interval saat modal ditutup
                                    }
                                }).then((result) => {
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        console.log("I was closed by the timer");
                                    }

                                    // Redirect otomatis ke halaman logout setelah delay
                                    window.location.href =
                                        '{{ route('
                                                                        logout ') }}'; // Ganti dengan route yang sesuai
                                });

                            },
                            error: function(xhr, status, error) {
                                // Tangani error jika terjadi
                                const errorMessage = xhr.responseJSON.message ||
                                    'Terjadi kesalahan. Silakan coba lagi.';
                                Swal.fire('Terjadi kesalahan', errorMessage, 'error');
                            }
                        });

                    } else {
                        // Jika password kosong atau tidak valid, teruskan tampilkan Swal
                        showPasswordUpdateModal(); // Menampilkan modal kembali jika input tidak valid
                    }
                } else {
                    // Menampilkan Swal kembali jika user menekan ESC atau klik luar
                    showPasswordUpdateModal();
                }
            });
        }

        // Menampilkan modal pertama kali
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
                <p style="font-size: 12px; color: #777; margin-top: 10px;">
                    Setelah Anda mendapatkan Chat ID, harap kembali ke halaman ini untuk melanjutkan.
                </p>
                <p style="font-size: 12px; color: #777; margin-top: 20px;">
                    <strong>Catatan:</strong> Chat ID ini digunakan untuk menerima notifikasi presensi siswa prakerin Anda. Dengan demikian, Anda akan mendapatkan pemberitahuan setiap kali siswa Anda melakukan presensi.
                </p>
                <input type="text" id="chat_id" class="swal2-input" placeholder="Masukkan Chat ID Telegram" autofocus>
            `,
                confirmButtonText: 'Simpan Chat ID',
                showCancelButton: true,
                preConfirm: () => {
                    const chatId = document.getElementById('chat_id').value;
                    if (!chatId) {
                        Swal.showValidationMessage('Chat ID tidak boleh kosong!');
                    }
                    return chatId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const chatId = result.value;

                    // Menampilkan pesan "Tunggu Sebentar" sebelum proses AJAX
                    Swal.fire({
                        title: 'Tunggu Sebentar',
                        text: 'Sedang mengirim data...',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });

                    // Kirim Chat ID ke server menggunakan AJAX
                    $.ajax({
                        url: "{{ route('mentor.updateChatId ') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            chat_id: chatId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Chat ID Berhasil Diperbarui!',
                                text: 'Chat ID Anda telah berhasil disimpan.',
                                timer: 1500,
                                willClose: () => {
                                    window.location.href = "{{ route('logout ') }}";
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            const errorMessage = xhr.responseJSON.message ||
                                'Terjadi kesalahan. Silakan coba lagi.';
                            Swal.fire('Terjadi kesalahan', errorMessage, 'error');
                        }
                    });
                } else {
                    // Jika mentor menekan tombol batal, tampilkan modal kembali
                    showChatIdUpdateModal();
                }
            });
        }

        // Menampilkan modal pertama kali
        showChatIdUpdateModal();
    </script>
@endif


<script>
    $(document).ready(function() {
        $('#telegramForm').on('submit', function(event) {
            event.preventDefault(); // Mencegah form submit biasa

            // Nonaktifkan tombol submit untuk mencegah pengiriman ganda
            $('#submitButton').prop('disabled', true);

            // Ambil nilai dari input
            var botToken = $('#botToken').val();
            var message = $('#message').val();

            // Tampilkan konfirmasi dengan SweetAlert2
            Swal.fire({
                title: 'Apakah Anda yakin dengan Bot Token ini?',
                text: 'Bot Token yang dimasukkan akan digunakan untuk mengirim pesan.',
                icon: 'question',
                showCancelButton: true, // Menampilkan tombol Cancel
                confirmButtonText: 'Ya, kirim pesan!',
                cancelButtonText: 'Batal',
                reverseButtons: true // Membalikkan urutan tombol
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Swal dengan loading message jika user menekan "Ya"
                    Swal.fire({
                        title: 'Mohon ditunggu, sedang diproses...',
                        text: 'Mengirim pesan ke Telegram...',
                        icon: 'info',
                        showConfirmButton: false, // Tidak ada tombol konfirmasi
                        allowOutsideClick: false, // Tidak bisa menutup dengan klik luar
                        didOpen: () => {
                            Swal.showLoading(); // Menampilkan spinner loading
                        }
                    });

                    // Lakukan request AJAX
                    $.ajax({
                        url: "{{ route('storeTelegram') }}", // Route ke controller Telegram (index)
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}", // CSRF token untuk Laravel
                            botToken: botToken,
                            message: message
                        },
                        success: function(response) {
                            // Tutup Swal dan tampilkan pesan sukses
                            Swal.close(); // Menutup Swal loading
                            Swal.fire({
                                icon: 'success', // Ikon yang digunakan
                                title: 'Sukses!',
                                text: response.message,
                                timer: 3000, // Menunggu 3 detik sebelum melakukan reload
                                timerProgressBar: true, // Menampilkan progress bar pada timer
                                showConfirmButton: true // Tidak ada tombol konfirmasi
                            });

                            // Reload halaman setelah 3 detik
                            setTimeout(function() {
                                location.reload(); // Reload halaman
                            }, 3000);
                        },
                        error: function(xhr, status, error) {
                            // Tutup Swal loading jika terjadi error
                            Swal.close(); // Menutup Swal loading
                            // Tampilkan pesan error menggunakan Swal.fire
                            var errorMessage = xhr.responseJSON.message ||
                                'Terjadi kesalahan. Coba lagi.';
                            Swal.fire({
                                icon: 'error', // Ikon yang digunakan
                                title: 'Error!',
                                text: errorMessage,
                                timer: 3000, // Menunggu 3 detik sebelum melakukan reload
                                timerProgressBar: true, // Menampilkan progress bar pada timer
                                showConfirmButton: false // Tidak ada tombol konfirmasi
                            });

                            // Reload halaman setelah 3 detik
                            setTimeout(function() {
                                location.reload(); // Reload halaman
                            }, 3000);
                        },
                        complete: function() {
                            // Re-enable tombol submit setelah proses selesai
                            $('#submitButton').prop('disabled', false);
                        }
                    });
                } else {
                    // Jika user memilih Batal, re-enable tombol submit
                    $('#submitButton').prop('disabled', false);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Ketika dropdown status berubah
        $('.status_batch').change(function() {
            var batchId = $(this).data('id'); // Ambil ID batch
            var status = $(this).val(); // Ambil status yang dipilih

            // Kirim data dengan AJAX
            $.ajax({
                url: '/batch/' + batchId + '/update-status', // Pastikan rutenya sesuai
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token Laravel
                    status_batch: status
                },
                beforeSend: function() {
                    // Menampilkan loading spinner sebelum request
                    Swal.fire({
                        title: 'Sedang memperbarui status...',
                        text: 'Mohon tunggu sebentar...',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function(response) {
                    // Menampilkan pesan sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Status berhasil diperbarui!',
                        text: response
                            .message, // Pastikan response mengirimkan message
                        timer: 3000, // Menunggu 3 detik sebelum menutup Swal
                        timerProgressBar: true
                    });
                },
                error: function(xhr, status, error) {
                    // Menampilkan pesan error jika ada
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan!',
                        text: 'Tidak dapat memperbarui status. Coba lagi.',
                        timer: 3000, // Menunggu 3 detik sebelum menutup Swal
                        timerProgressBar: true
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Cek apakah URL saat ini adalah /dashboard
        if (window.location.pathname === '/dashboard') {
            // Mengambil data session melalui AJAX
            $.ajax({
                url: '/user-session', // URL endpoint yang sudah dibuat
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    var userName = data.user_name; // Mengambil nama pengguna dari respon

                    // Menentukan waktu saat ini
                    var hours = new Date().getHours();
                    var greeting;

                    // Menentukan ucapan sesuai waktu
                    if (hours < 12) {
                        greeting = "Selamat Pagi";
                    } else if (hours < 15) {
                        greeting = "Selamat Siang";
                    } else if (hours < 18) {
                        greeting = "Selamat Sore";
                    } else {
                        greeting = "Selamat Malam";
                    }

                    // Pilih warna acak
                    var color = Math.floor((Math.random() * 4) + 1);

                    // Tampilkan notifikasi dengan ucapan waktu yang sesuai
                    $.notify({
                        icon: "nc-icon nc-notification-70",
                        message: greeting + ", Hallo <b>" + userName + "</b>"
                    }, {
                        type: type[color],
                        timer: 8000,
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                },
                error: function() {
                    console.error('Gagal mengambil data session');
                }
            });
        }
    });
</script>

<script>
    function showUploadModal(weekNumber, reportTitle) {
        var selectedDateView = moment().format('DD-MM-YYYY'); // Example format

        // Show "loading" Swal to indicate "Sedang memuat..."
        const loadingSwal = Swal.fire({
            title: 'Sedang memuat...',
            text: 'Mohon tunggu sebentar...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false, // Prevent closing by clicking outside
            willOpen: () => {
                Swal.showLoading(); // Display loading animation
            }
        });

        // Set a delay before showing the main modal (e.g., 3 seconds)
        setTimeout(() => {
            // After 3 seconds, close the loading Swal and show the upload modal
            loadingSwal.close(); // Close the loading indicator

            // Show the SweetAlert modal for entering social media links
            Swal.fire({
                title: 'Form Pengisian Laporan',
                html: `
                        <div class="form-group mb-3">
                            <label for="reportDate"><b>Tanggal Upload<span style="color: red;">*</span></b></label>
                            <input type="text" class="swal2-input" value="${selectedDateView}" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="reportTitle"><b>Judul Laporan (Minggu ${weekNumber})<span style="color: red;">*</span></b></label>
                            <input type="text" id="reportTitle" value="Minggu ${weekNumber}: Upload Laporan" class="swal2-input" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="reportLink1"><b>Link Video Laporan Via Sosmed <span style="color: red;">*</span></b></label>
                            <input type="text" id="reportLink1" class="swal2-input" placeholder="Masukkan Link Sosmed" autofocus>
                            <small class="form-text text-muted">Contoh link yang benar: https://www.facebook.com/share/v/15xYhj4697</small>
                        </div>
                    `,
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Kirim',
                preConfirm: () => {
                    var reportTitle = $('#reportTitle').val();
                    var reportLink1 = $('#reportLink1').val();
                    // var reportLink2 = $('#reportLink2').val();
                    // var reportLink3 = $('#reportLink3').val();

                    // Validation logic for new report
                    if (!reportTitle || reportTitle.length < 5) {
                        $('#reportTitle').css('border', '2px solid red');
                        Swal.showValidationMessage('Judul Laporan harus lebih dari 5 karakter');
                        return false;
                    } else {
                        $('#reportTitle').css('border', '');
                    }

                    // Regex patterns for social media links
                    // Regex patterns for social media links
                    // var facebookPattern =
                    //     /^https:\/\/(www\.)?facebook\.com\//;
                    // var instagramPattern = /^https:\/\/www\.instagram\.com\//;
                    // var tiktokPattern = /^https:\/\/vt\.tiktok\.com\//;
                    // var tiktokPattern2 =
                    //     /^https:\/\/(www\.)?tiktok\.com\//;
                    // var tiktokPattern3 = /^https:\/\/vm\.tiktok\.com\//;



                    // Validate Facebook link
                    // if (!facebookPattern.test(reportLink1)) {
                    //     $('#reportLink1').css('border', '2px solid red');
                    //     Swal.showValidationMessage('Link Sosmed 1 (Facebook) tidak valid.');
                    //     return false;
                    // } else {
                    //     $('#reportLink1').css('border', '');
                    // }

                    // Validate Instagram link (optional, you can add more checks)
                    // if (reportLink2 && !instagramPattern.test(reportLink2)) {
                    //     $('#reportLink2').css('border', '2px solid red');
                    //     Swal.showValidationMessage('Link Sosmed 2 (Instagram) tidak valid.');
                    //     return false;
                    // } else {
                    //     $('#reportLink2').css('border', '');
                    // }

                    // Validate TikTok link (optional, you can add more checks)
                    // if (reportLink3 && !(tiktokPattern.test(reportLink3) || tiktokPattern2.test(
                    //         reportLink3) || tiktokPattern3.test(
                    //         reportLink3))) {
                    //     $('#reportLink3').css('border', '2px solid red');
                    //     Swal.showValidationMessage('Link Sosmed 3 (TikTok) tidak valid.');
                    //     return false;
                    // } else {
                    //     $('#reportLink3').css('border', '');
                    // }


                    // If all validations pass, proceed to submit the form
                    var formData = {
                        report_title: reportTitle,
                        link1: reportLink1,
                        // link2: reportLink2,
                        // link3: reportLink3,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    // Show "loading" Swal while sending the report
                    Swal.fire({
                        title: 'Sedang mengirim laporan...',
                        text: 'Mohon tunggu sebentar...',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading(); // Show loading animation
                        }
                    });

                    // Set a timeout for 3 seconds to display the loading screen before making the AJAX request
                    setTimeout(function() {
                        // AJAX request to send the data
                        $.ajax({
                            url: '/report', // Your endpoint for handling links
                            method: 'POST',
                            data: formData,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Laporan berhasil dikirim. Terima kasih!',
                                    timer: 2000, // Auto-close after 2 seconds
                                    showConfirmButton: true
                                }).then(function() {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan',
                                    text: 'Gagal mengirim laporan.'
                                });
                            }
                        });
                    }, 3000); // Adjust this timeout duration (3 seconds in this case)
                }
            });
        }, 3000); // Adjust this delay (in milliseconds) before showing the form modal
    }

    function showEditModal(weekNumber, reportTitle) {
        // You can load the existing report data for the specific week
        // Show "loading" Swal to indicate "Sedang memuat..."
        const loadingSwal = Swal.fire({
            title: 'Sedang memuat...',
            text: 'Mohon tunggu sebentar...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false, // Prevent closing by clicking outside
            willOpen: () => {
                Swal.showLoading(); // Display loading animation
            }
        });

        // Set a delay before showing the main modal (e.g., 3 seconds)
        setTimeout(() => {
            // After 3 seconds, close the loading Swal and show the upload modal
            loadingSwal.close(); // Close the loading indicator
            // Example: Make an AJAX call to get the existing report information
            $.ajax({
                url: '/get-report-data', // Replace with your endpoint to get report data
                method: 'GET',
                data: {
                    report_title: reportTitle
                },
                success: function(response) {
                    // Assuming response contains the report data
                    var reportData = response.data;

                    // Show the modal to edit the report
                    Swal.fire({
                        title: 'Edit Laporan Minggu ' + weekNumber,
                        html: `
                    <div class="form-group mb-3">
                        <label for="editReportLink1"><b>Link Video Laporan Via Sosmed<span style="color: red;">*</span></b></label>
                        <input type="text" id="editReportLink1" class="swal2-input" value="${reportData.link1}" placeholder="Masukkan link sosmed" required>
                        <small class="form-text text-muted">Contoh link yang benar: https://www.facebook.com/share/v/15xYhj4697</small>
                    </div>
                `,
                        focusConfirm: false,
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
                        confirmButtonText: 'Kirim',
                        preConfirm: () => {
                            var link1 = $('#editReportLink1').val();
                            // var link2 = $('#editReportLink2').val();
                            // var link3 = $('#editReportLink3').val();

                            // Validate fields (same as before)
                            if (!link1) {
                                Swal.showValidationMessage('Link Sosmed harus diisi');
                                return false;
                            }

                            // Show loading Swal while sending the data
                            Swal.fire({
                                title: 'Sedang mengirim laporan...',
                                text: 'Mohon tunggu sebentar...',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                willOpen: () => {
                                    Swal
                                        .showLoading(); // Show loading animation
                                }
                            });

                            // Create the data for the AJAX request
                            var formData = {
                                report_title: reportTitle,
                                link1: link1,
                                // link2: link2,
                                // link3: link3,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };

                            // Set a timeout for 3 seconds to display the loading screen before making the AJAX request
                            setTimeout(function() {
                                // AJAX request to send the data
                                $.ajax({
                                    url: '/report/edit', // Replace with the correct endpoint for editing
                                    method: 'POST',
                                    data: formData,
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Laporan berhasil diperbarui.',
                                            timer: 2000, // Auto-close after 2 seconds
                                            showConfirmButton: true
                                        }).then(function() {
                                            location
                                                .reload();
                                        });
                                    },
                                    error: function(xhr, status,
                                        error) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Terjadi kesalahan',
                                            text: 'Gagal mengirim laporan.'
                                        });
                                    }
                                });
                            }, 3000);
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal memuat data laporan.'
                    });
                }
            });
        }, 2000);
    }
</script>

<script>
    $('#saveButton').click(function() {
        var data = [];

        // Show the loading message while the AJAX request is in progress
        Swal.fire({
            title: 'Tunggu sebentar...',
            text: 'Sedang menilai...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Loop through each row and collect the values
        $('tr').each(function() {
            var reportId = $(this).find('input[type="number"]').data('report-id');
            var studentId = $(this).find('input[type="number"]').data('student-id');

            var content = $(this).find('input[name="content_' + reportId + '"]').val();
            var videoAppearance = $(this).find('input[name="video_appearance_' + reportId + '"]')
                .val();
            // var creativityInnovation = $(this).find('input[name="creativity_innovation_' +
            //     reportId + '"]').val();
            // var socialMediaUpload = $(this).find('input[name="social_media_upload_' +
            //     reportId + '"]').val();
            // var adherenceToGuidelines = $(this).find(
            //     'input[name="adherence_to_guidelines_' + reportId + '"]').val();

            // Add to data array
            data.push({
                report_id: reportId,
                student_id: studentId,
                content: content,
                video_appearance: videoAppearance,
                // creativity_innovation: creativityInnovation,
                // social_media_upload: socialMediaUpload,
                // adherence_to_guidelines: adherenceToGuidelines
            });
        });

        // Send AJAX request to save the grades
        $.ajax({
            url: "{{ route('saveUpdateGrade') }}",
            method: 'POST',
            data: {
                grades: data,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Hide the loading message
                Swal.close();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Nilai berhasil disimpan!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Set timeout before reloading the page
                    setTimeout(function() {
                        location.reload();
                    }, 500); // 500 ms = 0.5 detik
                });
            },
            error: function(xhr, status, error) {
                // Hide the loading message if error occurs
                Swal.close();

                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                    'Terjadi kesalahan!';
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan!',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>

<script>
    // Variabel global untuk menyimpan instance chart
    var attendanceChartInstance = null;
    var allPresenceTable = []; // 🔥 Tambahkan ini paling atas

    // Fungsi untuk membuat dan merender chart
    function renderChart(labels, presentData, alphaData, izinData, sakitData, liburData) {
        var ctx = document.getElementById('attendanceChart').getContext('2d');

        if (attendanceChartInstance) {
            attendanceChartInstance.destroy();
        }

        attendanceChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Masuk',
                        data: presentData,
                        backgroundColor: 'rgba(41, 167, 69, 1)',
                        borderColor: 'rgba(41, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Alpha',
                        data: alphaData,
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Izin',
                        data: izinData,
                        backgroundColor: 'rgba(23, 162, 184, 0.8)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sakit',
                        data: sakitData,
                        backgroundColor: 'rgba(108, 117, 125, 0.8)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Libur',
                        data: liburData,
                        backgroundColor: 'rgba(166, 52, 227, 0.8)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Fungsi untuk mengambil data absensi dengan filter nama siswa dan batch
    function fetchAttendanceData() {
        currentPage = 1; // <--- tambahin ini
        var studentName = $('#studentFilter').val(); // Ambil nama siswa yang dipilih dari dropdown
        var classCode = $('#classFilter').val();
        var batchName = $('#batchFilter').val(); // Batch yang dipilih
        var startMonth = $('#startMonthFilter').val();
        var endMonth = $('#endMonthFilter').val();
        var startDate = $('#startDateFilter').val();
        var endDate = $('#endDateFilter').val();


        // Jika "All Students" dipilih, kirimkan null atau string kosong untuk parameter filter
        if (studentName === "Semua Siswa") {
            studentName = ""; // Kirimkan string kosong untuk "All Students"
        }

        if (batchName === "Semua Gelombang") {
            batchName = ""; // Kirimkan string kosong untuk "All Students"
        }

        $.ajax({
            url: "{{ route('attendance.data') }}", // URL untuk mengambil data absensi
            method: 'GET',
            data: {
                student_name: studentName, // Kirimkan filter nama siswa ke backend
                class_code: classCode,
                batch_name: batchName, // Kirimkan filter batch ke backend
                start_month: startMonth,
                end_month: endMonth,
                start_date: startDate,
                end_date: endDate,
            },
            success: function(response) {
                var chartData = response.attendanceData; // Ambil data absensi dari response
                var classes = response.classes;
                var students = response.students; // Ambil data siswa untuk mengisi dropdown filter
                var batches = response.batches; // Ambil data batches untuk mengisi dropdown filter
                var yearResult = response.yearResult; // Ambil yearResult
                var batchNameIdentity = response.batchNameIdentity; // Ambil yearResult
                allPresenceTable = response.presenceTable; // <-- ini kuncinya

                var labels = chartData.map(function(item) {
                    return item.label; // Ambil nama siswa sebagai label
                });

                var presentData = chartData.map(item => item.data[0]); // Masuk
                var alphaData = chartData.map(item => item.data[1]); // Alpha
                var izinData = chartData.map(item => item.data[2]); // Izin
                var sakitData = chartData.map(item => item.data[3]); // Sakit
                var liburData = chartData.map(item => item.data[4]); // Sakit

                // Render chart dengan 4 dataset
                renderChart(labels, presentData, alphaData, izinData, sakitData, liburData);



                // Isi dropdown filter dengan daftar siswa
                populateFilterOptions(students, studentName);

                // Isi dropdown filter batch
                populateBatchFilter(batches, batchName);

                // Isi dropdown filter batch
                populateClassFilter(classes, classCode);

                // 🆕 Tambahin ini buat nampilin data di tabel rekap
                if (response.rekapTable) {
                    // Tampilkan yearResult di tempat yang sesuai di frontend, misalnya di elemen dengan ID #yearResult
                    $('#batchName').text(
                        batchNameIdentity
                    );

                    $('#yearResult').text(
                        "Bulan : " + yearResult
                    );

                    populateRekapTable(response.rekapTable);
                }

                if (response.rekapTable && response.rekapTable.length > 0) {
                    $('#printButton').show(); // langsung show
                    var printUrl = "{{ route('print.presence') }}" +
                        '?student=' + encodeURIComponent(studentName || '') +
                        '&class_code=' + encodeURIComponent(classCode || '') +
                        '&batch=' + encodeURIComponent(batchName || '') +
                        '&start_month=' + encodeURIComponent(startMonth || '') +
                        '&end_month=' + encodeURIComponent(endMonth || '') +
                        '&start_date=' + encodeURIComponent(startDate || '') +
                        '&end_date=' + encodeURIComponent(endDate || '');
                    $('#printButtonLink').attr('href', printUrl);
                } else {
                    $('#printButton').hide();
                }


                $('.selectpicker').selectpicker('refresh');
            },
            error: function(error) {
                console.log("Error fetching data", error);
            }
        });
    }

    // Fungsi untuk mengisi dropdown filter dengan nama siswa
    function populateFilterOptions(students, selectedName) {
        var filterSelect = $('#studentFilter');
        filterSelect.empty(); // Kosongkan opsi yang ada

        // Tambahkan opsi "All Students" sebagai default
        filterSelect.append('<option value="Semua Siswa">Semua Siswa</option>');

        // Tambahkan setiap nama siswa sebagai opsi dalam dropdown
        students.forEach(function(student) {
            var selected = (student.name === selectedName) ? 'selected' : ''; // Tandai siswa yang dipilih
            filterSelect.append('<option value="' + student.name + '" ' + selected + '>' + student.name +
                ' | ' +
                student.class_code +
                '</option>');
        });
    }

    function populateBatchFilter(batches, selectedBatch) {
        var batchFilter = $('#batchFilter');
        batchFilter.empty(); // Kosongkan opsi yang ada
        batchFilter.append('<option value="Semua Gelombang">Semua Gelombang</option>');

        batches.forEach(function(batch) {
            var selected = (batch.id == selectedBatch) ? 'selected' : ''; // Tandai batch yang dipilih
            batchFilter.append('<option value="' + batch.id + '" ' + selected + '>' + batch.batch_name +
                ' | TP.' + batch.academic_year + '</option>');
        });
    }

    function populateClassFilter(classes, selectedClass) {
        var classFilter = $('#classFilter');
        classFilter.empty();

        // VALUE KOSONG, BUKAN STRING
        classFilter.append('<option value="">Semua Kelas</option>');

        classes.forEach(function(cls) {
            var selected = (cls.code === selectedClass) ? 'selected' : '';
            classFilter.append(
                '<option value="' + cls.code + '" ' + selected + '>' +
                cls.name +
                '</option>'
            );
        });
    }



    let currentPage = 1; // Track current page
    const rowsPerPage = 5; // Set the number of rows per page

    function paginateData(data) {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        return data.slice(start, end);
    }

    function renderPagination(data) {
        const totalPages = Math.ceil(data.length / rowsPerPage);
        const paginationContainer = document.getElementById('paginationControls');
        paginationContainer.innerHTML = '';

        // Previous Button
        const prevButton = document.createElement('button');
        prevButton.innerHTML = 'Previous';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                populateRekapTable(data); // Re-render the table with new page data
            }
        });
        paginationContainer.appendChild(prevButton);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.innerHTML = i;
            pageButton.classList.toggle('active', i === currentPage);
            pageButton.addEventListener('click', function() {
                currentPage = i;
                populateRekapTable(data); // Re-render the table with new page data
            });
            paginationContainer.appendChild(pageButton);
        }

        // Next Button
        const nextButton = document.createElement('button');
        nextButton.innerHTML = 'Next';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                populateRekapTable(data); // Re-render the table with new page data
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    function populateRekapTable(data) {
        const tableBody = document.querySelector("#filteredTable tbody");
        tableBody.innerHTML = ""; // Clear the table first

        if (data.length === 0) {
            const row = document.createElement("tr");
            row.innerHTML = `<td colspan="10" style="text-align:center;">Tidak ada data</td>`;
            tableBody.appendChild(row);
            return;
        }

        // Paginate the data before displaying it
        const paginatedData = paginateData(data);

        paginatedData.forEach((item, index) => {
            const row = document.createElement("tr");

            row.innerHTML = `
            <td>${(currentPage - 1) * rowsPerPage + index + 1}</td>
            <td>${item.nama || '-'}</td>
            <td>${item.kelas || '-'}</td>
            <td>${item.dudi || '-'}</td>
            <td>${item.pembimbing || '-'}</td>
            <td class="efektif-column">${item.hari_efektif || 0}</td>
            <td class="masuk-column">${item.masuk || 0}</td>
            <td class="sakit-column">${item.sakit || 0}</td>
            <td class="izin-column">${item.izin || 0}</td>
            <td class="alpa-column">${item.alpa || 0}</td>
            <td class="libur-column">${item.libur || 0}</td>
            <td class="lainnya-column">${item.lainnya || 0}</td>
            <td>${item.keterangan || '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary view-detail-btn"
                    data-nama="${item.nama}"
                    data-kelas="${item.kelas}"
                    data-efektif="${item.hari_efektif}"
                    data-masuk="${item.masuk}"
                    data-sakit="${item.sakit}"
                    data-izin="${item.izin}"
                    data-alpa="${item.alpa}"
                    data-libur="${item.libur}"
                    data-lainnya="${item.lainnya}"
                    data-keterangan="${item.keterangan}">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;

            tableBody.appendChild(row);
        });

        // Render pagination controls
        renderPagination(data);
    }


    function openModal() {
        const modal = document.getElementById('customModal');
        modal.style.display = 'block'; // penting: tetap tampil dulu
        // Force reflow biar animasi kebaca
        void modal.offsetWidth;
        modal.classList.add('show');
    }

    function closeModal() {
        const modal = document.getElementById('customModal');
        modal.classList.remove('show');

        // Delay hide sampai animasi selesai
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300); // sesuai dengan durasi animasi CSS
    }

    // Tutup modal kalau klik di luar kontennya
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('customModal');
        if (e.target === modal) {
            closeModal();
        }
    });

    // Handler klik tombol view-detail pakai SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-detail-btn');
            if (!btn) return;

            const nama = btn.dataset.nama;

            // Filter semua data presensi siswa yang dipilih
            const filtered = allPresenceTable.filter(item => item.siswa === nama);

            if (filtered.length === 0) {
                Swal.fire("Data tidak ditemukan", "", "warning");
                return;
            }

            // Buat tabel HTML untuk ditampilkan
            let tableHtml = `
        <div style="max-height:400px; overflow-y:auto; text-align:left;">
            <div style="width:100%; overflow-x:auto;">
                <table class="table table-bordered" style="min-width:900px; font-size:12px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>DUDI</th>
                            <th>Gelombang</th>
                            <th>Tahun Pelajaran</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Lokasi Masuk</th>
                            <th>Lokasi Pulang</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

            filtered.forEach((item, i) => {
                tableHtml += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.siswa}</td>
                    <td>${item.kelas}</td>
                    <td>${item.dudi}</td>
                    <td>${item.gelombang}</td>
                    <td>${item.tahun_pelajaran}</td>
                    <td>${item.hari}</td>
                    <td>${item.tanggal}</td>
                    <td>${item.masuk}</td>
                    <td>${item.pulang}</td>
                    <td>
                        ${item.lokasi_masuk 
                            ? `<iframe width="150" height="100" frameborder="0" style="border:0" src="${item.lokasi_masuk}&output=embed"></iframe>` 
                            : '-'}
                    </td>
                    <td>
                        ${item.lokasi_pulang 
                            ? `<iframe width="150" height="100" frameborder="0" style="border:0" src="${item.lokasi_pulang}&output=embed"></iframe>` 
                            : '-'}
                    </td>
                    <td>${item.status}</td>
                    <td>${item.note}</td>
                    <td>
                        <button class="btn-hapus" data-id="${item.id}" style="color:#fff; background:#e3342f; border:none; padding:4px 8px; border-radius:4px; cursor:pointer;">
                            Hapus
                        </button>
                    </td>
                </tr>
            `;
            });

            tableHtml += `</tbody></table></div></div>`;

            Swal.fire({
                title: 'Data Presensi',
                html: tableHtml,
                width: '95%',
                showCloseButton: true,
                showConfirmButton: false,
                didOpen: () => {
                    document.querySelectorAll('.btn-hapus').forEach(btn => {
                        btn.addEventListener('click', function() {
                            let id = this.dataset.id;

                            Swal.fire({
                                title: "Yakin hapus?",
                                text: "Data ini tidak bisa dikembalikan!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#e3342f",
                                cancelButtonColor: "#6c757d",
                                confirmButtonText: "Ya, Hapus"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    fetch(`/presence/${id}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document
                                                    .querySelector(
                                                        'meta[name="csrf-token"]'
                                                        )
                                                    .getAttribute(
                                                        'content'
                                                        ),
                                                'Accept': 'application/json'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(res => {
                                            if (res.success) {
                                                Swal.fire(
                                                    "Terhapus!",
                                                    res
                                                    .message,
                                                    "success"
                                                    ).then(
                                                () => {
                                                    location
                                                        .reload();
                                                });
                                            } else {
                                                Swal.fire(
                                                    "Gagal!",
                                                    res
                                                    .message,
                                                    "error");
                                            }
                                        })
                                        .catch(err => {
                                            Swal.fire("Error!",
                                                "Terjadi kesalahan server",
                                                "error");
                                        });
                                }
                            });
                        });
                    });
                }
            });
        });
    });




    // Ketika halaman siap, ambil data absensi
    $(document).ready(function() {
        fetchAttendanceData();

        // Ambil data absensi lagi ketika filter siswa atau batch berubah
        $('#studentFilter, #batchFilter, #classFilter, #startMonthFilter, #endMonthFilter, #startDateFilter, #endDateFilter')
            .change(function() {
                fetchAttendanceData();
            });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data presensi akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/presence/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                Swal.fire(
                                    'Dihapus!',
                                    'Data presensi berhasil dihapus.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // reload halaman
                                });
                            } else {
                                Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.',
                                    'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.',
                            'error');
                        });
                }
            });
        });
    });
</script>

</html>
