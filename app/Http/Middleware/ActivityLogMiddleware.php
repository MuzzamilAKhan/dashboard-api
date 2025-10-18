<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog\ActivityLog;
use App\Repositories\ActivityLog\ActivityLogRepository;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogMiddleware
{
    
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            Log::info((int)$request->header('X-User-Id'));
            $activity = new  ActivityLog([
                'ip_address' => $request->ip(), 
                'user_id' => (int)$request->header('X-User-Id'), 
                'route' => $request->fullUrl(), 
                'action_date' => date('Y-m-d'),
                'action_time' => date('H:i:s')
            ]);
            $activity->save();
            return $next($request);
        }catch(Throwable $e){
            Log::error($e->getMessage());
            return $next($request);

        }
        
    }
}
