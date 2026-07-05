<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perpindahan Server</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .progress-bar {
    background: linear-gradient(90deg, #16A349, #2ecc71, #16A349);
    background-size: 200% 100%;
    animation: slide 2s linear infinite;
  }
  @keyframes slide {
    0% {background-position: 200% 0;}
    100% {background-position: -200% 0;}
  }

  .fade-in {
    opacity: 0;
    animation: fadeIn 0.5s forwards;
  }
  @keyframes fadeIn {
    to {opacity:1;}
  }

  #countdown {
    animation: pulse 1s infinite;
  }
  @keyframes pulse {
    0%,100% {transform: scale(1);}
    50% {transform: scale(1.05);}
  }

  .bubble {
    position: absolute;
    border-radius: 50%;
    opacity: 0.2;
    background-color: #16A349;
    animation: float 6s infinite ease-in-out;
  }
  @keyframes float {
    0% {transform: translateY(0) translateX(0);}
    50% {transform: translateY(-30px) translateX(20px);}
    100% {transform: translateY(0) translateX(0);}
  }
</style>
</head>
<body class="relative bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center min-h-screen overflow-hidden px-4">

  <!-- Floating bubbles -->
  <div class="bubble w-20 h-20" style="top:10%; left:5%; animation-duration:8s;"></div>
  <div class="bubble w-16 h-16" style="top:70%; left:15%; animation-duration:6s;"></div>
  <div class="bubble w-24 h-24" style="top:30%; left:80%; animation-duration:10s;"></div>
  <div class="bubble w-12 h-12" style="top:50%; left:50%; animation-duration:7s;"></div>

  <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-10 max-w-xl w-full text-center relative z-10 mx-auto">

    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-800 mb-6">
      Perpindahan Server Sedang Berlangsung
    </h1>

    <p class="text-gray-600 mb-6 sm:mb-8 leading-relaxed text-sm sm:text-base">
      Saat ini aplikasi <strong>Prakerin Tracer</strong> sedang dipindahkan dari
      <span class="font-semibold text-green-600">prakerin.skahida.sch.id</span> ke
      <span class="font-semibold text-green-600">prakerin.skahida.my.id</span>.
      Silakan cek kembali beberapa saat lagi.
    </p>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-3 mb-6 overflow-hidden">
      <div class="h-3 progress-bar transition-all duration-500" style="width:0%; border-radius:9999px;"></div>
    </div>

    <p class="text-sm sm:text-base font-medium mb-4 flex items-center justify-center gap-2 text-green-600 fade-in" id="progress-text">
      <span class="text-green-600 animate-pulse">●</span> Memulai proses...
    </p>

    <!-- Countdown -->
    <p class="text-gray-700 font-medium mb-1">Perkiraan selesai dalam:</p>
    <div id="countdown" class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 text-green-600 drop-shadow-sm">
      00:00:00
    </div>

    <p class="text-xs text-gray-500 italic mb-4">
      Perpindahan server akan selesai tepat pukul <span class="font-semibold">06:00 WIB</span>.
    </p>

    <div class="mt-6 text-xs text-gray-400">
      © 2025 SKADEV
    </div>

  </div>

  <script>
    const bar = document.querySelector('.progress-bar');
    const text = document.getElementById('progress-text');
    const countdownEl = document.getElementById("countdown");

    const steps = [
      "Menghubungkan ke server baru...",
      "Migrasi database...",
      "Sinkronisasi file...",
      "Menguji koneksi...",
      "Finalisasi...",
      "Perpindahan selesai!"
    ];

    const now = new Date();
    const target = new Date();
    target.setHours(6,0,0,0); // Set target pukul 06:00 hari ini

    // 🟢 JIKA SUDAH LEWAT JAM 06:00 → langsung redirect
    if (now >= target) {
        window.location.href = "https://prakerin.skahida.my.id";
    }

    // Jika belum jam 06:00 → countdown berjalan
    const totalTime = target - now;

    function update() {
      const current = new Date();
      const diff = target - current;

      if(diff <= 0){
        text.innerHTML = `<span class="text-green-600">✔</span> ${steps[5]}`;
        bar.style.width = "100%";
        window.location.href = "https://prakerin.skahida.my.id";
        return;
      }

      const hours = String(Math.floor(diff / (1000*60*60))).padStart(2,'0');
      const minutes = String(Math.floor((diff / (1000*60)) % 60)).padStart(2,'0');
      const seconds = String(Math.floor((diff/1000)%60)).padStart(2,'0');
      countdownEl.textContent = `${hours}:${minutes}:${seconds}`;

      const progress = ((totalTime - diff) / totalTime) * 100;
      bar.style.width = progress + "%";

      const stepIndex = Math.min(Math.floor(progress / (100 / steps.length)), steps.length-1);
      text.innerHTML = `<span class="text-green-600 animate-pulse">●</span> ${steps[stepIndex]}`;
      text.classList.add('fade-in');
    }

    setInterval(update, 1000);
    update();
  </script>

</body>
</html>
