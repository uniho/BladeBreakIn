<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // ※
    protected function prepareResponse($request, Throwable $e)
    {
        if (\HQ::getDebugShowSource()) {
            return parent::prepareResponse($request, $e);
        }

        // if (! $this->isHttpException($e) && config('app.debug')) {
        //     return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e)->prepare($request);
        // }

        if (! $this->isHttpException($e)) {
            $e = new \Symfony\Component\HttpKernel\Exception\HttpException(500, $e->getMessage(), $e);
        }

        return $this->toIlluminateResponse(
            $this->renderHttpException($e), $e
        )->prepare($request);
    }

    // ※
    protected function renderHttpException($e)
    {
        if (\HQ::getDebugShowSource()) {
            return parent::renderHttpException($e);
        }

        $this->registerErrorViewPaths();

        if ($view = $this->getHttpExceptionView($e)) {
            try {
                return response()->view($view, [
                    'errors' => new \Illuminate\Support\ViewErrorBag,
                    'exception' => $e,
                ], $e->getStatusCode(), $e->getHeaders());
            } catch (Throwable $t) {
                // config('app.debug') && throw $t;

                $this->report($t);
            }
        }

        return response()->view('errors::500', [
          'errors' => new \Illuminate\Support\ViewErrorBag,
          'exception' => $e,
        ], $e->getStatusCode(), $e->getHeaders());
        // return $this->convertExceptionToResponse($e);
    }
}
