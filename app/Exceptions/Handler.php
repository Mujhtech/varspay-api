<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Models\ErrorLog;
use Auth;

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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        
        if ($request->wantsJson() || $request->expectsJson() || $request->isJson()) {
            
          return $this->customApiResponse($exception);
            
        } else {
            
          $retval = parent::render($request, $exception);
        }
        
        return $retval;
    }
    
    
    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }
    
        $response = [];
    
        switch ($statusCode) {
            case 401:
                $response['responseMessage'] = 'Unauthorized';
                break;
            case 403:
                $response['responseMessage'] = 'Forbidden';
                break;
            case 404:
                $response['responseMessage'] = 'Not Found';
                break;
            case 405:
                $response['responseMessage'] = 'Method Not Allowed';
                break;
            case 422:
                $response['responseMessage'] = $exception->original['message'];
                $response['responseError'] = $exception->original['errors'];
                break;
            default:
                $response['responseMessage'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }
    
    
        $response['responseCode'] = $statusCode;
    
        return response()->json($response, $statusCode);
    }
}
