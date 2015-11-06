<?php
namespace Hoimi\Response;
use Hoimi\Response;

/**
 * Class Error
 * @package Hoimi\Response
 */
class Error implements Response
{
    private $exception = null;
    private $template = null;

    /**
     * @param $e
     * @param null $template
     */
    public function __construct($e, $template = null)
    {
        $this->exception = $e;
        $this->template = $template;
    }

    /**
     * @return array|mixed
     */
    public function getHeaders()
    {
        return array('HTTP/1.0 503 Service Temporary Unavailable');
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return '<p>Service Temporary Unavailable</p>';
    }
}