<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se l'utente è autenticato e ha il ruolo di dipendente
        if (Auth::check() && Auth::user()->role === 'dipendente') {
            return $next($request);
        }
        
        // Reindirizza l'utente alla dashboard principale se non è un dipendente
        return redirect('dashboard')->with('error', 'Accesso non autorizzato.');
    }
}
