<?php

namespace App\Exceptions;

use Log;
use Lang;
use Exception;
use Auth;
use App\Http\Kernel as Kernel;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Redirect;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception) {

        if ($exception instanceof UnauthorizedException) {
            return Redirect::to("/");
        };

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                \Route::any(request()->path(), function () use ($exception, $request) {
                    return parent::render($request, $exception);
                })->middleware('web');
                return app()->make(Kernel::class)->handle($request);
            }
        }

        $message = $exception->getMessage(); 

        if($request->ajax()) {
            Log::error($exception);
            $response = [
                'success' => false,
                'code' => 500,
                'message' => $message
            ];

            if($exception instanceof ValidationException) {
                $response->code = 400;
                $response->message = 'Invalid or Missing Field';
            }
            return response()->json($response, 500);
        }

        if(true) { // TODO
            return parent::render($request, $exception);
        } else {
            return redirect()->back()->with('error', $message);
        }
        
    }
}
