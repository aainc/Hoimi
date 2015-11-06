<?php
namespace Hoimi;
/**
 * Class Response
 *
 * instance of HTTP Response
 * @package Hoimi
 */
interface Response
{
    /**
     * Response Header
     * @return array()
     */
    public function getHeaders();

    /**
     * Response Body
     * @return string
     */
    public function getContent();
}
