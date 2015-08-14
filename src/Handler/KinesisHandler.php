<?php

namespace CascadeEnergy\Monolog\Handler;

use Aws\Kinesis\KinesisClient;
use CascadeEnergy\Monolog\Formatter\KinesisFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class KinesisHandler extends AbstractProcessingHandler
{
    /** @var KinesisClient */
    private $kinesisClient;

    /** @var string The name of the stream to send log messages to */
    private $streamName;

    /**
     * Constructor.
     *
     * @param KinesisClient $kinesisClient
     * @param string $streamName
     * @param int $level  The minimum logging level at which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(KinesisClient $kinesisClient, $streamName, $level = Logger::CRITICAL, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->kinesisClient = $kinesisClient;
        $this->streamName = $streamName;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        return new KinesisFormatter();
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        $content = $record['formatted'];
        $content['StreamName'] = $this->streamName;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->kinesisClient->putRecord($content);
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $kinesisParameters = $this->getFormatter()->formatBatch($records);
        $kinesisParameters['StreamName'] = $this->streamName;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->kinesisClient->putRecords($kinesisParameters);

        return false === $this->bubble;
    }
}
