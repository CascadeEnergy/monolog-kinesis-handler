# Kinesis handler for Monolog

Monolog handler to send messages to AWS Kinesis streams

## Example Usage

```php
<?php

$kinesis = new \Aws\Kinesis\KinesisClient(['region' => 'us-west-2', 'version' => 'latest']);

$kinesisHandler = new \CascadeEnergy\Monolog\Handler\KinesisHandler($kinesis, 'kinesis-stream-name');

$logger = new \Monolog\Logger('log-channel');
$logger->pushHandler($kinesisHandler);

$logger->notice('Off we go to Kinesis.');
```

## Import Usage Notes

This handler is designed for streaming near-real-time monitoring information to systems like DevOps dashboards; it is
not intended to be a mission critical log aggregator. Because of this, the exception handling strategy is currently to
allow logs to simply be dropped.

If the target Kinesis stream cannot be reached or if its throughput is being exceeded this handler makes no attempt to
re-try failed log transmissions. 
