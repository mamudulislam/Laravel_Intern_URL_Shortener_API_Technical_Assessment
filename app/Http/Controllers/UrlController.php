<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrlRequest;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request('per_page', 10), 1), 100);
        $urls = request()->user()->urls()->latest()->paginate($perPage);

        return response()->json(['success' => true, 'message' => 'URLs retrieved successfully', 'data' => UrlResource::collection($urls), 'meta' => ['current_page' => $urls->currentPage(), 'last_page' => $urls->lastPage(), 'per_page' => $urls->perPage(), 'total' => $urls->total()]]);
    }

    public function store(StoreUrlRequest $request): JsonResponse
    {
        $code = $request->validated('custom_code') ?: $this->uniqueCode();
        $url = $request->user()->urls()->create(['original_url' => $request->validated('url'), 'short_code' => $code]);

        return response()->json(['success' => true, 'message' => 'URL shortened successfully', 'data' => new UrlResource($url)], 201);
    }

    public function show(Url $url): JsonResponse
    {
        $this->authorize('view', $url);
        return response()->json(['success' => true, 'message' => 'URL retrieved successfully', 'data' => new UrlResource($url)]);
    }

    public function destroy(Url $url): JsonResponse
    {
        $this->authorize('delete', $url);
        $url->delete();
        return response()->json(['success' => true, 'message' => 'URL deleted successfully', 'data' => null]);
    }

    public function stats(Url $url): JsonResponse
    {
        $this->authorize('view', $url);
        return response()->json(['success' => true, 'message' => 'URL statistics retrieved successfully', 'data' => ['url' => $url->original_url, 'short_code' => $url->short_code, 'click_count' => $url->click_count]]);
    }

    private function uniqueCode(): string
    {
        do {
            $code = Str::random(6);
        } while (Url::where('short_code', $code)->exists());

        return $code;
    }
}
