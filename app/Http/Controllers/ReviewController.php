<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Notifications\ReviewWarningNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::withTrashed()
            ->with(['user', 'hotel'])
            ->latest()
            ->paginate(10);

        return view('admin.reviews.list', compact('reviews'));
    }

    public function toggle(Review $review)
    {
        // return $review;
        if ($review->trashed()) {
            $review->restore();
            return back()->with('success', 'Review has been Restored');
        }

        $user = $review->user;
        $hotelName = $review->hotel->name;
        $review->delete();

        $user->notify(new ReviewWarningNotification($review, $hotelName));
        return back()->with('success', 'Review has been Removed');
    }

    public function create($reference_number, $rating)
    {
        session()->put('redirect_after_review', url()->previous());
        return view('client.reviews.make-review', compact('reference_number', 'rating'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'cleaning' => 'nullable|numeric|min:1|max:5',
            'services' => 'nullable|numeric|min:1|max:5',
            'food' => 'nullable|numeric|min:1|max:5',
            'hospitality' => 'nullable|numeric|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $booking = Booking::where('reference_number', $validated['reference_number'])
            ->firstOrFail();
        Review::create([
            'hotel_id' => $booking->hotel_id,
            'user_id' => Auth::id(),
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'cleaning' => $validated['cleaning'] ?? null,
            'services' => $validated['services'] ?? null,
            'food' => $validated['food'] ?? null,
            'hospitality' => $validated['hospitality'] ?? null,
            'comment' => $validated['comment'] ?? null,
        ]);
        return redirect(session()->get('redirect_after_review'))->with('success', 'Your Review Has been Published');
    }

    public function destroy(Review $review)
    {
        return back()->with('success', 'Your Review has been Removed');
    }
}
