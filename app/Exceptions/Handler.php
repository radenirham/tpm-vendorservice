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
            /* header('Content-Type: application/json; charset=utf-8');
            echo json_encode(
                array(
                    'status' => false,
                    'message' => $e,
                    'response' => [],
                    'generated' => 0,
                    'tokenExpire' => null,
                    'serverTime' => time(),
                    'version' => "-"
                )
            );
            die(); */
        });
    }
}
