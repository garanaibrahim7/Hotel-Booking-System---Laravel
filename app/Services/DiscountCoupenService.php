<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DiscountCoupenService
{
    public static function validateCoupen(int $totalAmount, string $couponCode, int $nights, int $hotelId, int $userCountryId, $exchangeRate = 1)
    {

        $discount = Discount::where('coupen_code', strtoupper($couponCode))
            ->where('active_status', 1)
            ->first();

        // Log::channel('debug')->info("Discount: ", $discount->toArray());
        $convertedTotal = $totalAmount * $exchangeRate;

        if (!$discount) {
            return ['error' => "Coupon code doesn't exist."];
        }


        if ($discount->hotel_id) {
            // Log::channel('debug')->info("Dicount Code at Checkout : " . $discount->hotel_id .' - '. $hotelId);
            if ($discount->hotel_id != $hotelId) {
                return ['error' => 'This coupon is not valid For selected Hotel'];
            }
        }
        if ($discount->country_id) {
            if ($discount->country_id != $userCountryId) {
                return ['error' => 'This coupon is not valid in your country'];
            }
        } else {

            if ($discount->type !== 'percentage') {
                return ['error' => "Coupon can't be Applied Globally"];
            }
        }
        // Log::channel('debug')->info(":::: All is Well ::::");

        $now = now();
        if ($now->lt($discount->starts_from) || ($discount->ends_at && $now->gt($discount->ends_at))) {
            return ['error' => 'Coupon is expired or not yet active'];
        }


        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return ['error' => 'This coupon has reached its maximum usage limit'];
        }


        if (Auth::check() && $discount->user_limit) {
            $userUsageCount = Booking::where('user_id', Auth::id())
                ->where('discount_id', $discount->id)
                ->where('status', Booking::STATUS_CONFIRMED)
                ->count();

            if ($userUsageCount >= $discount->user_limit) {
                return ['error' => 'You have already used this coupon maximum allowed times'];
            }
        }
        
        // Log::channel('debug')->info("Discount------");

        if ($discount->min_nights > 0 && $nights < $discount->min_nights) {
            return ['error' => "This coupon requires a minimum stay of {$discount->min_nights} nights."];
        }

        if ($discount->min_amount > 0 && $convertedTotal < $discount->min_amount) {
            return ['error' => "Minimum booking amount should be {$discount->min_amount} to use this coupon."];
        }

        $discounted_amount = 0;

        if ($discount->type === 'fixed') {
            $discounted_amount = $discount->value;
        } else if ($discount->type === 'percentage') {
            $discounted_amount = ($totalAmount * $discount->value) / 100;

            if ($discount->max_discount > 0 && $discounted_amount > $discount->max_discount) {
                $discounted_amount = $discount->max_discount;
            }
        }


        if ($discounted_amount > $totalAmount) {
            $discounted_amount = $totalAmount;
        }

        // Log::channel('debug')->info('final_converted_amount: ' . round(($totalAmount - $discounted_amount) * $exchangeRate, 2));
        // Log::channel('debug')->info("Discount Validated Data : " . [
        //     'status' => true,
        //     'coupon_id' => $discount->id,
        //     'coupon_code' => $discount->coupen_code,
        //     'discount_amount' => round($discounted_amount, 2),
        //     'final_amount' => round($totalAmount - $discounted_amount, 2),
        //     'final_converted_amount' => round(($totalAmount - $discounted_amount) * $exchangeRate, 2)
        // ]);

        return [
            'status' => true,
            'coupon_id' => $discount->id,
            'coupon_code' => $discount->coupen_code,
            'discount_amount' => round($discounted_amount, 2),
            'final_amount' => round($totalAmount - $discounted_amount, 2),
            'final_converted_amount' => round(($totalAmount - $discounted_amount) * $exchangeRate, 2)
        ];
    }


    public static function getDiscountPreview(int $totalAmount, string $couponCode, int $nights, int $hotelId, int $userCountryId, $exchangeRate = 1)
    {
        $discount = Discount::where('coupen_code', strtoupper($couponCode))
            ->where('active_status', 1)
            ->first();

        if (!$discount) {
            return ['status' => false, 'error' => "Coupon Code Doesn't Exist"];
        }

        $potential_discount = 0;
        if ($discount->type === 'fixed') {
            $potential_discount = $discount->value;
        } else {
            $potential_discount = ($totalAmount * $discount->value) / 100;
            if ($discount->max_discount > 0 && $potential_discount > $discount->max_discount) {
                $potential_discount = $discount->max_discount;
            }
        }

        if ($potential_discount > $totalAmount) {
            $potential_discount = $totalAmount;
        }

        if ($discount->hotel_id && $discount->hotel_id != $hotelId) {
            return ['status' => false, 'error' => "This Coupon Is Not Valid For The Selected Hotel"];
        }

        if ($discount->country_id) {
            if ($discount->country_id != $userCountryId) {
                return ['status' => false, 'error' => "This Coupon Is Not Valid In Your Country"];
            }
        } elseif ($discount->type !== 'percentage') {
            return ['status' => false, 'error' => "Fixed Coupons Can't Be Applied Globally"];
        }

        $now = now();
        if ($now->lt($discount->starts_from) || ($discount->ends_at && $now->gt($discount->ends_at))) {
            return ['status' => false, 'error' => "Coupon Is Expired Or Not Yet Active"];
        }

        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return ['status' => false, 'error' => "This Coupon Has Reached Its Maximum Usage Limit"];
        }

        if ($discount->min_nights > 0 && $nights < $discount->min_nights) {
            return [
                'status' => false,
                'error' => "This Offer Is Limited To Min {$discount->min_nights} Nights Booking"
            ];
        }

        $convertedTotal = $totalAmount * $exchangeRate;
        if ($discount->min_amount > 0 && $convertedTotal < $discount->min_amount) {
            $diff = round($discount->min_amount - $convertedTotal, 2);
            return [
                'status' => false,
                'error' => "Add {$diff} Amount More To Save " . round($potential_discount * $exchangeRate, 2) . " Amount"
            ];
        }

        return [
            'status' => true,
            'coupon_id' => $discount->id,
            'coupon_code' => $discount->coupen_code,
            'discount_amount' => round($potential_discount, 2),
            'final_amount' => round($totalAmount - $potential_discount, 2),
            'final_converted_amount' => round(($totalAmount - $potential_discount) * $exchangeRate, 2)
        ];
    }
}
