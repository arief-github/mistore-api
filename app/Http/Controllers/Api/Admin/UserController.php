<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // display a listing of the resource
    public function index()
    {
        // get all users
        $users = User::when(request() -> q, function($users) {
           $users = $users->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        // return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    // store a new user
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if(!$user) {
            // return failed with Api Resource
            return new UserResource(false, 'Data User Gagal Disimpan', null);
        }

        return new UserResource(true, 'Data User Berhasil Disimpan', $user);
    }

    // Display the specified resource.

    public function show($id)
    {
        $user = User::whereId($id)->first();

        if(!$user) {
            // return failed with Api Resource
            return new UserResource(false, "Detail User Tidak Ditemukan!", null);
        }

        return new UserResource(true, "Detail Data User Ditemukan!", $user);
    }

    // Update the specified user

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password'=> 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "") {
            // update user without password
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        // update user with new password
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if(!$user) {
            return new UserResource(false, "Data user gagal diupdate!", null);
        }

        return new UserResource(true, "Data User berhasil diupdate!", $user);
    }

    // Delete the specified user
    public function destroy(User $user) {
        if ($user->delete()) {
            return new UserResource(true, "Data User Berhasil Dihapus!", null);
        }

        return new UserResource(false, "Data User Gagal Dihapus!", null);
    }
}
