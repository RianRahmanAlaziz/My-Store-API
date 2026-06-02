<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;
    public function index(Request $request, Product $product)
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $reviews = Review::query()
            ->with('user')
            ->where('product_id', $product->id)
            ->when(
                $request->rating,
                fn($query, $rating) =>
                $query->where('rating', $rating)
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            ReviewResource::collection($reviews),
            'Reviews retrieved successfully'
        );
    }

    public function store(StoreReviewRequest $request, Product $product)
    {
        if ($request->order_id) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $request->user()->id)
                ->where('order_status', 'completed')
                ->first();

            if (! $order) {
                return $this->error(
                    'You can only review completed orders',
                    422
                );
            }
        }

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'order_id' => $request->order_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return $this->created(
            new ReviewResource($review->load('user')),
            'Review submitted successfully'
        );
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        abort_if($review->user_id !== $request->user()->id, 403);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return $this->success(
            new ReviewResource($review->load('user')),
            'Review updated successfully'
        );
    }

    public function destroy(Request $request, Review $review)
    {
        abort_if($review->user_id !== $request->user()->id, 403);

        $review->delete();

        return $this->deleted('Review deleted successfully');
    }
}
