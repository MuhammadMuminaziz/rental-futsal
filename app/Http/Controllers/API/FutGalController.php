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
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message());
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }
        $gallery = FutsalGallery::create($data['data']);
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
        $gallery = $this->getGallery($id);
        $data = $this->baseStore($request, $this->data(), $this->validData(), $this->message(), user: $gallery);
        if (!$data['success']) {
            return $this->sendError("Bad Request", $data['data'], 400);
        }

        // update slug
        if ($request->title != $gallery->title) {
            $data['data']['slug'] = SlugService::createSlug(FutsalGallery::class, 'slug', $request->title);
        }
        $gallery->update($data['data']);
        return $this->sendResponse("Success", new FutGalResource($gallery));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gallery = $this->getGallery($id);
        $gallery->delete();
        return $this->sendResponse('Success');
    }

    public function getGallery($id)
    {
        $gallery = FutsalGallery::find($id);
        throw_if($gallery == null, new ModelNotFoundException("Futsal Gallery Not Found", 404));
        return $gallery;
    }

    private function data(): array
    {
        return array('futsal_id', 'event_id', 'title', 'description', 'isBackground', 'photo');
    }

    private function validData($update = false): array
    {
        return [
            'futsal_id' => 'required',
            'title' => 'required',
            'isBackground' => 'required',
            'photo' => $update ? 'nullable|mimes:jpg,png,jpeg|max:2048' : 'required|mimes:jpg,png,jpeg|max:2048'
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
