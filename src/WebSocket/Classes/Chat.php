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
    private static $_storage = __DIR__ . 
        DIRECTORY_SEPARATOR . 
        '..' . 
        DIRECTORY_SEPARATOR . 
        '..' . 
        DIRECTORY_SEPARATOR .
        '..' .
        DIRECTORY_SEPARATOR .
        'storage';

    public function __destruct()
    {
    }

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
     * Save chat message to json file
     *
     * @param [type] $channel channel
     * @param [type] $name    name
     * @param [type] $message message
     * 
     * @return void
     */
    private static function _saveMessage($channel, $name, $message)
    {
        $storageFile = self::$_storage . DIRECTORY_SEPARATOR . mb_substr($channel, 0, 5) . DIRECTORY_SEPARATOR . mb_substr($channel, 5, 8) . '.json';

        if (file_exists($storageFile)) {
            
            $dataStorageFile = file_get_contents($storageFile);
            if ($array = json_decode($dataStorageFile, true)) {
                
                $array['chat'][] = [
                    'name' => $name,
                    'message' => $message,
                ];

                file_put_contents($storageFile, json_encode($array));
            }
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
        if (!$client = Clients::getClient($frame->fd)) {
            return false;
        }

        if (!mb_strlen($data->message)) {
            return false;
        } else if (mb_strlen($data->message) > 256) {
            $data->message = mb_substr($data->message, 0, 256);
        }

        self::_saveMessage($data->channel, $client['name'], $data->message);

        foreach (Clients::list($data->channel) as $clientData) {
            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
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

        if (!$client = Clients::getClient($frame->fd)) {
            return false;
        }

        // self::_saveMessage($data->channel, 'System', $client['name'] . ' join to channel');
        
        foreach (Clients::list($data->channel) as $clientData) {

            if ($clientData['id'] === $frame->fd) {
                continue;
            }

            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
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

        if (!$client = Clients::getClient($fd)) {
            return false;
        }

        // self::_saveMessage($channel, 'System', $client['name'] . ' leave from channel');

        foreach (Clients::list($channel) as $clientData) {

            if ($clientData['id'] === $fd) {
                continue;
            }

            if (!$server->exist($clientData['id'])) {
                Clients::delete($clientData['id']);
                continue;
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
