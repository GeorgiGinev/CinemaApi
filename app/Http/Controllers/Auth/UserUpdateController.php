<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserUpdateController extends Controller
{
    function update(Request $request) {
        $this->validator($request->input('attributes'))->validate();

        $user = $request->user();

        if($user->name !== $request->input('attributes')['name'] || $user->avatar !== $request->input('attributes')['avatar']) {
            $name = $request->input('attributes')['name'];
            $avatar = $request->input('attributes')['avatar'];

            User::where('id', $user->id)->update(array('name' => $name, 'avatar' => $avatar));
        }

        return User::where('id', $user->id)->first();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string'],
        ]);
    }
}
