<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\InviteResource;
use App\Models\InviteTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InviteController extends BaseController
{
    public function index(Request $request)
    {
        $params = $request->only(['user_id', 'team_id']);
        $invite = InviteTeam::when(array_key_exists('user_id', $params), function ($q)  use ($params) {
            $user_id = $params['user_id'];
            $q->where('user_id', 'like', "%$user_id%");
        })->when(array_key_exists('team_id', $params), function ($q)  use ($params) {
            $team_id = $params['team_id'];
            $q->where('team_id', 'like', "%$team_id%");
        })->get();
        return $this->sendResponse("Success", InviteResource::collection($invite));
    }

    public function store(Request $request)
    {
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData(), $this->message());
        if ($validator->fails()) {
            return $this->sendError('Bad Request', $validator->errors(), code: 401);
        }
        $invite = InviteTeam::create([
            'user_id' => $data['user_id'],
            'team_id' => $data['team_id']
        ]);
        return $this->sendResponse('Success', new InviteResource($invite));
    }

    public function update(Request $request, $id)
    {
        $invite = $this->getInvite($id);
        $user = User::find($invite->user_id);
        if (!empty($user)) {
            if (empty($user->team_id)) {
                $user->update([
                    'team_id' => $invite->team_id
                ]);
                return $this->sendResponse('Success', new InviteResource($invite));
            }
        } else {
            return $this->sendError('User Not Found');
        }
        $invite->delete();
        return $this->sendError('User already has a team');
    }

    public function destroy($id)
    {
        $invite = $this->getInvite($id);
        $invite->delete();
        return $this->sendResponse('Success');
    }

    public function getInvite($id)
    {
        $invite = InviteTeam::find($id);
        throw_if($invite == null, new ModelNotFoundException('Invite team not found'));
        return $invite;
    }

    private function data()
    {
        return array('user_id', 'team_id');
    }

    private function validData()
    {
        return [
            'user_id' => 'required',
            'team_id' => 'required'
        ];
    }

    protected function message()
    {
        return [
            'user_id.required' => 'User id harus diisi',
            'team_id.required' => 'Team id harus diisi',
        ];
    }
}
