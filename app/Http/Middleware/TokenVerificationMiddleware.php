<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');
        $payload = JWTToken::verifyToken($token);
        if($payload === 'Invalid Token'){
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'Invalid Token'
            // ], 200);
            return redirect('/userLogin');
        }else{
            $request->headers->set('email', $payload->user_email);
            
            if(isset($payload->user_id)){
                $request->headers->set('user_id', $payload->user_id);
            }
            return $next($request);
        }
        
    }
}
