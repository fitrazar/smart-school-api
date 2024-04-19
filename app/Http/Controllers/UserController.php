<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::role('admin')->get();

        return $this->sendResponse(UserResource::collection($user), 'Data user');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->assignRole('admin');
            return $this->sendResponse(new UserResource($user), 'User Created');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->sendResponse(new UserResource($user), 'Detail User');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $input = $request->only('name');

        $validator = Validator::make($input, [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $user->name = $input['name'];
            $user->save();

            return $this->sendResponse(new UserResource($user), 'User Updated');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return $this->sendResponse('-', 'User Deleted');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }
    }
}
