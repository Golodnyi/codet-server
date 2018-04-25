<?php
/**
 * Include autload file
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['..', 'vendor', 'autoload.php']);
use Codet\Http\ProcessingRequest;
use Klein\Klein;

$dotenv = new Dotenv\Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..']));
$dotenv->load();

$http = new swoole_http_server('127.0.0.1', 9501, SWOOLE_BASE);
$http->set(
    [
        'daemonize' => true,
        'pid_file' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'codet-server.pid'
    ]
);

$http->on(
    'request', function ($req, $resp) {
        go(
            function () use ($req, $resp) {
                $processingRequest = new ProcessingRequest($req, $resp);
            }
        );
    }
);

$http->start();
