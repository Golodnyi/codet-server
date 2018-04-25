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
use Codet\Http\ProcessingRequest as PRHttp;
use Codet\WebSocket\ProcessingRequest as PRWss;
use Codet\WebSocket\Classes\Clients;
use Klein\Klein;

$dotenv = new Dotenv\Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..']));
$dotenv->load();
$test = [];
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
                $processingRequest = new PRHttp($req, $resp);
            }
        );
    }
);

/**
 * WebSocket
 */
$server->on(
    'open', function ($server, $req) use ($test) { 
        
    }
);

$server->on(
    'message', function ($server, $frame) {
        go(
            function () use ($server, $frame) {
                $processingRequest = new PRWss($server, $frame);
            }
        );
    }
);

$server->on(
    'close', function ($server, $fd) use ($test) {
        Clients::delete($fd);
    }
);

$server->start();
