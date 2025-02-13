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
    var markers = [];

    // Ajouter les marqueurs sur la carte
    stations.forEach(function(station) {
        var marker = L.marker([station.lat, station.lon]).addTo(map);
        marker.bindPopup(
            "<b>" + station.name + "</b><br>" +
            "Capacité : " + station.capacity + "<br>" +
            "🚲 Vélos disponibles : " + station.numBikesAvailable + "<br>" +
            "🔋 Vélos électriques : " + station.numEbikeAvailable + "<br>" +
            "⚙️ Vélos mécaniques : " + station.numMechAvailable + "<br>" +
            "📍 Bornettes libres : " + station.numDocksAvailable
        );
        markers.push(marker); // Ajouter chaque marqueur au tableau markers
    });

    // Fonction pour filtrer les stations en fonction de la recherche et du type de vélo
    function filterStations() {
        var query = document.getElementById('search').value.toLowerCase();
        var selectedBikeType = document.getElementById('bike-type-filter').value;
        var suggestions = [];
        var filteredMarkers = [];

        // Filtrer les stations en fonction du nom et du type de vélo
        stations.forEach(function(station, index) {
            var stationName = station.name.toLowerCase();
            var isMatchingType = false;

            // Vérifier si le type de vélo correspond
            if (selectedBikeType === "mechanical" && station.numMechAvailable > 0) {
                isMatchingType = true;
            } else if (selectedBikeType === "ebike" && station.numEbikeAvailable > 0) {
                isMatchingType = true;
            } else if (selectedBikeType === "" && (station.numMechAvailable > 0 || station.numEbikeAvailable >
                    0)) {
                isMatchingType = true;
            }

            // Vérifier si la station correspond au filtre de recherche et au type de vélo
            if (stationName.includes(query) && isMatchingType) {
                suggestions.push(station); // Ajouter les suggestions correspondantes
                filteredMarkers.push(markers[index]); // Garder les marqueurs correspondants
            }
        });

        // Afficher les suggestions sous le champ de recherche
        displaySuggestions(suggestions);

        // Mettre à jour la carte avec les marqueurs filtrés
        updateMapWithFilteredMarkers(filteredMarkers);
    }

    // Fonction pour mettre à jour la carte avec les marqueurs filtrés
    function updateMapWithFilteredMarkers(filteredMarkers) {
        markers.forEach(function(marker) {
            marker.setOpacity(0); // Rendre tous les marqueurs invisibles sans les supprimer
        });

        filteredMarkers.forEach(function(marker) {
            marker.setOpacity(1); // Rendre les marqueurs filtrés visibles
        });
    }

    // Fonction pour afficher les suggestions
    function displaySuggestions(suggestions) {
        var suggestionsContainer = document.getElementById('suggestions');
        suggestionsContainer.innerHTML = ""; // Vider les suggestions précédentes

        suggestions.slice(0, 10).forEach(function(station) { // Limiter à 10 suggestions
            var div = document.createElement("div");
            div.classList.add("suggestion-item");
            div.textContent = station.name;
            div.onclick = function() {
                // Zoom sur la station lorsque l'utilisateur clique sur une suggestion
                var marker = markers[stations.indexOf(station)];
                map.setView([station.lat, station.lon], 14); // Zoom sur la station
                marker.openPopup(); // Ouvrir le popup du marqueur
                document.getElementById('search').value = station
                    .name; // Mettre le nom dans le champ de recherche
                suggestionsContainer.innerHTML = ""; // Vider les suggestions après la sélection
            };
            suggestionsContainer.appendChild(div);
        });
    }

    // Ajouter un gestionnaire d'événement pour fermer les suggestions lorsqu'on clique en dehors du champ de recherche
    document.addEventListener('click', function(event) {
        var suggestionsContainer = document.getElementById('suggestions');
        var searchInput = document.getElementById('search');

        // Si le clic se fait en dehors du champ de recherche et de la liste des suggestions, fermer les suggestions
        if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.innerHTML = ''; // Vider les suggestions
        }
    });
    </script>
</body>

</html>