<!-- Fonts and icons -->
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
<!-- CSS Files -->
<link href="{{ asset('../assets/css/bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ asset('../assets/css/light-bootstrap-dashboard.css?v=2.0.0') }}" rel="stylesheet" />
<!-- CSS Just for demo purpose, don't include it in your project -->
<link href="{{ asset('../assets/css/demo.css') }}" rel="stylesheet" />
{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('../assets/css/style.css') }}">
{{-- leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
{{-- SweetAlert 2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- FullCalendar CSS (v3.x) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
{{-- font awesome 6 --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<!-- Include Select2 CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" /> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select/dist/css/bootstrap-select.min.css">
<style>
    /* Gradient sidebar */
    .sidebar:after,
    body>.navbar-collapse:after {
        background: linear-gradient(to bottom, #15a34b 0%, #1fb258 100%);
        background-size: 150% 150%;
        z-index: 3;
        opacity: 1;
    }

    /* Custom Menu */
    .custom-menu {
        display: flex;
        gap: 16px;
        background-color: #f9f9f9;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        justify-content: center;
        flex-wrap: wrap;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        background: #ffffff;
        padding: 10px 18px;
        border-radius: 10px;
        color: #444;
        font-weight: 500;
        transition: 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.07);
        position: relative;
    }

    .menu-item .badge {
        background: #15a34b;
        color: #fff;
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 12px;
    }

    .menu-item:hover {
        background: #15a34b;
        color: #fff;
        transform: translateY(-2px);
    }

    .menu-item:hover .badge {
        background: #fff;
        color: #15a34b;
    }

    .menu-item.active {
        background: linear-gradient(135deg, #15a34b, #4e9e4d);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .menu-item.active .badge {
        background: white;
        color: #15a34b;
    }

    /* Animasi lompat emojiText */
    @keyframes jump {
        0% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-15px);
        }

        60% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(0);
        }
    }

    .jump {
        animation: jump 0.5s ease;
    }

    /* Footer */
    .app-version-footer {
        background-color: #f0f0f0;
        color: #6c757d;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        padding: 12px 10px;
        border-top: 1px solid #d6d6d6;
        border-radius: 0 0 8px 8px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.8;
    }

    .app-version-footer i {
        color: #adb5bd;
        font-size: 12px;
    }

    /* Custom Modal */
    #customModal {
        opacity: 0;
        transform: scale(0.9);
        pointer-events: none;
        transition: all 0.3s ease;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background: rgba(0, 0, 0, 0.4);
        z-index: 9999;
    }

    #customModal .modal-content {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
        transition: all 0.3s ease;
        margin: 5% auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        max-width: 800px;
        position: relative;
    }

    #customModal.show {
        opacity: 1;
        pointer-events: auto;
    }

    #customModal.show .modal-content {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Custom Modal Box */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .custom-modal-content {
        background-color: #fff;
        margin: 20px auto;
        padding: 0;
        border-radius: 8px;
        width: 95%;
        max-width: 1200px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .custom-modal-header {
        background-color: #007bff;
        color: white;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .custom-close {
        font-size: 1.5rem;
        cursor: pointer;
    }

    .custom-modal-body {
        padding: 1rem;
    }

    /* Table Custom */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th,
    .table-custom td {
        border: 1px solid #dee2e6;
        padding: 0.5rem;
        text-align: center;
    }

    .table-custom thead {
        background-color: #f8f9fa;
    }

    /* Column Coloring */
    .masuk-column {
        background-color: #28a745;
        color: white;
    }

    .efektif-column {
        background-color: #856404;
        color: white;
    }

    .izin-column {
        background-color: #14a2b8;
        color: white;
    }

    .sakit-column {
        background-color: #ffc007;
        color: white;
    }

    .alpa-column {
        background-color: #dc3343;
        color: white;
    }

    .libur-column {
        background-color: #ba5fea;
        color: white;
    }

    .lainnya-column {
        background-color: #858e96;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #customModal {
            padding: 0 15px;
        }

        #customModal .modal-content {
            width: 100%;
            margin: 0;
            border-radius: 8px;
            padding: 1rem;
        }
    }
</style>