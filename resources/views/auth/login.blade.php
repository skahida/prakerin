<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Prakerin Tracer | Login</title>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6),
                rgba(0, 0, 0, 0.6)),
            url("{{ asset('../assets/img/banner/smk.jpeg') }}") no-repeat center center/cover;
            min-height: 100dvh;
            /* ✅ lebih stabil di mobile */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            position: relative;
        }

        /* Biar tombol eye bulat di kanan */
        .input-group .btn {
            border-radius: 0 10px 10px 0 !important;
        }


        /* Biar input password bulat di kiri */
        .input-group .form-control {
            border-radius: 10px 0 0 10px !important;
        }



        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 30px 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .login-card img {
            width: 90px;
            margin-bottom: 15px;
        }

        .login-card h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .login-card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
        }

        .btn-login {
            width: 100%;
            border-radius: 10px;
            padding: 12px;
            background: #15a34b;
            color: white;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #0e7a38;
        }

        /* Animasi pulse */
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(21, 163, 75, 0.7);
            }

            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 15px rgba(21, 163, 75, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(21, 163, 75, 0);
            }
        }

        .contact-btn {
            position: fixed;
            bottom: 70px;
            right: 20px;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #15a34b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            z-index: 1000;
            text-decoration: none;

            /* efek animasi */
            animation: pulse 2s infinite;
        }


        .contact-btn:hover {
            transform: scale(1.1);
            background: #0e7a38;
            color: #fff;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            animation: fadeInUp 2s ease-in-out infinite alternate;
        }

        .footer-link {
            color: #15a34b;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
            color: #d1d5db;
            /* abu-abu terang pas hover */
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0.6;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}" alt="Logo" />
        <h4>Welcome to Prakerin Tracer</h4>
        <p>Internship Attendance & Reporting</p>

        <!-- Alert Error -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif


        <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukan Username"
                    value="{{ old('username') }}" autofocus required />
                <div class="invalid-feedback">Username wajib diisi.</div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordField" class="form-control"
                        placeholder="Masukan Password" required />
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fa fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">Password wajib diisi.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>

    <!-- Floating Contact -->
    <a href="https://wa.me/6287897315639" target="_blank" class="contact-btn" title="Hubungi Tim IT">
        <i class="fa fa-headset"></i>
    </a>


    <!-- Footer -->
    <!-- Footer -->
    <div class="footer">
        Website ini dibuat oleh
        <a href="https://skahida.github.io" target="_blank" class="footer-link">
            <strong>SKADEV</strong>
        </a>
    </div>


    <script>
        // Login button loading
        const loginForm = document.getElementById("loginForm");
        const loginBtn = document.getElementById("loginBtn");

        loginForm.addEventListener("submit", function(e) {
            if (!loginForm.checkValidity()) {
                e.preventDefault(); // stop submit
                loginForm.classList.add("was-validated");
                return;
            }

            // ubah tombol jadi loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Loading...
    `;
        });

        // Toggle password visibility
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordField = document.getElementById("passwordField");
            const icon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        });

        // Form validation
        (function() {
            "use strict";
            const form = document.getElementById("loginForm");

            form.addEventListener("submit", function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            }, false);
        })();
    </script>
</body>

</html>