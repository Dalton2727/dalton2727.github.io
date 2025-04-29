<?php

class testGet_UserList extends PHPUnit\Framework\TestCase
{
   protected $client;

   protected function setUp() : void{
      parent::setUp();
      $this->client = new GuzzleHttp\Client(["base_uri" => "http://localhost/"]);
   }

   public function testPost_NewSong() {
      $response = $this->client->request('GET', 'index2.php/user/list');
      $this->assertEquals(200, $response->getStatusCode());
   }

   public function tearDown() : void{
      parent::tearDown();
      $this->client = null;
   }
}
?>