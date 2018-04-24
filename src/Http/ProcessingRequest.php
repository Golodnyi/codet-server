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
 * @link     https://github.com/Golodnyi/codet.git
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
 * @link     https://github.com/Golodnyi/codet.git
 */
class ProcessingRequest extends Request
{
    /**
     * __construct
     *
     * @param [type] $req  request
     * @param [type] $resp response
     */
    public function __construct($req, $resp)
    {
        parent::__construct($req, $resp);
        $router = new Route($req, $resp);
    }
}
