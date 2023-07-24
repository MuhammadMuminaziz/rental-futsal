<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\FutsalResource;
use App\Models\Futsal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FutsalController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('name');
        $futsals = Futsal::when(array_key_exists('name', $params), function ($q)  use ($params) {
            $name = $params['name'];
            $q->where('name', 'like', "%$name%");
        })->orderBy('name', 'asc')->get();

        return $this->sendResponse("Success", FutsalResource::collection($futsals));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData($request->avatar ?? null), $this->message());
        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        $data['user_id'] = auth()->id();
        $avatar = $request->avatar ?? null;
        if (!empty($avatar)) {
            $name = rand() . time() . "." . $avatar->getClientOriginalExtension();
            $avatar->move('file_upload/avatar', $name);
            $data['avatar'] = $name;
        }

        $futsal = Futsal::create($data);
        return $this->sendResponse("Success", new FutsalResource($futsal));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $futsal = $this->getFutsal($id);
        return $this->sendResponse("Success", new FutsalResource($futsal));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $futsal = $this->getFutsal($id);
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData($request->avatar ?? null), $this->message());
        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        $avatar = $request->avatar ?? null;
        if (!empty($avatar)) {
            if (!empty($futsal->avatar)) {
                unlink('file_upload/avatar/' . $futsal->avatar);
            }

            $name = rand() . time() . "." . $avatar->getClientOriginalExtension();
            $avatar->move('file_upload/avatar', $name);
            $data['avatar'] = $name;
        }

        $futsal->update($data);
        return $this->sendResponse("Success", new FutsalResource($futsal));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 
    }

    // public function rating(Request $request, $id)
    // {
    //     $futsal = $this->getFutsal($id);
    //     $data = $request->only('rating');
    //     $validator = Validator::make($data, [
    //         'rating' => 'required|number'
    //     ], [
    //         'rating.required' => 'Rating harus diisi',
    //         'rating.number' => 'Rating harus berbentuk nomer'
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->sendError("Bad Request", $validator->errors(), 400);
    //     }

    //     $futsal->update($data);
    //     return $this->sendResponse("Success", new FutsalResource($futsal));
    // }

    private function getFutsal($id)
    {
        $futsal = Futsal::find($id);
        throw_if($futsal == null, new ModelNotFoundException($futsal));
        return $futsal;
    }

    private function data(): array
    {
        return array('name', 'facility_id', 'description', 'whatsapp', 'facebook', 'instagram', 'avatar', 'isActive', 'rating', 'address', 'lat', 'lng');
    }

    private function validData($avatar): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'whatsapp' => 'required',
            'address' => 'required',
            'avatar' => $avatar != null ? 'required|mimes:jpg,png,jpeg|max:2048' : '',
            'lat' => 'required',
            'lng' => 'required',
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => 'Nama Rental harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'whatsapp.required' => 'Nomer whatsapp harus diisi',
            'address.required' => 'Alamat harus diisi',
            'avatar.required' => 'Avatar harus diisi',
            'avatar.mimes' => 'Avatar harus berbentuk gambar (jpg, jpeg, png)',
            'avatar.max' => 'Avatar tidak boleh melebihi 2Mb',
            'lat.required' => 'lat harus diisi',
            'lng.required' => 'lng harus diisi',
        ];
    }
}
