<?php
namespace Hoimi\Exception;
use Hoimi\BaseException;
use Hoimi\Response\ErrorJson;

/**
 * Class ValidationException
 * @package Hoimi\Exception
 */
class ValidationException extends BaseException
{
    private $validationErrors = array();

    public function __construct($array)
    {
        parent::__construct('validation error');
        $this->validationErrors = $array;
    }
    /**
     * @param array $validationErrors
     */
    public function setValidationErrors($validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @return ErrorJson
     */
    public function buildResponse()
    {
        return new ErrorJson(
            $this->validationErrors ? $this->validationErrors : array(),
            array('HTTP/1.1 400 Bad Request')
        );
    }
}