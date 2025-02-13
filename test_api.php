<?php
// URL de l'API Vélib'
$url = "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json";

// Récupération du JSON
$json = file_get_contents($url);

// Décodage en tableau PHP
$data = json_decode($json, true);

// Vérification et affichage des premières stations
if ($data && isset($data['data']['stations'])) {
    echo "<pre>";
    print_r(array_slice($data['data']['stations'], 0, 5)); // Afficher 5 premières stations
    echo "</pre>";
} else {
    echo "Erreur de récupération des données.";
}
?>