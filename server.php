<?php
require_once __DIR__ . "/function.php";

header("Access-Control-Allow-Origin: http://localhost:5180");
header("Access_Control-Allow-Headers: X-Requested-With");

$list_personages = file_get_contents("one-piece.json");

$personages = json_decode($list_personages, true);

// var_dump($personages);

if (isset($_POST["new_character"]) && $_POST["new_character"] === 'new') {
  $new_character = [
    "name" => dataSanitization($_POST["name"]),
    "fullName" => dataSanitization($_POST["fullName"]),
    "alias" => dataSanitization($_POST["alias"]),
    "epithet" => dataSanitization($_POST["epithet"]),
    "ability" => dataSanitization($_POST["ability"]),
    "dream" => dataSanitization($_POST["dream"]),
    "role" => dataSanitization($_POST["role"]),
    "bestTechniques" => dataSanitization($_POST["bestTechniques"]),
    "wantedPoster" => dataSanitization($_POST["wantedPoster"]),
    "photo" => dataSanitization($_POST["photo"])
  ];
  $personages[] = $new_character;
  file_put_contents("one-piece.json", json_encode($personages, JSON_PRETTY_PRINT));
}

$response_data = [
  "results" => $personages,
  "numberCharacters" => count($personages),
  "success" => true,
];

$json_response = json_encode($response_data);

header("Content-Type: applicazione/json");

echo $json_response;
