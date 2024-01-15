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
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message());
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        $data['data']['user_id'] = auth()->id();
        $futsal = Futsal::create($data['data']);
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
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message(), user: $futsal);
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        $futsal->update($data['data']);
        return $this->sendResponse("Success", new FutsalResource($futsal));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $futsal =  $this->getFutsal($id);
        if ($futsal->trashed()) {
            $futsal->forceDelete();
        } else {
            // Soft delete
            $futsal->delete();
        }
        return $this->sendResponse("Success");
    }

    public function futsalTrashed()
    {
        $futsal = Futsal::onlyTrashed()->get();
        return $this->sendResponse("Success", FutsalResource::collection($futsal));
    }

    public function restore(string $id)
    {
        $futsal =  $this->getfutsalTrashed($id);
        $futsal->restore();
        return $this->sendResponse("Success", new FutsalResource($futsal));
    }

    public function restoreAll()
    {
        $futsal = Futsal::onlyTrashed();
        throw_if($futsal->count() == 0, new ModelNotFoundException("Futsal Not Found", 404));
        $futsal->restore();
        return $this->sendResponse("Success Restore All Data");
    }

    public function getFutsalTrashed($id): Futsal
    {
        $futsal = Futsal::onlyTrashed()->where('id', $id)->first();
        throw_if($futsal == null, new ModelNotFoundException("Futsal Not Found", 404));
        return $futsal;
    }

    private function getFutsal($id)
    {
        $futsal = Futsal::find($id);
        throw_if($futsal == null, new ModelNotFoundException("Futsal Not Found", 404));
        return $futsal;
    }

    private function data(): array
    {
        return array('name', 'facilities', 'description', 'cancellation', 'whatsapp', 'facebook', 'instagram', 'avatar', 'isActive', 'rating', 'address', 'lat', 'lng');
    }

    private function validData(): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'facilities' => 'required',
            'cancellation' => 'required',
            'whatsapp' => 'required',
            'address' => 'required',
            'avatar' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'lat' => 'required',
            'lng' => 'required',
        ];
    }

    protected function message(): array
    {
        return [
            'name.required' => 'Nama Rental harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'facilities.required' => 'Fasilitas harus diisi',
            'cancellation.required' => 'Kebijakan pembatalan harus diisi',
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
