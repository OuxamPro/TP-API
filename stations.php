<?php
// URLs des APIs Vélib'
$info_url = "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json";
$status_url = "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json";

// Récupération des données JSON
$info_json = file_get_contents($info_url);
$status_json = file_get_contents($status_url);

// Vérification et décodage en tableau PHP
$info_data = json_decode($info_json, true);
$status_data = json_decode($status_json, true);

if (!$info_data || !$status_data || !isset($info_data['data']['stations']) || !isset($status_data['data']['stations'])) {
    die("Erreur : impossible de récupérer les données.");
}

// Convertir les disponibilités en un tableau associatif [station_id => données]
$status_map = [];
foreach ($status_data['data']['stations'] as $status) {
    $status_map[$status['station_id']] = $status;
}

// Associer les infos et les disponibilités
$stations = [];
foreach ($info_data['data']['stations'] as $station) {
    $station_id = $station['station_id'];
    $status = $status_map[$station_id] ?? null;
    
    $stations[] = [
    'name' => $station['name'],
    'capacity' => $station['capacity'],
    'lat' => $station['lat'],
    'lon' => $station['lon'],
    'numBikesAvailable' => $status['num_bikes_available'] ?? 0,
    'numEbikeAvailable' => $status['num_bikes_available_types'][0]['ebike'] ?? 0,
    'numMechAvailable' => $status['num_bikes_available_types'][0]['mechanical'] ?? 0,
    'numDocksAvailable' => $status['num_docks_available'] ?? 0
];

}
?>