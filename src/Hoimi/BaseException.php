<?php
namespace Hoimi;
/**
 * Class Exception
 * @package Hoimi
 */
abstract class BaseException extends \Exception
{
    abstract public function buildResponse();
}