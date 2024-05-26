<?php

$list_personages = file_get_contents("one-piece.json");

$personages = json_decode($list_personages, true);

// var_dump($personages);

if (isset($_POST["new_character"]) && $_POST["new_character"] === 'new') {
  $new_character = [
    "name" => $_POST["name"],
    "fullName" => $_POST["fullName"],
    "role" => $_POST["role"],
    "wantedPoster" => $_POST["wantedPoster"],
  ];
  $personages[] = $new_character;
  file_put_contents("one-piece.json", json_encode($personages));
}

$response_data = [
  "results" => $personages,
  "numberCharacters" => count($personages),
  "success" => true,
];

$json_response = json_encode($response_data);

header("Content-Type: applicazione/json");

echo $json_response;
