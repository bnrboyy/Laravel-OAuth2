<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function login(Request $req) {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;

            return response()->json([
                'success' => $success
            ], $this->successStatus);
        } else {
            return response([
                'error' => 'Unauthorised'
            ], 401);
        }
    }

    public function register(Request $req) {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors()
            ], 401);
        }

        $input = $req->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return response([
            'message' => $success
        ], $this->successStatus);

    }

    public function details() {
        $user = Auth::user();
        return response([
            'data' => $user
        ], $this->successStatus);
    }
}
