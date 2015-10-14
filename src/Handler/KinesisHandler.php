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
    public function __construct(KinesisClient $kinesisClient, $streamName, $level = Logger::INFO, $bubble = true)
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

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->kinesisClient->putRecord($content);
        } catch (\Exception $ex) {
            // We intentionally eat exceptions here -- the purpose of this handler is to emit logs to Kinesis for
            // real-time monitoring, not to guarantee delivery of mission critical logs. If Kinesis cannot accept
            // the log at this time, we just drop it on the floor and move on.
            // @TODO: A more durable (but slower) approach to exception handling would be a nice future option
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $kinesisParameters = $this->getFormatter()->formatBatch($records);
        $kinesisParameters['StreamName'] = $this->streamName;

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->kinesisClient->putRecords($kinesisParameters);
        } catch (\Exception $ex) {
            // As above, we intentionally allow logs to drop when Kinesis fails for any reason
        }

        return false === $this->bubble;
    }
}
