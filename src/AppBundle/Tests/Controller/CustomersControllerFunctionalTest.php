<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomersControllerFunctionalTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testCreateCustomers()
    {
        $customers = [
            ['name' => 'Leandro', 'age' => 26],
            ['name' => 'Marcelo', 'age' => 30],
            ['name' => 'Alex', 'age' => 18],
        ];
        $customers = json_encode($customers);
        $this->client->request('POST', '/customers/', [], [], ['CONTENT_TYPE' => 'application/json'],$customers);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testListCustomers()
    {
        $this->client->request('GET', '/customers/', [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        print_r($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDeleteCustomers()
    {
        $this->client->request('DELETE', '/customers/', [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetCustomers()
    {
        $this->client->request('GET', '/customers/', [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('[]',$this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
