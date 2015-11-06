<?php
namespace Hoimi\Response;
use Hoimi\Response;

/**
 * Class ErrorJson
 * @package Hoimi\Response
 */
class ErrorJson extends \Hoimi\Response\Json
{
    /**
     * @param array $validationError
     * @param array|null $status
     */
    public function __construct(array $validationError, array $status = null)
    {
        parent::__construct(array (
            'result' => false,
            'error' => $validationError,
            'entity' => null,
            'list' => null
        ), $status ?: array('HTTP/1.1 400 Bad Request'));
    }
}