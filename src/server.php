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

$server = new swoole_websocket_server($_ENV['http_host'], $_ENV['http_port'], SWOOLE_BASE);

if ((int)$_ENV['daemonize']) {
    $server->set(
        [
            'daemonize' => true,
            'pid_file' => $_ENV['pid']
        ]
    );
}
/**
 * HTTP
 */
$server->on(
    'request', function ($req, $resp) {
        go(
            function () use ($req, $resp) {
                $processingRequest = new ProcessingRequest($req, $resp);
            }
        );
    }
);

/**
 * WebSocket
 */
$server->on(
    'open', function ($server, $req) {
    }
);

$server->on(
    'message', function ($server, $frame) {
    }
);

$server->on(
    'close', function ($server, $fd) {
    }
);

$server->start();
