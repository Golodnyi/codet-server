<?php
/**
 * WebSocket package
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
namespace Codet\WebSocket;

use Codet\WebSocket\Classes\Chat;
use Codet\WebSocket\Classes\Marker;
/**
 * Processing Request
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
class ProcessingRequest
{
    /**
     * __construct
     *
     * @param [type] $server server
     * @param [type] $frame  frame
     */
    public function __construct($server, $frame)
    {
        if (!$data = json_decode($frame->data)) {
            return false;
        }
        switch($data->type) {
        case 0:
                new Chat($server, $frame);
            break;
        case 1:
                new Chat($server, $frame);
            break;
        case 2:
                new Marker($server, $frame);
            break;
        defualt:
            break;

        }
    }
}
