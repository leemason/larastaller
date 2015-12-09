<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 04/12/15
 * Time: 16:49
 */

namespace LeeMason\Larastaller\Middlewares;


class InstallMiddleware
{

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (!$request->is('install*')) {
            return redirect('install');
        }

        return $next($request);
    }

}