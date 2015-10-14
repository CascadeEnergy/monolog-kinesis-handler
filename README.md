# Kinesis handler for Monolog

Monolog handler to send messages to AWS Kinesis streams

# Example Usage

```php
<?php

$kinesis = new \Aws\Kinesis\KinesisClient(['region' => 'us-west-2', 'version' => 'latest']);

$kinesisHandler = new \CascadeEnergy\Monolog\Handler\KinesisHandler($kinesis, 'kinesis-stream-name');

$logger = new \Monolog\Logger('log-channel');
$logger->pushHandler($kinesisHandler);

$logger->notice('Off we go to Kinesis.');
```
