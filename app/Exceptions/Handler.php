<?php

namespace App\Exceptions;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (DecryptException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        });

        $this->renderable(function (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Data yang anda cari tidak ditemukan',
            ];
            return response()->json($response, 404);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 404);
        });

        $this->renderable(function (Throwable $e) {

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        });
    }
}
