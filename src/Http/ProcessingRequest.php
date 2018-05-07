<?php
/**
 * Http package
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
namespace Codet\Http;

use Codet\Http\Classes\Request;

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
class ProcessingRequest extends Request
{
    public function __destruct()
    {
        parent::__destruct();
    }
    /**
     * __construct
     *
     * @param [type] $req  request
     * @param [type] $resp response
     */
    public function __construct($req, $resp)
    {
        parent::__construct($req, $resp);
        new Route($req, $resp);
    }
}
