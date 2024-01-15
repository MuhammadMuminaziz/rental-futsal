<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FacilityController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('name');
        $facilities = Facility::when(array_key_exists('name', $params), function ($q)  use ($params) {
            $name = $params['name'];
            $q->where('name', 'like', "%$name%");
        })->orderBy('name', 'asc')->get();
        return $this->sendResponse("Success", FacilityResource::collection($facilities));
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
        $facility = Facility::create($data['data']);
        return $this->sendResponse("Success", new FacilityResource($facility));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $facility = $this->getFacility($id);
        return $this->sendResponse("Success", new FacilityResource($facility));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message());
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        $facility = $this->getFacility($id);
        $facility->update($data['data']);
        return $this->sendResponse("Success", new FacilityResource($facility));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $facility = $this->getFacility($id);
        $facility->delete();
        return $this->sendResponse('Success');
    }

    public function getFacility($id)
    {
        $facility = Facility::find($id);
        throw_if($facility == null, new ModelNotFoundException("Facility Not Found", 404));
        return $facility;
    }

    private function data(): array
    {
        return array('futsal_id', 'name', 'description');
    }

    private function validData(): array
    {
        return [
            'futsal_id' => 'required|string',
            'name' => 'required|string'
        ];
    }

    protected function message(): array
    {
        return [
            'futsal_id.required' => 'Futsal id harus diisi',
            'futsal_id.string' => 'Futsal id harus berbentuk string',
            'name.required' => 'Fasilitas harus diisi',
            'name.string' => 'Fasilitas harus berbentuk string'
        ];
    }
}
