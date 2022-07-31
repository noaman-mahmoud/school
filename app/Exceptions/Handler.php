<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ResponseTrait;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception) {
        if ($request->is('api/*')) {
            if ($exception instanceof ModelNotFoundException) {
                $msg = 'Model Not Found';
            }

            if ($exception instanceof NotFoundHttpException) {
                $msg = 'Not Found Http';
            }

            if ($exception instanceof AuthenticationException) {
                return $this->unauthenticatedReturn();
            }

            return $this->response('unauthenticated', $msg ?? $exception->getMessage(),
            );
        }

        return parent::render($request, $exception);
    }
}
