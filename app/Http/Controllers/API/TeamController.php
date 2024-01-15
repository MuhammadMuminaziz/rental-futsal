<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\TeamResource;
use App\Models\InviteTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('name');
        $team = Team::when(array_key_exists('name', $params), function ($q)  use ($params) {
            $name = $params['name'];
            $q->where('name', 'like', "%$name%");
        })->orderBy('name', 'asc')->get();
        return $this->sendResponse("Success", TeamResource::collection($team));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message());
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        if (!empty($request->invite_user)) {
            foreach ($request->invite_user as $n) {
                $invite_user[] = $n;
            }
        }
        unset($data['data']['invite_user']);
        $team = Team::create($data['data']);
        $user = User::find(auth()->id());
        $user->update(['team_id' => $team->id]);
        InviteTeam::makeInvite($invite_user, $team->id);
        return $this->sendResponse("Success", new TeamResource($team));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = $this->getTeam($id);
        return $this->sendResponse('Success', new TeamResource($team));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = $this->getTeam($id);
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message(), user: $team);
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        unset($data['data']['user_id']);
        $team->update($data['data']);
        return $this->sendResponse("Success", new TeamResource($team));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = $this->getTeam($id);
        InviteTeam::deleteInvite($team->id);
        $team->delete();
        return $this->sendResponse('Success');
    }

    public function getTeam($id)
    {
        $team = Team::find($id);
        throw_if($team == null, new ModelNotFoundException('Team not found', 404));
        return $team;
    }

    public function data()
    {
        return array('name', 'description', 'invite_user', 'avatar', 'wins', 'loses', 'level', 'match', 'poin', 'address', 'lat', 'lng');
    }

    public function validData()
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'avatar' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'lat' => 'required',
            'lng' => 'required',
        ];
    }

    protected function message()
    {
        return [
            'name' => 'Nama Team harus diisi',
            'address.required' => 'Alamat harus diisi',
            'avatar.required' => 'Avatar harus diisi',
            'avatar.mimes' => 'Avatar harus berbentuk gambar (jpg, jpeg, png)',
            'avatar.max' => 'Avatar tidak boleh melebihi 2Mb',
            'lat.required' => 'lat harus diisi',
            'lng.required' => 'lng harus diisi',
        ];
    }
}
