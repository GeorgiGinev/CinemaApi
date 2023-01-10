<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public function reset(Request $request) {
        $this->validator($request->all())->validate();

        $user = $request->user();

        $newPassword = Hash::make($request->all()['password']);

        User::where('id', $user->id)->update(['password' => $newPassword]);
        
        return response()->json([
            'status' => 'success'
        ]);
    }

    protected function validator($data) {
        return Validator::make($data, [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);
    }
}
