<?php

class testPost_FailedLogin extends PHPUnit\Framework\TestCase
{
   protected $client;

   protected function setUp() : void{
      parent::setUp();
      $this->client = new GuzzleHttp\Client(["base_uri" => "http://localhost/"]);
   }

   public function testPost_SignUpUser() {
    $response = $this->client->request('POST', 'index2.php/user/login', [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode([
            'username' => 'testuser_',
            'password' => 'Not_Correct',

        ]),
    ]);

    $this->assertEquals(200, $response->getStatusCode());
}

   public function tearDown() : void{
      parent::tearDown();
      $this->client = null;
   }
}
?>