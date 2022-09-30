<?php

namespace MasterKelas;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

/**
 * Master Log
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MasterLog {

  public static function queue() {
    // $dateFormat = "Y-m-d H:i:s";
    // $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
    // $formatter = new LineFormatter($output, $dateFormat);
    // $formatter->includeStacktraces(true);

    $error_stream = new RotatingFileHandler(PRIVATE_STORAGE_DIR . '/log/queue/error.log', 10, Logger::ERROR);
    $debug_stream = new RotatingFileHandler(PRIVATE_STORAGE_DIR . '/log/queue/debug.log', 3);

    $log = new Logger('queue');
    $log->pushHandler($error_stream);
    $log->pushHandler($debug_stream);
    return $log;
  }
}
