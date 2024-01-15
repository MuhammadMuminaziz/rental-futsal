<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Futsal;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only('futsal_id');
        $futsals = Review::when(array_key_exists('futsal_id', $params), function ($q)  use ($params) {
            $futsal_id = $params['futsal_id'];
            $q->where('futsal_id', 'like', "%$futsal_id%");
        })->orderBy('id', 'asc')->get();
        return $this->sendResponse("Success", ReviewResource::collection($futsals));
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
        $review = Review::create($data['data']);
        $rating = Review::getRating($request->futsal_id);
        if (!empty($rating)) {
            Futsal::updateRating($request->futsal_id, $rating);
        }
        return $this->sendResponse("Success", new ReviewResource($review));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = $this->getReview($id);
        return $this->sendResponse('Success', new ReviewResource($review));
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
        $futsal = Review::create($data['data']);
        $rating = Review::getRating($request->futsal_id);
        if (!empty($rating)) {
            Futsal::updateRating($request->futsal_id, $rating);
        }
        return $this->sendResponse("Success", new ReviewResource($futsal));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = $this->getReview($id);
        $review->delete();
        return $this->sendResponse('Success');
    }

    public function getReview($id)
    {
        $review = Review::find($id);
        throw_if($review == null, new ModelNotFoundException("Review Not Found", 404));
        return $review;
    }

    private function data(): array
    {
        return array('futsal_id', 'note', 'stars', 'doc_revies');
    }

    private function validData(): array
    {
        return [
            'futsal_id' => 'required|string',
            'note' => 'required|string',
            'stars' => 'required|integer',
            'doc_revies' => 'nullable|mimes:jpg,png,jpeg|max:2048'
        ];
    }

    protected function message(): array
    {
        return [
            'futsal_id.required' => 'Futsal id harus diisi',
            'futsal_id.string' => 'Futsal id harus berbentuk string',
            'note.required' => 'Catatan harus diisi',
            'note.string' => 'Catatan harus berbentuk string',
            'stars.required' => 'Bintang harus diisi',
            'stars.integer' => 'Bintang harus berbentuk integer',
            'doc_revies.mimes' => 'Dokumen harus berbentuk gambar (jpg, jpeg, png)',
            'doc_revies.max' => 'Dokumen tidak boleh melebihi 2Mb',
        ];
    }
}
