<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Models\Owner;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('name');
        $users = User::when(array_key_exists('name', $params), function ($q)  use ($params) {
            $name = $params['name'];
            $q->where('name', 'like', "%$name%");
        })->orderBy('name', 'asc')->get();

        return $this->sendResponse("Success", UserResource::collection($users));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->getUser($id);
        return $this->sendResponse("Success", new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $this->getUser($id);
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData($request->photo ?? null), $this->message());
        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        $photo = $request->photo ?? null;
        if (!empty($photo)) {
            if (!empty($user->photo)) {
                unlink('file_upload/photo/' . $user->photo);
            }

            $name = rand() . time() . "." . $photo->getClientOriginalExtension();
            $photo->move('file_upload/photo', $name);
            $data['photo'] = $name;
        }

        // update slug
        if ($request->name != $user->name) {
            $data['slug'] = SlugService::createSlug(User::class, 'slug', $request->name);
        }
        $user->update($data);
        return $this->sendResponse("Success", new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            $user = Owner::find($id);
        }
        throw_if($user == null, new ModelNotFoundException($user));
        return $user;
    }

    private function data()
    {
        return array('team_id', 'name', 'email', 'photo');
    }

    private function validData($photo)
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'photo' => $photo != null ? 'required|mimes:jpg,png,jpeg|max:2048' : '',
        ];
    }
}
