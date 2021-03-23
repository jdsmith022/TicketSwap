<?php

namespace TicketSwap\Assessment\tests;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$logger = new Logger('MarketplaceTest');
$logger->pushHandler(new StreamHandler(__DIR__.'/debug_logs/logs', Logger::DEBUG));
$logger->error('An error occurred');
