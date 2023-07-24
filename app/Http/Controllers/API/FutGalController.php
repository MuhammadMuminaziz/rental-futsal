<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\FutGalResource;
use App\Models\FutsalGallery;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FutGalController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('title');
        $galleries = FutsalGallery::when(array_key_exists('title', $params), function ($q)  use ($params) {
            $title = $params['title'];
            $q->where('title', 'like', "%$title%");
        })->orderBy('title', 'asc')->get();

        return $this->sendResponse("Success", FutGalResource::collection($galleries));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData(type: "create"), $this->message());
        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        $photo = $request->photo ?? null;
        if (!empty($photo)) {
            $name = rand() . time() . "." . $photo->getClientOriginalExtension();
            $photo->move('file_upload/gallery', $name);
            $data['photo'] = $name;
        }

        $gallery = FutsalGallery::create($data);
        return $this->sendResponse("Success", new FutGalResource($gallery));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $gallery = $this->getGallery($id);
        return $this->sendResponse("Success", new FutGalResource($gallery));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->data());
        $validator = Validator::make($data, $this->validData(type: "update"), $this->message());
        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        $gallery = $this->getGallery($id);
        $photo = $request->photo ?? null;
        if (!empty($photo)) {
            if (!empty($gallery->photo)) {
                unlink('file_upload/gallery/' . $gallery->photo);
            }

            $name = rand() . time() . "." . $photo->getClientOriginalExtension();
            $photo->move('file_upload/gallery', $name);
            $data['photo'] = $name;
        }

        // update slug
        if ($request->name != $gallery->title) {
            $data['slug'] = SlugService::createSlug(FutsalGallery::class, 'slug', $request->name);
        }

        $gallery->update($data);
        return $this->sendResponse("Success", new FutGalResource($gallery));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function getGallery($id)
    {
        $gallery = FutsalGallery::find($id);
        throw_if($gallery == null, new ModelNotFoundException($gallery));
        return $gallery;
    }

    private function data(): array
    {
        return array('futsal_id', 'event_id', 'title', 'description', 'isBackground', 'photo');
    }

    private function validData($type): array
    {
        return [
            'futsal_id' => 'required',
            'title' => 'required',
            'isBackground' => 'required',
            'photo' => $type == "update" ? 'mimes:jpg,png,jpeg|max:2048' : 'required|mimes:jpg,png,jpeg|max:2048'
        ];
    }

    protected function message()
    {
        return [
            'futsal_id.required' => 'Futsal id harus diisi',
            'title.required' => 'Title harus diisi',
            'isBackground.required' => 'Is Background harus diisi',
            'photo.required' => 'Photo harus diisi'
        ];
    }
}
