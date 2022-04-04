<?php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkerOperationsTest extends WebTestCase
{
    public function testNewWorker(): void
    {
        //Assuming that server runs on 127.0.0.1:8000 which is default for built in symfony server
        $client = static::createClient([], ['HTTP_HOST' => '127.0.0.1:8000']);

        $client->request('Post', '/worker/new', ['fullName' => 'Ahmet Kılıççç']);

        self::assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertContains('Ahmet Kılıççç', $responseData);
    }

    public function testNewShiftToWorker(): void
    {
        //Assuming that server runs on 127.0.0.1:8000 which is default for built in symfony server
        $client = static::createClient([], ['HTTP_HOST' => '127.0.0.1:8000']);

        //First adding new worker to test the adding of shift to that worker.
        $client->request('Post', '/worker/new', ['fullName' => 'Ahmet Kılıççç']);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $workerId = $responseData['id'];

        $client->request('Post', '/worker/new-shift', ['workerId' => $workerId, 'shift' => '0-8', 'date' => '15-02-2020']);

        self::assertResponseIsSuccessful();

        //Trying with wrong workerId
        $client->request('Post', '/worker/new-shift', ['workerId' => 'someId', 'shift' => '0-8', 'date' => '15-02-2020']);
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());

        $responseMessage = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Worker could not be found with id: someId', $responseMessage);

        //Trying with wrong shift
        $client->request('Post', '/worker/new-shift', ['workerId' => $workerId, 'shift' => 'someShift', 'date' => '15-02-2020']);
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());

        $responseMessage = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Shift must be one of these ["0-8", "8-16", "16-24"]', $responseMessage);

        //Trying with wrong date
        $client->request('Post', '/worker/new-shift', ['workerId' => $workerId, 'shift' => '8-16', 'date' => 'someDate']);
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());

        $responseMessage = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Date must be valid and in d-m-Y format', $responseMessage);

        //Trying with second shift on same date
        $client->request('Post', '/worker/new-shift', ['workerId' => $workerId, 'shift' => '8-16', 'date' => '15-02-2020']);
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());

        $responseMessage = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Specified worker already has shift on this date.', $responseMessage);
    }

}