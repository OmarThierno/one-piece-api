<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use OpenAIHandler as GlobalOpenAIHandler;

class OpenAiHandler {
    private Client $client;
    private string $apiKey;
    private array $headers;
    private array $data;
    private string $threadId;
    private string $runId;
    private string $assistantId;
    private string $message_return;

    public function __construct(string $_apiKey, string $_assistantId)
    {
        $this->client = new Client();
        $this->apiKey = $_apiKey;
        $this->headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $this->apiKey,
            "OpenAI-Beta" => "assistants=v2"
        ];
        $this->threadId = '';
        $this->runId = '';
        $this->assistantId = $_assistantId;
        $this->data = [];
        $this->message_return = '';
    }

    public function getThreadId() {
      $client = $this->client;

      $promise = $client->postAsync('https://api.openai.com/v1/threads', [
        'headers' => $this->headers,
        'json' => $this->data
      ]);

      return $promise->then(
        function($response) {
          $responseBody = json_decode($response->getBody(), true);
          $this->threadId = $responseBody["id"];
          // echo 'Thread ID: ' . $this->threadId . "\n";
          // echo '<br>';

          return $this->threadId;
        },
        function (RequestException $e) {
          // echo 'Error in getThreadId(): ' . $e->getMessage();
          $e->getMessage();
          return null;
        }
      );
    }

    public function postMessages(string $_message, string $_threadId) {
      $this->data = [
        "role" => "user",
        "content" => $_message
      ];

      $client = $this->client;
      $promise = $client->postAsync('https://api.openai.com/v1/threads/'. $_threadId . '/messages', [
        'headers' => $this->headers,
        'json' => $this->data
      ]);

      return $promise->then(
        function($response) {
          $responseBody = json_decode($response->getBody(), true);
          // echo 'Message posted: ' . var_export($responseBody, true) . "\n";
          // echo '<br>';
        },
        function (RequestException $e) {
          // echo 'Error in postMessages()';
          // echo '<br>';
          $e->getMessage();
        }
      );
    }

    public function runThread(string $_threadId) {
      $this->data = [
        "assistant_id" => $this->assistantId,
        "additional_instructions" => null,
        "tool_choice" => null
      ];

      $client = $this->client;
      
      $promise = $client->postAsync('https://api.openai.com/v1/threads/'. $_threadId . '/runs', [
        'headers'=> $this->headers,
        'json'=> $this->data,
      ]);

      return $promise->then(
        function($response) {
          $responseBody = json_decode($response->getBody(), true);
          // echo 'Message runThread: ' . var_export($responseBody, true) . "\n";
          // echo '<br>';

          $this->runId = $responseBody["id"];
          return $this->runId;
        },
        function (RequestException $e) {
          // echo 'Error in postMessages():' . $e->getMessage();
          // echo '<br>';
          $e->getMessage();
        }
      );
    }

    public function pollThreadStatus(string $_threadId, string $_runId) {
      $client = $this->client;
      $promise = $client->getAsync('https://api.openai.com/v1/threads/'. $_threadId . '/runs/'. $_runId, [
        'headers'=> $this->headers,
      ]);

      return $promise->then(
        function($response) use ($_threadId) {
          $responseBody = json_decode($response->getBody(), true);
          $status = $responseBody["status"];

          if($status === 'completed') {
            return $this->getMessages($_threadId);
          } else {
            sleep(1);
            return $this->pollThreadStatus($_threadId, $responseBody["id"]);
          }
        },
        function (RequestException $e) {
          // echo 'Error in pollThreadStatus():' . $e->getMessage();
          // echo '<br>';
          $e->getMessage();
        }
      );
    }

    public function getMessages(string $_threadId) {
      $client = $this->client;
      $promise = $client->getAsync('https://api.openai.com/v1/threads/'. $_threadId . '/messages', [
        'headers'=> $this->headers,
      ]);

      return $promise->then(
        function($response) {
          $responseBody = json_decode($response->getBody(), true);
          // echo 'Message getMessages: ' . var_export($responseBody, true) . "\n";
          // echo '<br>';
          $this->message_return = $responseBody['data'][0]['content'][0]['text']['value'];
          return $responseBody['data'][0]['content'][0]['text']['value'];
        },
        function (RequestException $e) {
          // echo 'Error in getMessages():' . $e->getMessage();
          // echo '<br>';
          $e->getMessage();
        }
      );
    }

    public function main(string $_message, string $_threadId = '') {
      if($_threadId === '' || $_threadId === null) {
        $this->getThreadId()->then(
          function ($threadId) use ($_message) {
            return $this->postMessages($_message, $threadId)->then(
              function () use ($threadId) {
                return $this->runThread($threadId)->then(
                  function($runId) use ($threadId) {
                    if($runId) {
                      return $this->pollThreadStatus($threadId, $runId);
                    }
                  }
                );
              }
            );
          }
        )->wait();
      } else {
        $this->threadId = $_threadId;
        $this->postMessages($_message, $_threadId)->then(
          function () use ($_threadId) {
            return $this->runThread($_threadId)->then(
              function($runId) use ($_threadId) {
                if($runId) {
                  return $this->pollThreadStatus($_threadId, $runId);
                }
              }
            );
          }
        )->wait();
      }
    }

    /**
     * Get the value of message_return
     */ 
    public function getMessage_return()
    {
        return $this->message_return;
    }
    
    public function getThreadIdReturn() {
      return $this->threadId;
    }
}

// $apiKey = '';

// $assistantId = '';

