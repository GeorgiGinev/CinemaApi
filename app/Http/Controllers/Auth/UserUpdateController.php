<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; 
use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserUpdateController extends Controller
{
    use ImageTrait;
    
    function get(Request $request) {
        $user = $request->user();

        if($user->avatar) {
            $user->avatar = $this->retriveImages($user->avatar);
        }
        
        return $user;
    }

    function update(Request $request) {
        $this->validator($request->all()['attributes'])->validate();

        $user = $request->user();

        $avatar = null;

        if($request->input('attributes')['avatar']) {
            $avatar = $this->verifyAndUpload($request->input('attributes')['avatar']);
        }
        $name = $request->input('attributes')['name'];
        User::where('id', $user->id)->update(array('name' => $name, 'avatar' => $avatar));

        $loadedUser = User::where('id', $user->id)->first();
        if($loadedUser->avatar) {
            $loadedUser->avatar = $this->retriveImages($loadedUser->avatar);
        }

        return $loadedUser;
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
