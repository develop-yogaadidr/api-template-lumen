<?php

namespace App\Exceptions;

use App\Enums\StatusCodes;
use App\Enums\MariaDbCodes;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return $this->handleApiException($request, $exception);
    }

    private function handleApiException($request, Throwable $exception)
    {
        $exc = $this->prepareJsonResponse($request, $exception);
        $statusCode = $exc->getStatusCode();
        $data = null;

        if ($exception instanceof HttpResponseException) {
            $exc = $exception->getResponse();
        }

        if ($exception instanceof ModelNotFoundException) {
            $statusCode = StatusCodes::NotFound;
        }

        if ($exception instanceof QueryException) {
            $statusCode = StatusCodes::UnprocessableEntity;
            $exc->original['message'] = $exception->errorInfo[2];
        }

        if ($exception instanceof ValidationException) {
            $statusCode = StatusCodes::UnprocessableEntity;
            $data = $exception->getResponse()->original;
        }

        return $this->printResponse($exc, $statusCode, $data);
    }

    private function printResponse($exception, $statusCode, $data = null)
    {
        $response = [];

        switch ($statusCode) {
            case StatusCodes::Unauthorized:
                $response['message'] = 'Unauthorized';
                break;
            case StatusCodes::Forbidden:
                $response['message'] = 'Forbidden';
                break;
            case StatusCodes::NotFound:
                $response['message'] = $exception->original['message'] != null ? $exception->original['message'] : 'Not Found';
                break;
            case StatusCodes::MethodNotAllowed:
                $response['message'] = 'Method Not Allowed';
                break;
            case StatusCodes::UnprocessableEntity:
                $response['message'] = $exception->original['message'];
                if($data != null)
                {
                    $response['data'] = $data;
                }
                break;
            case StatusCodes::BadRequest:
                $response['message'] = $exception->original['message'];
                break;
            default:
                $response['message'] = ($statusCode == StatusCodes::InternalServerError) ?  $exception->original['message'] : $exception->getMessage();
                break;
        }

        if (config('app.debug')) {
            $response['trace'] = $exception->original['trace'];
        }

        return response()->json($response, $statusCode);
    }
}
