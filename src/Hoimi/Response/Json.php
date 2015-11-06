<?php
namespace Hoimi\Response;
use Hoimi\Response;

/**
 * Class Json
 * @package Hoimi\Response
 */
class Json implements Response
{
    private $data = null;
    private $headers = null;

    /**
     * @param $data
     * @param $headers
     */
    public function __construct($data, array $headers = null)
    {
        $this->data = $data;
        $this->headers = $headers ?: array('HTTP/1.1 200 OK');
        $this->headers[] = 'Content-type: application/json';
    }

    /**
     * @return array|mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return json_encode($this->data);
    }
}
