<?php
namespace Hoimi\Exception;
use Hoimi\BaseException;
use Hoimi\Response\NotFound;

/**
 * Class NotFoundException
 * @package Hoimi\Exception
 */
class NotFoundException extends BaseException
{
    /**
     * @return NotFound
     */
    public function buildResponse()
    {
        return new NotFound();
    }
}