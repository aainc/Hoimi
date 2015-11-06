<?php
namespace Hoimi\Response;
use Hoimi\Response;

/**
 * Class Forbidden
 * @package Hoimi\Response
 */
class Forbidden implements Response
{
    /**
     * @return array|mixed
     */
    public function getHeaders()
    {
        return array('HTTP/1.0 403 Forbidden');
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return '<p>forbidden</p>';
    }
}
