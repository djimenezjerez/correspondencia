<?php

namespace App\Http\Middleware;

use App\Models\Procedure;
use Closure;
use Illuminate\Http\Request;

class EnsureUserOwnsProcedure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $procedure = Procedure::findOrFail($request->procedure->id);
        if ($procedure->user_id === $user->id) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'El trámite no se modificar porque no se encuentra en su bandeja',
            ], 422);
        }
    }
}
