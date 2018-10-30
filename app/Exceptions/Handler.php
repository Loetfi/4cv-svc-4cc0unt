<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\Api;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

            return response()->json(['token_expired'], 500);

        } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

            return response()->json(['token_invalid'], 500);

        } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException) {

            return response()->json(['token_absent' => $exception->getMessage()], 500);

        }
        return parent::render($request, $exception);
        // return response()->json(Api::format('0',[],'Something wrong'), 400);
    }
}
