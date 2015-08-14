<?php

namespace CascadeEnergy\Tests\Monolog;

use CascadeEnergy\Monolog\Handler\KinesisHandler;

class KinesisHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var KinesisHandler */
    private $handler;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $kinesisClient;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $formatter;

    public function setUp()
    {
        $this->kinesisClient = $this
            ->getMockBuilder('Aws\Kinesis\KinesisClient')
            ->setMethods(['putRecord', 'putRecords'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->formatter = $this->getMock('CascadeEnergy\Monolog\Formatter\KinesisFormatter');

        /** @noinspection PhpParamsInspection */
        $this->handler = new KinesisHandler($this->kinesisClient, 'streamName');
    }

    public function testItShouldStoreTheKinesisClientAndStreamName()
    {
        $this->assertAttributeEquals('streamName', 'streamName', $this->handler);
        $this->assertAttributeSame($this->kinesisClient, 'kinesisClient', $this->handler);
    }

    public function testWriteShouldSendASingleKinesisRecord()
    {
        $this->kinesisClient
            ->expects($this->once())
            ->method('putRecord');

        $this->handler->handle(['foo' => 'bar', 'level' => 1000, 'channel' => 'channelName']);
    }

    public function testHandleBatchShouldSendASetOfRecords()
    {
        $this->kinesisClient
            ->expects($this->once())
            ->method('putRecords');

        $this->handler->handleBatch([
            ['foo' => 'bar', 'level' => 1000, 'channel' => 'channelName'],
            ['baz' => 'qux', 'level' => 1000, 'channel' => 'channelName']
        ]);
    }
}
