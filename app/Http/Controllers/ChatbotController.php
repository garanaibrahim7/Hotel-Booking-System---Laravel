<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\Hotel;
use App\Models\RoomDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json([
                'text' => 'Hello! I am Diksha, your virtual assistant. How can I help you today?',
                'options' => $this->getDefaultOptions()
            ]);
        }

        $lowerMsg = strtolower($message);

        // 1. GREETINGS
        if ($this->matchPattern($lowerMsg, ['hi', 'hello', 'hey', 'greetings', 'hola', 'start', 'restart', 'diksha', 'ask diksha', 'menu'])) {
            return response()->json([
                'text' => "Namaste! 🙏 I am **Diksha**, your virtual assistant for **XYZ Hotels**.\n\nI can help you search hotels, find available room types, check your booking status, or explain cancellation/refund policies. How may I assist you today?",
                'options' => $this->getDefaultOptions()
            ]);
        }

        // 2. BOOKING STATUS CHECK
        if ($this->matchPattern($lowerMsg, ['booking status', 'pnr', 'track booking', 'my booking', 'booking details', 'check booking'])) {
            $refNumber = $this->extractReferenceNumber($message);
            if ($refNumber) {
                return response()->json($this->getBookingStatusResponse($refNumber));
            }
            return response()->json([
                'text' => "Please provide your **Booking Reference Number** (e.g., `1776847410LW730001`) to check your booking details.",
                'options' => [
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ]);
        }

        // Check if there's a potential reference number in the message directly
        $possibleRef = $this->extractReferenceNumber($message);
        if ($possibleRef && ($this->matchPattern($lowerMsg, ['status', 'booking', 'track', 'check']) || strlen($possibleRef) >= 12)) {
            return response()->json($this->getBookingStatusResponse($possibleRef));
        }

        // 3. HOTEL SEARCH
        if ($this->matchPattern($lowerMsg, ['hotel', 'hotels', 'stay', 'where', 'location', 'cities', 'city'])) {
            $cities = City::all();
            $matchedCity = null;
            foreach ($cities as $city) {
                if (str_contains($lowerMsg, strtolower($city->name))) {
                    $matchedCity = $city;
                    break;
                }
            }

            if ($matchedCity) {
                return response()->json($this->getHotelsInCityResponse($matchedCity));
            }

            return response()->json($this->getAllHotelsResponse());
        }

        // 4. ROOM DETAILS / TYPES
        if ($this->matchPattern($lowerMsg, ['room', 'rooms', 'price', 'rates', 'types', 'category', 'categories', 'suite', 'deluxe', 'premium', 'standard', 'single', 'double', 'twin', 'family'])) {
            $categories = ['standard', 'deluxe', 'premium', 'luxury', 'suite'];
            $types = ['single', 'double', 'twin', 'family'];

            $matchedCategory = null;
            $matchedType = null;

            foreach ($categories as $cat) {
                if (str_contains($lowerMsg, $cat)) {
                    $matchedCategory = $cat;
                }
            }
            foreach ($types as $t) {
                if (str_contains($lowerMsg, $t)) {
                    $matchedType = $t;
                }
            }

            return response()->json($this->getRoomsResponse($matchedCategory, $matchedType));
        }

        // 5. CANCELLATION / REFUND POLICY
        if ($this->matchPattern($lowerMsg, ['cancel', 'cancellation', 'refund', 'charge', 'charges', 'policy'])) {
            return response()->json([
                'text' => "### Cancellation & Refund Policy 💳\n\n- **How to Cancel:** You can cancel your booking directly by visiting your profile, navigating to **My Bookings**, selecting the booking, and clicking **Cancel Booking**.\n- **Cancellation Charges:** Vary by hotel (ranges from 0% to 15% of the total amount). Ask me about a specific hotel's policy (e.g. *'cancellation policy for Burj Al Arab'*).\n- **Refund Processing:** Approved refunds are automatically credited back to your original payment method (Stripe/Razorpay) within 5-7 business days.",
                'options' => [
                    ['label' => 'View Hotels', 'value' => 'show hotels'],
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ]);
        }

        // Check if user asked cancellation policy for a specific hotel
        foreach (Hotel::all() as $hotel) {
            if (str_contains($lowerMsg, strtolower($hotel->name))) {
                if ($this->matchPattern($lowerMsg, ['cancel', 'charge', 'policy'])) {
                    return response()->json([
                        'text' => "**{$hotel->name}** cancellation policy:\n\n- **Cancellation Charge:** " . ($hotel->cancellation_charge > 0 ? "{$hotel->cancellation_charge}% of the total booking cost." : "Free Cancellation (No charge!).") . "\n- **Policy details:** Eligible cancellations made prior to arrival date will be processed immediately. Refunds are sent back to the payment provider.",
                        'options' => [
                            ['label' => 'View Rooms in Hotel', 'value' => "rooms in {$hotel->name}"],
                            ['label' => 'Back to Menu', 'value' => 'menu']
                        ]
                    ]);
                }
            }
        }

        // 6. AMENITIES
        if ($this->matchPattern($lowerMsg, ['amenity', 'amenities', 'facility', 'facilities', 'wifi', 'pool', 'swimming', 'parking', 'gym', 'spa', 'ac', 'restaurant'])) {
            foreach (Hotel::all() as $hotel) {
                if (str_contains($lowerMsg, strtolower($hotel->name))) {
                    $amenities = $hotel->amenities;
                    if ($amenities->count() > 0) {
                        $list = $amenities->pluck('name')->map(fn($item) => "- {$item}")->implode("\n");
                        return response()->json([
                            'text' => "### Amenities at **{$hotel->name}** 🌟\n\nHere are the amenities offered at {$hotel->name}:\n\n{$list}",
                            'options' => [
                                ['label' => 'View Rooms', 'value' => "rooms in {$hotel->name}"],
                                ['label' => 'Back to Menu', 'value' => 'menu']
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'text' => "We offer premium facilities including Free Wi-Fi, Air Conditioning, 24/7 Front Desk, and Room Service at **{$hotel->name}**.",
                            'options' => [
                                ['label' => 'Back to Menu', 'value' => 'menu']
                            ]
                        ]);
                    }
                }
            }

            return response()->json([
                'text' => "### Hotel Amenities 🏨\n\nOur hotels offer top-tier amenities to ensure a comfortable stay. Typical facilities include:\n\n- 🛜 Free High-Speed Wi-Fi\n- ❄️ Air Conditioning\n- 🏊 Swimming Pool / Spa\n- 🚗 Free Secure Parking\n- 🍽️ In-house Fine Dining & Room Service\n- 🛎️ 24/7 Reception & Front Desk\n\nYou can ask about amenities for a specific hotel, e.g. *'amenities at The Taj Mahal Palace'*.",
                'options' => [
                    ['label' => 'List Hotels', 'value' => 'show hotels'],
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ]);
        }

        // 7. DEFAULT FALLBACK
        return response()->json([
            'text' => "I apologize, but I couldn't quite find details about that. 🧐\n\nI can assist you better with these options below, or you can check for hotel cities or booking references directly.",
            'options' => $this->getDefaultOptions()
        ]);
    }

    private function matchPattern(string $message, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($message, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function extractReferenceNumber(string $message): ?string
    {
        if (preg_match('/[0-9]{8,}[A-Z0-9]+/i', $message, $matches)) {
            return $matches[0];
        }
        if (preg_match('/[0-9]{10,}/', $message, $matches)) {
            return $matches[0];
        }
        return null;
    }

    private function getDefaultOptions(): array
    {
        return [
            ['label' => '🏨 Search Hotels', 'value' => 'show hotels'],
            ['label' => '🛏️ Check Room Types', 'value' => 'room types'],
            ['label' => '📅 Track Booking Status', 'value' => 'booking status'],
            ['label' => '💳 Cancellation & Refund', 'value' => 'cancellation policy']
        ];
    }

    private function getBookingStatusResponse(string $reference): array
    {
        $booking = Booking::with(['hotel', 'items.room'])->where('reference_number', $reference)->first();

        if (!$booking) {
            return [
                'text' => "⚠️ I couldn't find any booking with reference number: **{$reference}**.\n\nPlease double check the number and try again.",
                'options' => [
                    ['label' => 'Check Status Again', 'value' => 'booking status'],
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ];
        }

        $statusText = 'Unknown';
        switch ($booking->status) {
            case Booking::STATUS_PENDING:
                $statusText = '🔴 Pending (Awaiting Payment)';
                break;
            case Booking::STATUS_CONFIRMED:
                $statusText = '🟢 Confirmed';
                break;
            case Booking::STATUS_FAILED:
                $statusText = '🔴 Failed';
                break;
            case Booking::STATUS_PROCESSING:
                $statusText = '🔵 Processing';
                break;
            case Booking::STATUS_CANCELLED:
                $statusText = '⚫ Cancelled';
                break;
            case Booking::STATUS_REJECTED:
                $statusText = '🔴 Rejected';
                break;
            case Booking::STATUS_REFUNDED:
                $statusText = '🟣 Refunded';
                break;
        }

        $checkIn = $booking->arrival ? $booking->arrival->format('d M Y') : 'N/A';
        $checkOut = $booking->leaved ? $booking->leaved->format('d M Y') : 'N/A';

        $html = "
        <div class='chatbot-booking-card p-3 rounded shadow-sm border border-secondary bg-light text-dark'>
            <div class='d-flex justify-content-between align-items-center mb-2'>
                <span class='badge bg-dark'>Ref: {$booking->reference_number}</span>
                <span class='fw-bold text-success' style='font-size:12px;'>{$statusText}</span>
            </div>
            <h6 class='fw-bold mb-1 text-dark'>{$booking->hotel->name}</h6>
            <p class='small text-muted mb-2' style='font-size: 11px;'><i class='bi bi-geo-alt-fill'></i> {$booking->hotel->address}</p>
            <div class='row g-2 mb-2 text-center' style='font-size: 11px;'>
                <div class='col-6 border-end'>
                    <span class='d-block text-muted tiny'>CHECK-IN</span>
                    <strong class='text-dark'>{$checkIn}</strong>
                </div>
                <div class='col-6'>
                    <span class='d-block text-muted tiny'>CHECK-OUT</span>
                    <strong class='text-dark'>{$checkOut}</strong>
                </div>
            </div>
            <hr class='my-2'>
            <div class='d-flex justify-content-between align-items-center' style='font-size:12px;'>
                <span>Guest: <strong>{$booking->guest_name}</strong></span>
                <span class='fw-bold text-dark fs-6'>Total: " . number_format($booking->total_amount, 2) . " {$booking->currency}</span>
            </div>
        </div>";

        return [
            'text' => "Here are the details for your booking **{$booking->reference_number}**:",
            'html' => $html,
            'options' => [
                ['label' => 'Back to Menu', 'value' => 'menu']
            ]
        ];
    }

    private function getHotelsInCityResponse(City $city): array
    {
        $hotels = Hotel::where('city_id', $city->id)->get();

        if ($hotels->isEmpty()) {
            return [
                'text' => "Currently, we don't have any hotels listed in **{$city->name}**.",
                'options' => [
                    ['label' => 'View All Hotels', 'value' => 'show hotels'],
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ];
        }

        $html = "<div class='chatbot-hotel-list d-flex flex-column gap-2' style='max-height:240px; overflow-y:auto;'>";
        foreach ($hotels as $hotel) {
            $exploreUrl = route('client.room.explore', $hotel->id);
            $desc = Str::limit($hotel->description, 80);
            $html .= "
            <div class='chatbot-hotel-item p-2 rounded border bg-white text-dark d-flex gap-2 shadow-sm'>
                <div class='flex-grow-1'>
                    <h7 class='fw-bold m-0 d-block text-dark' style='font-size:12px;'>{$hotel->name}</h7>
                    <small class='text-muted d-block' style='font-size:10px;'><i class='bi bi-geo-alt'></i> {$hotel->address}</small>
                    <p class='m-0 text-secondary' style='font-size:11px;'>{$desc}</p>
                    <div class='d-flex justify-content-between align-items-center mt-1'>
                        <span class='text-success fw-bold' style='font-size:10px;'><i class='bi bi-tag'></i> Free Cancellation</span>
                        <a href='{$exploreUrl}' target='_blank' class='btn btn-xs btn-dark py-0 px-2 fw-semibold rounded-pill' style='font-size:10px;'>Book Now</a>
                    </div>
                </div>
            </div>";
        }
        $html .= "</div>";

        return [
            'text' => "Found **{$hotels->count()}** hotel(s) in **{$city->name}**:",
            'html' => $html,
            'options' => [
                ['label' => 'Back to Menu', 'value' => 'menu']
            ]
        ];
    }

    private function getAllHotelsResponse(): array
    {
        $hotels = Hotel::with('city')->take(5)->get();

        $html = "<div class='chatbot-hotel-list d-flex flex-column gap-2' style='max-height:240px; overflow-y:auto;'>";
        foreach ($hotels as $hotel) {
            $exploreUrl = route('client.room.explore', $hotel->id);
            $desc = Str::limit($hotel->description, 85);
            $cityName = $hotel->city?->name ?? 'India';
            $html .= "
            <div class='chatbot-hotel-item p-2 rounded border bg-white text-dark shadow-sm'>
                <h7 class='fw-bold m-0 d-block text-dark' style='font-size:12px;'>{$hotel->name}</h7>
                <small class='text-muted d-block' style='font-size:10px;'><i class='bi bi-geo-alt'></i> {$cityName} - {$hotel->address}</small>
                <p class='m-0 text-secondary my-1' style='font-size:11px;'>{$desc}</p>
                <div class='d-flex justify-content-between align-items-center mt-1'>
                    <span class='text-success fw-bold' style='font-size:10px;'><i class='bi bi-shield-check'></i> Premium Hotel</span>
                    <a href='{$exploreUrl}' target='_blank' class='btn btn-xs btn-dark py-0 px-2 fw-semibold rounded-pill' style='font-size:10px;'>View Rooms</a>
                </div>
            </div>";
        }
        $html .= "</div>";

        return [
            'text' => "Here are some of our top luxury hotels globally:",
            'html' => $html,
            'options' => [
                ['label' => 'Back to Menu', 'value' => 'menu']
            ]
        ];
    }

    private function getRoomsResponse(?string $category, ?string $type): array
    {
        $query = RoomDetail::with('hotel.city');

        if ($category) {
            $query->where('category', $category);
        }
        if ($type) {
            $query->where('type', $type);
        }

        $rooms = $query->take(4)->get();

        if ($rooms->isEmpty()) {
            return [
                'text' => "I couldn't find any specific rooms matching " . ($category ? "**{$category}** " : "") . ($type ? "**{$type}** " : "") . "right now.",
                'options' => [
                    ['label' => 'Show All Rooms', 'value' => 'room types'],
                    ['label' => 'Back to Menu', 'value' => 'menu']
                ]
            ];
        }

        $html = "<div class='chatbot-room-list d-flex flex-column gap-2' style='max-height:240px; overflow-y:auto;'>";
        foreach ($rooms as $room) {
            $exploreUrl = route('client.room.explore', $room->hotel_id);
            $title = Str::title($room->category) . " - " . Str::title($room->type);
            $localPrice = number_format($room->price, 2);
            $html .= "
            <div class='chatbot-room-item p-2 rounded border bg-white text-dark shadow-sm'>
                <div class='d-flex justify-content-between align-items-start'>
                    <div>
                        <h7 class='fw-bold m-0 d-block text-dark' style='font-size:12px;'>{$title}</h7>
                        <small class='text-muted d-block mb-1' style='font-size:10px;'>{$room->hotel->name} ({$room->hotel->city->name})</small>
                    </div>
                    <span class='badge bg-warning text-dark fw-bold' style='font-size:10px;'>₹{$localPrice}/night</span>
                </div>
                <div class='d-flex justify-content-between align-items-center mt-1' style='font-size:11px;'>
                    <span class='text-secondary'><i class='bi bi-people'></i> Max: " . ($room->max_adults) . " Adults</span>
                    <a href='{$exploreUrl}' target='_blank' class='btn btn-xs btn-dark py-0 px-2 fw-semibold rounded-pill' style='font-size:10px;'>Book Room</a>
                </div>
            </div>";
        }
        $html .= "</div>";

        $filtersText = "matching options:";
        if ($category || $type) {
            $filtersText = "for " . ($category ? "**" . Str::title($category) . "** " : "") . ($type ? "**" . Str::title($type) . "** " : "") . "rooms:";
        }

        return [
            'text' => "Here are the top available room categories {$filtersText}",
            'html' => $html,
            'options' => [
                ['label' => 'Standard Rooms', 'value' => 'standard rooms'],
                ['label' => 'Deluxe Rooms', 'value' => 'deluxe rooms'],
                ['label' => 'Suites', 'value' => 'suite rooms'],
                ['label' => 'Back to Menu', 'value' => 'menu']
            ]
        ];
    }
}
