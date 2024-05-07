<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class VerifyMiddleware
{  use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user() && Auth::user()->is_verified==true )
        { return $next($request);
     }
      return $this->unAuthorizeResponse(
         'you dont have permission to perform the actionm please verify your email first'
      );
       
    }
}
