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
namespace Codet\Http\Classes;

/**
 * Abstract request class
 * 
 * PHP version 7
 * 
 * @category PHP
 * @package  Codet
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/codet-app/codet-server
 */
abstract class Request
{
    protected $req;
    protected $resp;

    protected $method;
    protected $uri;

    /**
     * __construct
     *
     * @param [type] $req  request
     * @param [type] $resp response
     */
    public function __construct($req, $resp)
    {
        $this->req = $req;
        $this->resp = $resp;
        $this->setMethod($req->server['request_method']);
        $this->setUri($req->server['request_uri']);
    }

    /**
     * Set method uri
     *
     * @param string $method method
     * 
     * @return void
     */
    public function setMethod(string $method): void
    {
        $this->method = mb_strtoupper($method);
    }

    /**
     * Get method uri
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set uri path
     *
     * @param string $uri uri path
     * 
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = mb_strtolower($uri);
    }

    /**
     * Get uri path
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
