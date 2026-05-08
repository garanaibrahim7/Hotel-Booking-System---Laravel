<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Discount;
use App\Models\Room;
use App\Models\RoomDetail;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoomsFindService
{
    public static function loadAvailableRoomsPaginate($checkIn, $checkOut, $hotelId = null, $cityId = null, $userLocation = null, $adults = null, $children = null, $page = 1, $perPage = 10)
    {
        $today = now();
        $userLocation = $userLocation ?? LocationService::fetchLocation();

        $query = RoomDetail::with(['images', 'hotel.city.state.country', 'rooms'])
            ->where('qty', '>', 0);

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        } elseif ($cityId) {
            $query->whereHas('hotel', fn ($q) => $q->where('city_id', $cityId));
        }

        if ($adults) {
            $query->where('max_adults', '>=', $adults);
        }

        if ($children !== null) {
            $query->where('max_children', '>=', $children);
        }

        if ($checkIn && $checkOut) {
            $query->whereHas('rooms', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', 1)
                    ->whereDoesntHave('bookingItems', function ($bi) use ($checkIn, $checkOut) {
                        $bi->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn)
                            ->whereHas('booking', fn ($b) => $b->where('status', Booking::STATUS_CONFIRMED));
                    });
            });
        }

        $paginatedRooms = $query->paginate($perPage, ['*'], 'page', $page);

        if ($paginatedRooms->isEmpty()) {
            return $paginatedRooms;
        }

        $nights = ($checkIn && $checkOut) ? Carbon::parse($checkIn)->diffInDays($checkOut) : 1;

        $groupedData = $paginatedRooms->getCollection()->groupBy('hotel_id')->map(function ($roomsForThisHotel, $hotelId) use ($userLocation, $today, $nights) {

            $hotel = $roomsForThisHotel->first()->hotel;
            $hotelCurrency = $hotel->city->state->country->currency_code;
            $exchangeRate = currencyExchangeRate($userLocation['currency_code'], $hotelCurrency);

            // Fetch discounts applicable to this specific hotel
            $discounts = Discount::where('active_status', true)
                ->where('required_code', false)
                ->where('starts_from', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
                })
                ->where(function ($q) use ($userLocation) {
                    $q->where('country_id', $userLocation['country_id'])
                        ->orWhereNull('country_id');
                })
                ->where(function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId)
                        ->orWhereNull('hotel_id');
                })
                ->where('min_nights', '<=', $nights)
                ->orderByRaw('hotel_id IS NULL ASC')
                ->orderByRaw('country_id IS NULL ASC')
                ->get();

            $applicableDiscount = $discounts->first();

            // Process only the rooms that made it onto this page
            $roomsPayload = $roomsForThisHotel->map(function ($room) use ($exchangeRate, $discounts, $userLocation, &$applicableDiscount) {
                $baseConvertedPrice = round($room->price * $exchangeRate, 2);
                $offerPrice = null;
                $discountType = null;

                if ($discounts->isNotEmpty()) {
                    $discountValue = $baseConvertedPrice;

                    foreach ($discounts as $discount) {
                        if ($discount->type === 'fixed') {
                            if ($discount->country_id && $discount->country_id != $userLocation['country_id']) {
                                continue;
                            }
                        }

                        $roomPrice = $room->price * $exchangeRate;
                        if ($discount->type === 'percentage') {
                            $discountAmount = ($roomPrice * $discount->value) / 100;

                            if ($discount->max_discount) {
                                $discountAmount = min($discountAmount, $discount->max_discount);
                            }

                            $rawDiscounted = $roomPrice - $discountAmount;
                            $currentDiscountType = $discount->value.'%';
                        } else {
                            $rawDiscounted = $roomPrice - $discount->value;
                            $currentDiscountType = $discount->value.' '.$userLocation['currency_code'];
                        }

                        $offerPrice = $rawDiscounted;

                        if ($offerPrice < $discountValue) {
                            $discountValue = $offerPrice;
                            $applicableDiscount = $discount;
                            $discountType = $currentDiscountType;
                        }
                    }
                }

                return (object) [
                    'id' => $room->id,
                    'title' => $room->title,
                    'type' => $room->type,
                    'category' => $room->category,
                    'max_adults' => $room->max_adults ?? 2,
                    'max_children' => $room->max_children ?? 0,
                    'converted_price' => $baseConvertedPrice,
                    'offer_price' => $discountValue > 0 ? $discountValue : 0,
                    'offer' => ($applicableDiscount?->message ?? $applicableDiscount?->coupen_code) ?? null,
                    'coupon_code' => $applicableDiscount?->coupen_code ?? null,
                    'offer_type' => $discountType,
                    'user_currency_symbol' => $userLocation['currency_symbol'],
                    'images' => $room->images->map(function($image){
                        $image->path = url('/').'/storage/'.$image->path;
                        return $image;
                    }),
                    'hotel_id' => $room->hotel_id,
                ];
            });

            return (object) [
                'hotel' => $hotel,
                'offer' => $applicableDiscount,
                'rooms' => $roomsPayload->values(),
            ];
        })->values();

        return new LengthAwarePaginator(
            $groupedData,
            $paginatedRooms->total(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public static function loadAvailableRooms($checkIn, $checkOut, $hotelId = null, $cityId = null, $userLocation = null, $adults = null, $children = null): Collection
    {
        $today = now();
        $userLocation = $userLocation ?? LocationService::fetchLocation();

        $query = RoomDetail::with(['images', 'hotel.city.state.country', 'rooms'])
            ->where('qty', '>', 0);

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        } elseif ($cityId) {
            $query->whereHas('hotel', fn ($q) => $q->where('city_id', $cityId));
        }

        if ($adults) {
            $query->where('max_adults', '>=', $adults);
        }

        if ($children !== null) {
            $query->where('max_children', '>=', $children);
        }

        if ($checkIn && $checkOut) {
            $query->whereDoesntHave('blocks', function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($qb) use ($checkIn, $checkOut) {
                    $qb->where('from', '<=', $checkOut)
                        ->where('to', '>=', $checkIn);
                });
            });

            $query->whereHas('rooms', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', 1)
                    ->whereDoesntHave('bookingItems', function ($bi) use ($checkIn, $checkOut) {
                        $bi->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn)
                            ->whereHas('booking', fn ($b) => $b->where('status', Booking::STATUS_CONFIRMED));
                    });
            });
        }

        $roomDetails = $query->get();
        $nights = ($checkIn && $checkOut) ? Carbon::parse($checkIn)->diffInDays($checkOut) : 1;

        return $roomDetails->groupBy('hotel_id')->map(function ($details, $hotelId) use ($userLocation, $today, $nights) {
            $hotel = $details->first()->hotel;
            $hotelCurrency = $hotel->city->state->country->currency_code;
            $exchangeRate = currencyExchangeRate($userLocation['currency_code'], $hotelCurrency);

            $discounts = Discount::where('active_status', true)
                ->where('required_code', false)
                ->where('starts_from', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
                })
                ->where(function ($q) use ($userLocation) {
                    $q->where('country_id', $userLocation['country_id'])
                        ->orWhereNull('country_id');
                })
                ->where(function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId)
                        ->orWhereNull('hotel_id');
                })
                ->where('min_nights', '<=', $nights)
                ->orderByRaw('hotel_id IS NULL ASC')
                ->orderByRaw('country_id IS NULL ASC')
                ->get();

            // Log::channel('debug')->info("Discounts : " . $discounts->count());
            // Log::channel('debug')->info("Discounts : ", compact('discounts'));

            // return (object)[
            //     'hotel'  => $hotel,
            //     'offer'  => collect(),
            //     'rooms'  => collect()
            // ];

            $applicableDiscount = $discounts->first();

            $roomsPayload = $details->map(function ($room) use ($exchangeRate, $discounts, $userLocation, &$applicableDiscount) {
                $baseConvertedPrice = round($room->price * $exchangeRate, 2);
                $offerPrice = null;
                $discountType = null;

                session()->forget('picked_discount');
                if ($discounts->isNotEmpty()) {

                    $discountValue = $baseConvertedPrice;

                    foreach ($discounts as $discount) {
                        if ($discount->type === 'fixed') {
                            if ($discount->country_id && $discount->country_id != $userLocation['country_id']) {
                                continue;
                            }
                        }

                        $roomPrice = $room->price * $exchangeRate;
                        if ($discount->type === 'percentage') {
                            $discountAmount = ($roomPrice * $discount->value) / 100;

                            if ($discount->max_discount) {
                                $discountAmount = min($discountAmount, $discount->max_discount);
                            }

                            $rawDiscounted = $roomPrice - $discountAmount;
                            $currentDiscountType = $discount->value.'%';
                        } else {
                            $rawDiscounted = $roomPrice - $discount->value;
                            $currentDiscountType = $discount->value.' '.$userLocation['currency_code'];
                        }
                        $offerPrice = $rawDiscounted;

                        // Log::info("Discount: {$discount->coupen_code} => {$offerPrice}");

                        if ($offerPrice < $discountValue) {
                            $discountValue = $offerPrice;
                            $applicableDiscount = $discount;
                            $discountType = $currentDiscountType;
                        }
                    }
                    // Log::channel('debug')->info("Discounts : " . $discountType);

                    // if ($discount->type == 'percentage') {
                    //     $discountType = $discount->value . "%";
                    //     $rawDiscounted = $room->price - ($room->price * $discount->value / 100);
                    // } else {
                    //     $discountType = round($discount->value * $exchangeRate, 2) . $userLocation['currency_code'];
                    //     $rawDiscounted = $room->price - $discount->value;
                    // }
                    // $offerPrice = round($rawDiscounted * $exchangeRate, 2);
                    session()->put('picked_discount', $applicableDiscount->coupen_code);
                }

                return (object) [
                    'id' => $room->id,
                    'title' => $room->title,
                    'type' => $room->type,
                    'category' => $room->category,
                    'max_adults' => $room->max_adults ?? 2,
                    'max_children' => $room->max_children ?? 0,
                    'converted_price' => $baseConvertedPrice,
                    'offer_price' => ($discountValue ?? 0) > 0 ? $discountValue : 0,
                    'offer' => ($applicableDiscount?->message ?? $applicableDiscount?->coupen_code) ?? null,
                    'coupon_code' => $applicableDiscount?->coupen_code ?? null,
                    'offer_type' => $discountType,
                    'user_currency_symbol' => $userLocation['currency_symbol'],
                    'images' => $room->images,
                    'hotel_id' => $room->hotel_id,
                ];
            });

            // Log::channel('debug')->info("Final Discount : " . $applicableDiscount->coupen_code);
            return (object) [
                'hotel' => $hotel,
                'offer' => $applicableDiscount,
                'rooms' => $roomsPayload,
            ];
        });
    }

    public static function loadRequiredRooms(array $roomsRequirements, $checkIn, $checkOut)
    {
        // return $roomsRequirements;
        $allSelectedRooms = collect();

        foreach ($roomsRequirements as $req) {
            if (isset($req['id']) && $req['quantity'] > 0) {
                $rooms = Room::with(['details', 'hotel.city.state.country'])
                    ->where('room_detail_id', $req['id'])
                    ->where('status', 1)
                    ->whereDoesntHave('bookingItems', function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn)
                            ->whereHas('booking', function ($q) {
                                $q->where('status', Booking::STATUS_CONFIRMED);
                            });
                    })
                    ->limit($req['quantity'])
                    ->get();

                if ($rooms->count() < $req['quantity']) {
                    // return collect();
                }

                $allSelectedRooms = $allSelectedRooms->merge($rooms);
            }
        }

        // return $allSelectedRooms;
        if ($allSelectedRooms->isEmpty()) {
            return collect();
        }

        $firstRoom = $allSelectedRooms->first();
        $hotel = $firstRoom->hotel;

        $formattedRooms = $allSelectedRooms->map(function ($room) {
            unset($room->hotel);

            return $room;
        });

        return collect([
            'rooms' => $formattedRooms,
            'hotel' => $hotel,
        ]);
    }

    public static function roomsAvailability(array $roomsRequirements, $checkIn, $checkOut) // : Collection
    {
        $allSelectedRoomsQty = collect();

        // return array_map(function ($req) {
        //     return $req['id'];
        // }, $roomsRequirements);
        foreach ($roomsRequirements as $req) {
            $totalRooms = Room::where('room_detail_id', $req['id'])
                ->where('status', 1)
                ->count();

            $bookedCount = Room::where('room_detail_id', $req['id'])
                ->whereHas('bookingItems', function ($query) use ($checkIn, $checkOut) {
                    $query->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn)
                        ->whereHas('booking', function ($q) {
                            $q->where('status', Booking::STATUS_CONFIRMED);
                        });
                })->count();

            $allSelectedRoomsQty = $allSelectedRoomsQty->push(collect([
                'room_detail_id' => $req['id'],
                'quantity' => $totalRooms - $bookedCount,
                'available' => ($totalRooms - $bookedCount) >= ($req['quantity'] ?? 1),
            ]));
        }

        return $allSelectedRoomsQty;
    }

    public static function validateSingleHotel(Collection $rooms): bool
    {
        if ($rooms->isEmpty()) {
            return false;
        }

        return $rooms->pluck('hotel_id')->unique()->count() === 1;
    }

    public static function pricingAndOffersRooms($userLocation, $nights = 1)
    {
        $userCityId = $userLocation['city_id'] ?? null;
        $currencyCode = $userLocation['currency_code'] ?? 'USD';

        if (! $userCityId) {
            return collect();
        }

        $cacheKey = "city_specials_city_{$userCityId}_curr_{$currencyCode}";

        // return Cache::get($cacheKey);
        return Cache::remember($cacheKey, 3600, function () use ($userLocation, $userCityId) {
            $today = now();

            $baseQuery = RoomDetail::query()
                ->select('room_details.*')
                ->whereHas('hotel', fn ($q) => $q->where('city_id', $userCityId))
                ->selectSub(function ($query) use ($today) {
                    $query->from('discounts')
                        ->selectRaw('CASE
                WHEN hotel_id IS NOT NULL THEN 2
                WHEN hotel_id IS NULL THEN 1
                ELSE 0 END')
                        ->where('active_status', true)
                        ->where('required_code', false)
                        ->where('starts_from', '<=', $today)
                        ->where(function ($q) use ($today) {
                            $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
                        })

                        ->where(function ($q) {
                            $q->whereColumn('hotel_id', 'room_details.hotel_id')
                                ->orWhereNull('hotel_id');
                        })
                        ->orderByRaw('CASE WHEN hotel_id IS NOT NULL THEN 2 ELSE 1 END DESC')
                        ->limit(1);
                }, 'discount_priority');

            $rooms = RoomDetail::query()
                ->fromSub($baseQuery, 'room_details')
                ->with(['hotel.city.state.country', 'images'])
                ->where('discount_priority', '>', 0)
                ->orderBy('discount_priority', 'DESC')
                ->take(5)
                ->get();

            return $rooms->map(function ($room) use ($userLocation, $today) {
                $hotel = $room->hotel;
                $hotelCurrency = $hotel->city->state->country->currency_code;

                $exchangeRate = currencyExchangeRate($userLocation['currency_code'], $hotelCurrency);

                $discount = Discount::where('active_status', true)
                    ->where('required_code', false)
                    ->where('starts_from', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
                    })
                    ->where(function ($q) use ($room) {
                        $q->where('hotel_id', $room->hotel_id)->orWhereNull('hotel_id');
                    })
                    ->orderBy('hotel_id', 'desc')
                    ->first();

                $room->converted_price = round($room->price * $exchangeRate, 2);
                $room->user_currency_symbol = $userLocation['currency_symbol'];
                $room->offer_price = null;
                $room->offer = null;

                if ($discount) {
                    $rawDiscounted = ($discount->type == 'percentage')
                        ? $room->price - ($room->price * $discount->value / 100)
                        : $room->price - $discount->value;

                    $room->offer_price = round($rawDiscounted * $exchangeRate, 2);
                    $room->offer = $discount->message ?? $discount->coupen_code;
                }

                return $room;
            });
        });
    }
}
