<?php
// URL de l'API des stations Vélib'
$url = "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json";

// Récupération du JSON
$json = file_get_contents($url);

// Vérification et décodage en tableau PHP
$data = json_decode($json, true);

// Vérifier que les données existent
if (!$data || !isset($data['data']['stations'])) {
    die("Erreur : impossible de récupérer les données.");
}

// Récupérer la liste des stations
$stations = $data['data']['stations'];
?>