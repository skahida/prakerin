<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Live Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body>
    <h3>Lokasi Saya</h3>
    <div id="map" style="width: 100%; height: 500px;"></div>

    <script>
        // Membuat peta Leaflet
        var map = L.map('map').setView([0, 0], 13);

        // Menambahkan layer peta menggunakan OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Fungsi untuk mendapatkan lokasi terkini menggunakan Geolocation API
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;

                    // Menampilkan posisi pengguna di peta
                    map.setView([lat, lon], 13);

                    // Menambahkan marker di posisi pengguna
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup("Lokasi Anda: " + lat.toFixed(4) + ", " + lon.toFixed(4))
                        .openPopup();
                }, function(error) {
                    alert("Error: " + error.message);
                });
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        }

        // Memanggil fungsi untuk mendapatkan lokasi
        getLocation();
    </script>
</body>

</html>
