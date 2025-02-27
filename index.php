<?php
// Inclure le script qui récupère les stations
require_once 'stations.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stations Vélib' Paris</title>
    <link rel="stylesheet" href="styles.css" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body>
    <h2>Carte des Stations Vélib' Paris</h2>

    <!-- Menu déroulant pour filtrer par type de vélo -->
    <label for="bike-type-filter">Filtrer par type de vélo :</label>
    <select id="bike-type-filter" onchange="filterStations()">
        <option value="">Tous les types de vélos</option>
        <option value="mechanical">Vélos mécaniques</option>
        <option value="ebike">Vélos électriques</option>
    </select>

    <!-- Champ de recherche -->
    <input type="text" id="search" placeholder="Rechercher une station..." oninput="filterStations()" />

    <!-- Conteneur des suggestions -->
    <div id="suggestions"></div>

    <div style="text-align: center; margin-bottom: 20px;">
        <label for="minMech">🔧 Min vélos mécaniques :</label>
        <input type="number" id="minMech" min="0" value="0" style="width: 50px; margin-right: 20px;">

        <label for="minEbike">⚡ Min vélos électriques :</label>
        <input type="number" id="minEbike" min="0" value="0" style="width: 50px;">

        <button onclick="filterStations()" style="margin-left: 20px; padding: 5px 10px;">Filtrer</button>
    </div>

    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
    // Initialisation de la carte centrée sur Paris
    var map = L.map('map').setView([48.8566, 2.3522], 15);

    // Ajouter OpenStreetMap comme fond de carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Charger les données des stations depuis PHP
    var stations = <?php echo json_encode($stations); ?>;
    var markers = [];

    // Fonction pour afficher les stations sur la carte
    function displayStations(filteredStations) {
        // Supprimer les anciens marqueurs
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Ajouter les nouveaux marqueurs filtrés
        filteredStations.forEach(function(station) {
            var marker = L.marker([station.lat, station.lon]).addTo(map);
            marker.bindPopup(
                "<b>" + station.name + "</b><br>" +
                "🚲 Vélos disponibles : " + station.numBikesAvailable + "<br>" +
                "🔋 Vélos électriques : " + station.numEbikeAvailable + "<br>" +
                "⚙️ Vélos mécaniques : " + station.numMechAvailable + "<br>" +
                "📍 Bornettes libres : " + station.numDocksAvailable
            );
            markers.push(marker);
        });
    }

    // Fonction pour filtrer les stations
    function filterStations() {
        var minMech = parseInt(document.getElementById("minMech").value) || 0;
        var minEbike = parseInt(document.getElementById("minEbike").value) || 0;
        var query = document.getElementById("search").value.toLowerCase();
        var selectedBikeType = document.getElementById("bike-type-filter").value;
        var suggestions = [];

        var filteredStations = stations.filter(station => {
            var matchMech = station.numMechAvailable >= minMech;
            var matchEbike = station.numEbikeAvailable >= minEbike;
            var matchQuery = station.name.toLowerCase().includes(query);
            var matchType = true;

            if (selectedBikeType === "mechanical") {
                matchType = station.numMechAvailable > 0;
            } else if (selectedBikeType === "ebike") {
                matchType = station.numEbikeAvailable > 0;
            }

            if (matchQuery) {
                suggestions.push(station);
            }

            return matchMech && matchEbike && matchQuery && matchType;
        });

        displayStations(filteredStations);
        displaySuggestions(suggestions);
    }

    // Fonction pour afficher la liste des suggestions
    function displaySuggestions(suggestions) {
        var suggestionsContainer = document.getElementById("suggestions");
        suggestionsContainer.innerHTML = "";

        suggestions.slice(0, 10).forEach(function(station) { // Limite à 10 suggestions
            var div = document.createElement("div");
            div.classList.add("suggestion-item");
            div.textContent = station.name;
            div.onclick = function() {
                var marker = markers[stations.indexOf(station)];
                map.setView([station.lat, station.lon], 14); // Zoom sur la station
                marker.openPopup(); // Ouvrir le popup
                document.getElementById("search").value = station.name;
                suggestionsContainer.innerHTML = "";
            };
            suggestionsContainer.appendChild(div);
        });
    }

    // Ajouter un gestionnaire d'événement pour fermer les suggestions lorsqu'on clique en dehors du champ de recherche
    document.addEventListener("click", function(event) {
        var suggestionsContainer = document.getElementById("suggestions");
        var searchInput = document.getElementById("search");

        if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.innerHTML = "";
        }
    });

    // Afficher toutes les stations au chargement
    displayStations(stations);
    </script>

</body>

</html>