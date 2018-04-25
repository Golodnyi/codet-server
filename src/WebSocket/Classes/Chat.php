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
namespace Codet\WebSocket\Classes;

use Codet\WebSocket\Classes\Clients;

/**
 * WebSocket chat protocol
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
class Chat
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

        if (!in_array($data->type, [0, 1])) {
            return false;
        }
        switch ($data->type) {
        case 0:
            $this->_broadcastMessages($server, $frame, $data);
            break;
        case 1:
            $this->_join($server, $frame, $data);
            break;
        default: 
            break;
        }


    }

    /**
     * Broadcasting messages
     *
     * @param [type] $server server
     * @param [type] $frame  frame
     * @param [type] $data   data
     * 
     * @return void
     */
    private function _broadcastMessages($server, $frame, $data)
    {
        foreach (Clients::list($data->channel) as $clientData) {
            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
            }

            if (!$client = Clients::getClient($frame->fd)) {
                return false;
            }

            $server->push(
                $clientData['id'], json_encode(
                    [
                        'name' => $client['name'],
                        'message' => $data->message,
                        'type' => 0
                    ]
                )
            );
        }
    }

    /**
     * Join to channel
     *
     * @param [type] $server server
     * @param [type] $frame  frame
     * @param [type] $data   data
     * 
     * @return void
     */
    private function _join($server, $frame, $data)
    {
        Clients::add($frame->fd, $data->channel);

        $server->push(
            $frame->fd, json_encode(
                [
                    'result' => 'ok',
                    'type' => 1
                ]
            )
        );

        foreach (Clients::list($data->channel) as $clientData) {

            if ($clientData['id'] === $frame->fd) {
                continue;
            }

            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
            }

            if (!$client = Clients::getClient($frame->fd)) {
                return false;
            }

            $server->push(
                $clientData['id'], json_encode(
                    [
                        'name' => 'System',
                        'message' => $client['name'] . ' join to channel',
                        'type' => 0
                    ]
                )
            );
        }
    }

    /**
     * Leave to channel
     *
     * @param [type] $server server
     * @param [type] $fd     fd
     * 
     * @return void
     */
    public static function leave($server, $fd)
    {
        if (!$channel = Clients::getChannel($fd)) {
            return false;
        }

        foreach (Clients::list($channel) as $clientData) {

            if ($clientData['id'] === $fd) {
                continue;
            }

            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
            }

            if (!$client = Clients::getClient($fd)) {
                return false;
            }

            $server->push(
                $clientData['id'], json_encode(
                    [
                        'name' => 'System',
                        'message' => $client['name'] . ' leave from channel',
                        'type' => 0
                    ]
                )
            );

            Clients::delete($fd);
        }
    }
}
