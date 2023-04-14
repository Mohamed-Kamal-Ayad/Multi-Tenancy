<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetActiveStore
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $store = Store::where('domain', $host)->firstOrFail();
        app()->instance('store.active', $store);
        $db = $store->database_options['dbname'];
        config(['database.connections.mysql.database' => $db]);

        return $next($request);
    }
}
