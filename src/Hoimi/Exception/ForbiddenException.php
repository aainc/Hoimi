<?php
namespace Hoimi\Exception;

use Hoimi\BaseException;
use Hoimi\Response\Forbidden;

class ForbiddenException extends BaseException
{
    public function buildResponse()
    {
        return new Forbidden();
    }
}