<?php

namespace CascadeEnergy\Monolog\Formatter;

use Monolog\Formatter\FormatterInterface;

/**
 * This formatter just JSON encodes the incoming record (or records) then wraps the
 * result in a structure suitable for either a Kinesis PutRecord or PutRecords call
 * (depending on whether a single record or batch of records is being handled).
 */
class KinesisFormatter implements FormatterInterface
{
    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        return [
            'Data' => json_encode($record),
            'PartitionKey' => $record['channel']
        ];
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     *
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $kinesisRecords = [];

        foreach ($records as $record) {
            $kinesisRecords[] = $this->format($record);
        }

        return ['Records' => $kinesisRecords];
    }
}
