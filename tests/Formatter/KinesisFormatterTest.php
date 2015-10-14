<?php

namespace CascadeEnergy\Tests\Monolog;

use CascadeEnergy\Monolog\Formatter\KinesisFormatter;

class KinesisFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var KinesisFormatter */
    private $formatter;

    public function setUp()
    {
        $this->formatter = new KinesisFormatter();
    }

    public function testItShouldFormatASingleRecordAsParametersForAKinesisPutRecordCall()
    {
        $record = ['foo' => 'bar', 'channel' => 'channelName'];

        $result = $this->formatter->format($record);

        $this->assertEquals(
            [
                'Data' => json_encode($record),
                'PartitionKey' => 'channelName'
            ],
            $result
        );
    }

    public function testItShouldFormatABatchOfRecordsAsParametersForPutRecords()
    {
        $recordList = [
            ['foo' => 'bar', 'channel' => 'channelNameFoo'],
            ['baz' => 'qux', 'channel' => 'channelNameBaz'],
        ];

        $result = $this->formatter->formatBatch($recordList);

        $this->assertEquals(
            [
                'Records' => [
                    ['Data' => json_encode($recordList[0]), 'PartitionKey' => 'channelNameFoo'],
                    ['Data' => json_encode($recordList[1]), 'PartitionKey' => 'channelNameBaz'],
                ]
            ],
            $result
        );
    }
}
