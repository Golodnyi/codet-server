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
use Codet\Http\Routing\Auth;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Exception;

/**
 * Route class
 * 
 * @category Route
 * @package  Http
 * @author   Golodnyi <ochen@golodnyi.ru>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     none
 */
class Route extends Request
{
    private $_dispatcher;

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

        $this->_dispatcher = \FastRoute\simpleDispatcher(
            function (RouteCollector $r) {
                $r->addGroup(
                    '/{version}', function (RouteCollector $r) {
                        $r->addRoute('POST', '/add', 'Code@add');
                        $r->addRoute('GET', '/get', 'Code@get');
                        $r->addRoute('OPTIONS', '/add', 'Code@add');
                    }
                );
            }
        );

        self::_handler();
    }

    /**
     * Handler
     *
     * @return void
     */
    private function _handler()
    {
        $httpMethod = $this->getMethod();
        $uri = self::_prepareUri($this->getUri());

        $routeInfo = $this->_dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            $this->resp->status(404);
            $json = json_encode(['result' => 'not found']);
            break;
        case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $this->resp->status(405);
                $json = json_encode(['result' => 'not allowed']);
            break;
        case Dispatcher::FOUND:
            $handler = explode('@', $routeInfo[1]);

            if (!is_array($handler) || count($handler) < 2) {
                $this->resp->status(500);
                $json = json_encode(['result' => 'Incorrect handler string']);
                break;                
            }

            $vars = $routeInfo[2];
            $classPath = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['Routing', $vars['version'], $handler[0] . '.php']);

            if (!file_exists($classPath)) {
                $this->resp->status(404);
                $json = json_encode(['result' => 'not found']);
                break;                
            }
            
            include_once $classPath;
            $class = '\\Codet\\Http\\Routing\\' . $handler[0];

            if (!class_exists($class)) {
                $this->resp->status(500);
                $json = json_encode(['result' => 'Class not found']);
                break;                
            }

            $obj = new $class();

            if (!method_exists($obj, $handler[1])) {
                $this->resp->status(500);
                $json = json_encode(['result' => 'Method not found']);
                break;
            }

            $json = $obj->{$handler[1]}($this->req, $this->resp, $vars);

            break;
        default:
            $json = json_encode(['result' => 'error']);
            break;
        }

        $this->resp->header('Content-Type', 'application/json');
        $this->resp->header('Access-Control-Allow-Origin', $_ENV['frontend']);
        $this->resp->header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS');
        $this->resp->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        $this->resp->end($json);
    }

    /**
     * Prepare uri
     *
     * @param string $uri uri path
     * 
     * @return string
     */
    private function _prepareUri(string $uri): string
    {
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        return rawurldecode($uri);
    }
}
