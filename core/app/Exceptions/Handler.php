<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return responseJson(404, 'failed', 'not found');
            }
            return false;
        });

    }

    public function render($request, Throwable $exception)
    {

        if ($request->expectsJson() && $exception instanceof ModelNotFoundException) {
            return responseJson(404, 'failed', 'Data not found');
        }

//        if ($request->expectsJson()) {
//            return responseJson(500, 'error', 'Something went wrong please try again later');
//        }

        return parent::render($request, $exception);
    }
}
