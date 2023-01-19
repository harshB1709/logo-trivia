<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\LoginResponse as ContractsLoginResponse;

class LoginResponse implements ContractsLoginResponse
{
    /**
     * Handles the login response
     *
     * @param [type] $request
     * @return void
     */
    public function toResponse($request)
    {
        return redirect()->intended("/words");
    }
}
