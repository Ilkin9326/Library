<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyPublisherApiKey
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

        $api_key = $request->header('x-api-key');

        // Select publisher from database by its API-KEY
        $publisherInfo = DB::table('publisher as p')->where('api_key', $api_key)
            ->select('p.publisher_id')
            ->first();

        if ($publisherInfo == null) {
            return response()->json(
                [
                    'operation_message' => 'Invalid Api key'
                ], 401);
        }

        return $next($request);
    }
}
