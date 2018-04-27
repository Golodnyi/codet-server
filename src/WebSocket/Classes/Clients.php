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

/**
 * WebSocket clients
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
class Clients
{
    private static $_list = [];
    private static $_names = [
        'Niklaus Wirth',
        'Bill Gates',
        'James Gosling',
        'Guido van Rossum',
        'Ken Thompson',
        'Donald Knuth',
        'Brian Kernighan',
        'Tim Berners-Lee',
        'Bjarne Stroustrup',
        'Linus Torvalds',
        'Dennis Ritchie'
    ];

    /**
     * Client list
     *
     * @param boolean $channel channel
     * 
     * @return array
     */
    public static function list($channel = false): array
    {
        if ($channel) {
            if (!isset(self::$_list[$channel])) {
                return [];
            }

            return self::$_list[$channel];
        }
        
        return self::$_list;
    }

    /**
     * Add fd client
     *
     * @param [type] $fd      fd
     * @param [type] $channel channel
     * 
     * @return void
     */
    public static function add($fd, $channel): void
    {
        $name = self::$_names[rand(0, count(self::$_names) -1)];
        // TODO: проверить валидность имени канала
        self::$_list[$channel][] = ['id' => $fd, 'name' => $name];
    }

    /**
     * Delete fd client
     *
     * @param [type] $fd fd
     * 
     * @return bool
     */
    public static function delete($fd): bool
    {
        foreach (self::list() as $channel => $data) {
            foreach ($data as $key => $clientData) {
                if ($clientData['id'] === $fd) {
                    unset(self::$_list[$channel][$key]);
    
                    if (!count(self::$_list[$channel])) {
                        unset(self::$_list[$channel]);
                    }
        
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get client by fd
     *
     * @param [type] $fd fd
     * 
     * @return array
     */
    public static function getClient($fd): array
    {
        foreach (self::list() as $channel => $data) {
            foreach ($data as $key => $clientData) {
                if ($clientData['id'] === $fd) {
                    return self::$_list[$channel][$key];
                }
            }
        }

        return [];
    }

    /**
     * Get channel by fd
     *
     * @param [type] $fd fd
     * 
     * @return string|bool
     */
    public static function getChannel($fd)
    {
        foreach (self::list() as $channel => $data) {
            foreach ($data as $key => $clientData) {
                if ($clientData['id'] === $fd) {
                    return $channel;
                }
            }
        }

        return false;
    }
}
