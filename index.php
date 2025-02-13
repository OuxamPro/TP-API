<?php
// Inclure le script qui récupère les stations
require_once 'stations.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stations Vélib' Métropole</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
    }

    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 10px;
    }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
    #map {
        height: 500px;
        width: 80%;
        margin: 20px auto;
        border: 1px solid black;
    }
    </style>

</head>

<body>
    <h2>Carte des Stations Vélib'</h2>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
    // Initialisation de la carte centrée sur Paris
    var map = L.map('map').setView([48.8566, 2.3522], 12);

    // Ajouter OpenStreetMap comme fond de carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Charger les données des stations depuis PHP
    var stations = <?php echo json_encode($stations); ?>;

    // Ajouter les marqueurs sur la carte
    stations.forEach(function(station) {
        var marker = L.marker([station.lat, station.lon]).addTo(map);
        marker.bindPopup(
            "<b>" + station.name + "</b><br>" +
            "Capacité : " + station.capacity
        );
    });
    </script>


</body>

</html>