<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 04/12/15
 * Time: 16:50
 */

namespace LeeMason\Larastaller\Middlewares;


class UpdateMiddleware
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
        if (!$request->is('update*')) {
            return redirect('update');
        }

        return $next($request);
    }

}