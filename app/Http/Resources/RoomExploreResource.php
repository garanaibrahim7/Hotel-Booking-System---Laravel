<?php

namespace App\Http\Resources;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomExploreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hotel' => [
                'id' => $this->hotel->id,
                'name' => $this->hotel->name,
                'description' => $this->hotel->description,
                'address' => $this->hotel->address,
                'city_id' => $this->hotel->city_id,
                'pincode' => $this->hotel->pincode,
                'created_at' => $this->hotel->created_at,
                'updated_at' => $this->hotel->updated_at,
                'cancellation_charge' => $this->hotel->cancellation_charge,
                'city_name' => $this->hotel->city?->name,
                'state_id' => $this->hotel->city?->state_id,
                'state_name' => $this->hotel->city?->state?->name,
                'country_id' => $this->hotel->city?->state?->country_id,
                'country_name' => $this->hotel->city?->state?->country?->name,
                'country_iso_code' => $this->hotel->city?->state?->country?->iso_code,
                'currency_code' => $this->hotel->city?->state?->country?->currency_code,
                'currency_symbol' => $this->hotel->city?->state?->country?->currency_symbol,
            ],
            'rooms' => $this->rooms->map(fn ($room) => [
                'id' => $room->id,
                'title' => $room->title,
                'type' => $room->type,
                'category' => $room->category,
                'max_adults' => $room->max_adults,
                'max_children' => $room->max_children,
                'converted_price' => $room->converted_price,
                'offer_price' => $room->offer_price,
                'offer' => $room->offer,
                'coupon_code' => $room->coupon_code,
                'offer_type' => $room->offer_type,
                'user_currency_symbol' => $room->user_currency_symbol,
                'hotel_id' => $room->hotel_id,
                'images' => $room->images->isNotEmpty() ? $room->images->map(fn ($image) => [
                    'id' => $image->id,
                    'path' => $image->path ?? $image->url ?? asset('storage/' . $image->path),
                ]) : [
                    [
                        'id' => null,
                        'path' => asset('storage/room_placeholder.jpeg'),
                    ]
                ],
            ]),
            'offer' => $this->offer ?[
                'id' => $this->offer->id,
                'coupen_code' => $this->offer->coupen_code,
                'type' => $this->offer->type,
                'value' => $this->offer->value,
                'required_code' => $this->offer->required_code,
                'message' => $this->offer->message,
                'starts_from' => $this->offer->starts_from,
                'ends_at' => $this->offer->ends_at,
                'min_nights' => $this->offer->min_nights,
                'usage_limit' => $this->offer->usage_limit,
                // 'used_count' => $this->offer->used_count,
                'user_limit' => $this->offer->user_limit,
                'min_amount' => $this->offer->min_amount,
                'max_discount' => $this->offer->max_discount,
                'hotel_id' => $this->offer->hotel_id,
                'country_id' => $this->offer->country_id,
            ] : [],
        ];
    }
}
