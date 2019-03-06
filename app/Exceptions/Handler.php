<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Generic Symfony Http Exception Error Handling
        if ($this->isHttpException($exception)) {
            return $this->renderHttpException($exception);
        }

        // Errors below are not Symfony Http Exceptions and
        // therefore are rethrown to be handled as these.
        if ($this->isModelNotFoundException($exception)) {
            $model = class_basename($exception->getModel());

            $message = trans('errors.model_not_found', ['model' => $model]);

            throw new NotFoundHttpException($message, $exception);
        }

        if ($this->isAuthenticationException($exception)) {
            throw new UnauthorizedHttpException('Bearer', $exception->getMessage(), $exception);
        }

        if ($this->isAuthorizationException($exception)) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception, 2);
        }

        if ($this->isValidationException($exception)) {
            throw new UnprocessableEntityHttpException($exception->getMessage(), $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine if the given exception is an HTTP exception.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected function isHttpException(Exception $e)
    {
        return $e instanceof HttpExceptionInterface;
    }

    /**
     * Determines if the given exception is a not found exception.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isModelNotFoundException(Exception $exception)
    {
        return $exception instanceof ModelNotFoundException;
    }

    /**
     * Determines if the given exception is an authentication exception.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isAuthenticationException(Exception $exception)
    {
        return $exception instanceof AuthenticationException;
    }

    /**
     * Determines if the given exception is an authorization exception.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isAuthorizationException(Exception $exception)
    {
        return $exception instanceof AuthorizationException;
    }

    /**
     * Determines if the given exception is a validation exception.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isValidationException(Exception $exception)
    {
        return $exception instanceof ValidationException;
    }

    /**
     * Render the given HttpException.
     *
     * @param \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        return response()->json($this->constructErrorPayload($e), $e->getStatusCode(), $e->getHeaders());
    }

    /**
     * Generate a standard response payload for Http Exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $e
     *
     * @return array
     */
    protected function constructErrorPayload(HttpException $e)
    {
        $fields = [];

        $code = $e->getCode();

        $message = $e->getMessage();

        $statusCode = $e->getStatusCode();

        $statusTexts = Response::$statusTexts;

        $title = 'JARGON-API-HTTP '.Arr::get($statusTexts, $statusCode, trans('errors.error_occurred'));

        if (isset($e->getPrevious()->validator)) {
            $fields = $e->getPrevious()->validator->errors()->getMessages();
        }

        return compact('title', 'message', 'code', 'fields');
    }
}
