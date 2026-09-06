<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ReviewCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $currentUserId = Auth::id();
        $collection    = $this->collection;

        // Make sure we always have a flat Eloquent Collection to count / sum.
        if (method_exists($collection, 'getCollection')) {
            $allReviews = $collection->getCollection();
        } else {
            $allReviews = collect($collection);
        }
        $perPage = 10;

        $total = $allReviews->count();
        $sum   = (float) $allReviews->sum('rating');
        $avg   = $total > 0 ? round($sum / $total, 2) : 0;

        // Build the breakdown as a string-keyed object so the JSON output is
        // an object { "5": N, "4": N, ... } and not a 0-indexed array.
        $breakdown = new \stdClass();
        for ($s = 5; $s >= 1; $s--) {
            $breakdown->{(string) $s} = (int) $allReviews->where('rating', $s)->count();
        }

        return [
            'data' => $allReviews->map(function ($data) use ($currentUserId) {
                $user = $data->user; // may be null if the user was deleted
                return [
                    'id'         => (int) $data->id,
                    'user_id'    => $user ? (int) $user->id : (int) $data->user_id,
                    'user_name'  => $user?->name ?? 'User',
                    'avatar'     => $user ? api_asset($user->avatar_original) : null,
                    'rating'     => floatval(number_format((float) $data->rating, 1, '.', '')),
                    'comment'    => $data->comment,
                    'time'       => $data->updated_at?->diffForHumans() ?? '',
                    'created_at' => $data->created_at?->toIso8601String(),
                    'is_mine'    => $currentUserId ? ((int) $data->user_id === (int) $currentUserId) : false,
                ];
            })->values(),
            'meta' => [
                'total'             => $total,
                'per_page'          => $perPage,
                'average'           => $avg,
                'rating_breakdown'  => $breakdown,
            ],
            'links' => [
                'next' => method_exists($collection, 'nextPageUrl') ? $collection->nextPageUrl() : null,
                'prev' => method_exists($collection, 'previousPageUrl') ? $collection->previousPageUrl() : null,
            ],
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200
        ];
    }
}
