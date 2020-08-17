<?php

namespace App\Http\Middleware;

use App\ClientSite;
use Closure;

class CheckClientAccess
{



    public $is_valid = true;
    public $error_message = '';



    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        if ($request->has('accesstoken'))
        {

            $site = ClientSite::where('accesstoken', $request->accesstoken)
                ->first();
            if ($site != null)
            {
                $host_token = base64_decode(base64_decode($request->accesstoken));
                $requestHost = parse_url($request
                    ->headers
                    ->get('origin') , PHP_URL_HOST);

                if ($requestHost != $host_token)
                {
                    $this->is_valid = false;
                    $this->error_message = 'You couldn\'t access from Invalid Site!!';
                }else{

                    return $next($request);
                }
            }
            else
            {
                $this->is_valid = false;
                $this->error_message = 'Token is Invalid!!';
            }
        }
        else
        {
            $this->is_valid = false;
            $this->error_message = 'Access Token is Required!!';
        }

        if (!$this->is_valid)
        {
            return response()->json(['status' => $this->is_valid, 'message' => $this->error_message]);
        }


    }
}
