<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>AutoCare Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Welcome to AutoCare</h1>
    <p class="text-center">Find nearby vehicle repair shops in Bulan, Sorsogon.</p>

    <div id="map"></div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Coordinates for Bulan, Sorsogon
    var bulanCoords = [12.6675, 123.8906];

    // Create the map
    var map = L.map('map').setView(bulanCoords, 14);

    // Set OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a sample marker
    L.marker(bulanCoords).addTo(map)
        .bindPopup('Bulan Town Center')
        .openPopup();
</script>

</body>
</html>
