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
 * WebSocket marker protocol
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
class Marker
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

        if (!in_array($data->type, [2])) {
            return false;
        }
        switch ($data->type) {
        case 2:
            $this->_broadcastMarkers($server, $frame, $data);
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
    private static function _saveMarker($channel, $name, $message, $lineNumber, $column)
    {
        $storageFile = self::$_storage . DIRECTORY_SEPARATOR . mb_substr($channel, 0, 5) . DIRECTORY_SEPARATOR . mb_substr($channel, 5, 8) . '.json';

        if (file_exists($storageFile)) {
            $dataStorageFile = file_get_contents($storageFile);
            if ($array = json_decode($dataStorageFile, true)) {
                $key = false;

                if (isset($array['markers'])) {
                    $key = array_search($lineNumber, array_column($array['markers'], 'lineNumber'));
                }

                if ($key === false) {
                    $array['markers'][] = [
                        'name' => $name,
                        'message' => $message,
                        'lineNumber' => $lineNumber,
                        'column' => $column,
                    ];
                } else {
                    $array['markers'][$key] = [
                        'name' => $name,
                        'message' => $message,
                        'lineNumber' => $lineNumber,
                        'column' => $column,
                    ];
                }
          
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
    private function _broadcastMarkers($server, $frame, $data)
    {
        if (!$client = Clients::getClient($frame->fd)) {
            return false;
        }

        if (!mb_strlen($data->message)) {
            return false;
        } else if (mb_strlen($data->message) > 200) {
            $data->message = mb_substr($data->message, 0, 200);
        }

        self::_saveMarker($data->channel, $client['name'], $data->message, $data->lineNumber, $data->column);

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
                        'type' => 2,
                        'lineNumber' => $data->lineNumber,
                        'column' => $data->column
                    ]
                )
            );
        }
    }
}
