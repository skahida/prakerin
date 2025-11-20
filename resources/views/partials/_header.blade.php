<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Menambahkan link ke manifest -->
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}">
<link rel="icon" type="image/png" href="{{ asset('../assets/img/logo/logo-removebg-preview.png') }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>Prakerin Tracer | {{ $title }}</title>
<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
    name='viewport' />
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.7/dist/countUp.min.js"></script>