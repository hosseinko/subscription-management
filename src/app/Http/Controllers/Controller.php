<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Libs\Logger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Throwable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Throwable $exception
     * @param $message
     * @param $logFilename
     * @param $params
     * @return JsonResponse
     */
    protected function handleExceptions(Throwable $exception, $message, $logFilename, $params)
    {
        switch (true) {
            case $exception instanceof ValidationException:
                Logger::logDebug($logFilename, $exception->getMessage(), [
                    'file'   => $exception->getFile(),
                    'line'   => $exception->getLine(),
                    'errors' => json_encode($exception->validator->getMessageBag()),
                    'params' => $params
                ]);

                return response()->json([
                    'message' => __('errors.invalid_parameters'),
                    'errors'  => $exception->validator->getMessageBag()
                ], 422);
                break;

            case $exception instanceof ResourceNotFoundException:

                return response()->json([
                    'message' => $exception->getMessage()
                ], $exception->getCode());
                break;

            default:
                Logger::logDebug($logFilename, $exception->getMessage(), array_merge([
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ], $params));

                return response()->json([
                    'message' => $message
                ], 500);
                break;
        }
    }
}
