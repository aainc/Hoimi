<?php
namespace Hoimi;

use Hoimi\BaseAction;
use Hoimi\BaseException;
use Hoimi\Exception\ValidationException;
use Hoimi\Request;
use Hoimi\Response\Json;
use Hoimi\Router;

/**
 * Class BatchRequest
 * @package Hoimi
 */
class BatchRequest extends BaseAction
{
    /**
     * @return Json
     * @throws Exception\ValidationException
     */
    public function post ()
    {
        $request = $this->getRequest();
        if ($request->get('batch') === null) {
            throw new ValidationException(array('request' => 'empty set'));
        }
        $batch = json_decode($this->getRequest()->get('batch'));
        if ($batch === false || !is_object($batch)) {
            throw new ValidationException(array('request' => 'invalid set'));
        }
        $headers = $request->getHeaders();
        $responses = null;
        foreach ($batch->requests as $row)
        {
            if (!isset($row->url) || !isset($row->method)) {
                $responses[] = array(
                    'request' => $row->url,
                    'method' => $row->method,
                    'error' => 'invalid argument. url or method is null',
                    'result'  => null,
                );
            } else {
                $headers['REQUEST_URI']    = $row->url;
                $headers['REQUEST_METHOD'] = $row->method;
                $response = null;
                try {
                    $request = new Request($headers, (array)$row->params);
                    list ($action, $method) = $this->getRouter()->run($request);
                    $action->setRequest($request) ;
                    $action->setConfig($this->getConfig()) ;
                    $this->prepareAction($action);
                    $response =  $action->$method();
                } catch(\Hoimi\BaseException $e) {
                    $response = $e->buildResponse();
                }
                $responses[] = array (
                    'request' => $row->url,
                    'method' => $row->method,
                    'error' => null,
                    'result' => $response->getContent(),
                );
            }
        }
        return new Json(array('data' => $responses));
    }

    public function prepareAction($action)
    {
    }

    public function getRouter()
    {
        return Router::getInstance();
    }
}