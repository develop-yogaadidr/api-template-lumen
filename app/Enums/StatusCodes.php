<?php

namespace App\Enums;

abstract class StatusCodes
{
    const Ok = 200;
    const NoContent = 204;
    const BadRequest = 400;
    const Unauthorized = 401;
    const Forbidden = 403;
    const NotFound = 404;
    const MethodNotAllowed = 405;
    const UnprocessableEntity = 422;
    const InternalServerError = 500;
}