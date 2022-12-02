<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logout user from current session 
     */
    public function logout(Request $request) {
        $user = Auth::user();

        // Revoke the token that was used to authenticate the current request...
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }


    public function logoutAll(Request $request) {

    }
}
