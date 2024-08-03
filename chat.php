<?php
require_once __DIR__ . "/open_ai_handler.php";
require_once __DIR__ . "/function.php";

header("Access-Control-Allow-Origin: http://localhost:5180");
header("Access_Control-Allow-Headers: X-Requested-With");


if (isset($_POST["message"])) {

  $message = dataSanitization($_POST["message"]);

  if (!isset($_POST["thread_id"])) {
    $thread_id = '';
  } else {
    $thread_id = dataSanitization($_POST["thread_id"]);
  }

  $env = parse_ini_file('.env');
  $key =  $env['OPEN_AI_KEY'];
  $assistan_id = $env["OPEN_AI_ASSISTAN_ID"];

  $handler = new OpenAIHandler($key, $assistan_id);
  $handler->main($message, $thread_id);

  $thread_id_return = $handler->getThreadIdReturn();
  $message_return = $handler->getMessage_return();

  $data = [
    'threadid' => $thread_id_return,
    'message' =>  $message_return
  ];

  $response_data = [
    'results' => $data,
    'success' => true
  ];

  $json_response = json_encode($response_data);
  header("Content-Type: application/json");

  echo $json_response;
}

// $apiKey = '';

// $assistantId = '';

