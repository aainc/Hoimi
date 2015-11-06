<?php
namespace Hoimi\Response;
use Hoimi\Response;

/**
 * Class NotFound
 * @package Hoimi\Response
 */
class NotFound implements Response
{
    /**
     * @return array|mixed
     */
    public function getHeaders()
    {
        return array('HTTP/1.0 404 NotFound');
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return '<p>NotFound</p>';
    }
}
