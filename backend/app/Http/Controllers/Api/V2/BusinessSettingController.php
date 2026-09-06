<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BusinessSettingCollection;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BusinessSettingController extends Controller
{
    /**
     * GET /api/v2/business-settings
     *
     * Hot path: Home fires this from Layout + Navbar + Footer in the same
     * tick, and several other pages read it on each navigation. The payload
     * almost never changes between admin updates, so we:
     *   1. Cache the serialized response in the configured cache store for
     *      BUSINESS_SETTINGS_TTL seconds (default 300 = 5 minutes).
     *   2. Emit Cache-Control headers so browsers and any reverse proxy
     *      (nginx, Cloudflare) can serve the response without hitting PHP.
     *   3. Bust the cache automatically when an admin updates settings
     *      (handled in the admin controller via Cache::forget).
     */
    public function index(Request $request)
    {
        $ttl = (int) config('app.business_settings_ttl', 300);
        $cacheKey = 'api:v2:business_settings';

        $payload = Cache::remember($cacheKey, $ttl, function () {
            return new BusinessSettingCollection(BusinessSetting::all());
        });

        // ETag based on the serialized payload so 304s work for clients that
        // send If-None-Match. Cheap to compute because the result is already
        // a string-able response.
        $etag = '"' . md5(json_encode($payload)) . '"';

        if ($request->headers->get('If-None-Match') === $etag) {
            return response()->noContent(304);
        }

        return response($payload)
            ->header('Cache-Control', 'public, max-age=' . $ttl)
            ->header('ETag', $etag);
    }
}
