<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Review;

class ReviewModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->filled('filter')) {
            if ($request->filter === 'pending') {
                $query->where('approved', false);
            } elseif ($request->filter === 'approved') {
                $query->where('approved', true);
            }
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalReviews = Review::count();
        $pendingCount = Review::where('approved', false)->count();
        $approvedCount = Review::where('approved', true)->count();

        return View::make('admin.reviews.index', [
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
        ]);
    }

    /**
     * Show a single review for moderation details.
     */
    public function show(Review $review)
    {
        // eager load related models used in the blade
        $review->load('user', 'product.images', 'orderItem.order');

        return View::make('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review (mark approved = true).
     */
    public function approve(Review $review)
    {
        $review->approved = true;
        $review->save();

        return Redirect::back()->with('success', 'Review approved');
    }

    /**
     * Reject (delete) a review.
     */
    public function reject(Review $review)
    {
        $review->delete();
        return Redirect::back()->with('success', 'Review deleted');
    }

    /**
     * Toggle the approval flag on a review.
     */
    public function toggle(Review $review)
    {
        $review->approved = !$review->approved;
        $review->save();

        return Redirect::back()->with('success', 'Review status updated');
    }
}
