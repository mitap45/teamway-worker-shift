<?php


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkerOperationsTest extends WebTestCase
{
    public function testListWorkers()
    {
        //Assuming that server runs on 127.0.0.1:8000 which is default for built in symfony server
        $client = static::createClient([], ['HTTP_HOST' => '127.0.0.1:8000']);

        $client->request('GET', '/worker');

        self::assertResponseIsSuccessful();

    }

}