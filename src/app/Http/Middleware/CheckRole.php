<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        //Comprobar que está autenticado
        if(!$request->user()){
            return redirect()->route('login');
        }
        //Comprobar que está activo
        if(!$request->user()->isActive()){
            abort(403,'Tu cuenta está inactiva. Contacta al administrador.');
            //temporal, igual es mejor redirigir a una pagina.
        }
        //Comprobar si tiene uno de los roles permitidos
        if(!$request->user()->hasAnyRole($roles)){
            abort(403,'Permiso denegado.');
        }
        //Si pasa todas las comprobaciones sigue adelante
        return $next($request);
    }
}
