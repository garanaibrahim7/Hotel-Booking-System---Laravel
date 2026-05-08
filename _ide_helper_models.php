<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $icon
 * @property int $amenityable_id
 * @property string $amenityable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $amenityable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereAmenityableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereAmenityableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenities whereUpdatedAt($value)
 */
	class Amenities extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $hotel_id
 * @property int $status
 * @property string $reference_number
 * @property int|null $discount_id
 * @property numeric $discount_amount
 * @property numeric $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $instructions
 * @property numeric $sub_amount
 * @property string $currency
 * @property string $guest_name
 * @property string $guest_email
 * @property string $guest_phone
 * @property string|null $arrival
 * @property string|null $leaved
 * @property-read mixed $stay_dates
 * @property-read \App\Models\Hotel|null $hotel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Review|null $review
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereArrival($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuestPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereLeaved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereSubAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUserId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property int $room_id
 * @property \Illuminate\Support\Carbon $check_in
 * @property \Illuminate\Support\Carbon $check_out
 * @property numeric $price_at_booking
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\Room $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereCheckOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem wherePriceAtBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingItem whereUpdatedAt($value)
 */
	class BookingItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Hotel> $hotels
 * @property-read int|null $hotels_count
 * @property-read mixed $location_details
 * @property-read \App\Models\State $state
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereStateId($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $iso_code
 * @property string $currency_code
 * @property string $currency_symbol
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\State> $states
 * @property-read int|null $states_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $coupen_code
 * @property string $type
 * @property numeric $value
 * @property int $required_code
 * @property string|null $message
 * @property string|null $hotel_id
 * @property int $active_status
 * @property \Illuminate\Support\Carbon $starts_from
 * @property \Illuminate\Support\Carbon $ends_at
 * @property int $min_nights
 * @property int|null $usage_limit
 * @property int $used_count
 * @property int|null $user_limit
 * @property int|null $min_amount
 * @property numeric|null $max_discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $country_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $formatted_value
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCoupenCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMaxDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMinNights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereRequiredCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereStartsFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUsedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUserLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValue($value)
 */
	class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $address
 * @property int|null $city_id
 * @property string $pincode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property numeric $cancellation_charge
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Amenities> $amenities
 * @property-read int|null $amenities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoomDetail> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereCancellationCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hotel whereUpdatedAt($value)
 */
	class Hotel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $imageable_type
 * @property int $imageable_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $imageable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property string $gateway
 * @property numeric $amount
 * @property numeric|null $converted_amount
 * @property string|null $paid_currency
 * @property numeric|null $exchange_rate from user currency to hotel currency
 * @property string $currency
 * @property int $status 0:pending, 1:success, 2:failed, 3:processing
 * @property string|null $session_id
 * @property string|null $payment_intent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking|null $booking
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereConvertedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $hotel_id
 * @property int $user_id
 * @property int|null $booking_id
 * @property int $rating
 * @property int|null $cleaning
 * @property int|null $services
 * @property int|null $food
 * @property int|null $hospitality
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Hotel $hotel
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCleaning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereFood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereHospitality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review withoutTrashed()
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $hotel_id
 * @property int $room_detail_id
 * @property string $room_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingItem> $bookingItems
 * @property-read int|null $booking_items_count
 * @property-read \App\Models\RoomDetail $details
 * @property-read \App\Models\Hotel $hotel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $hotel_id
 * @property string $type
 * @property string $category
 * @property string $description
 * @property int $qty
 * @property numeric $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $max_adults
 * @property int|null $max_children
 * @property-read mixed $local_price
 * @property-read mixed $title
 * @property-read \App\Models\Hotel $hotel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereMaxAdults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereMaxChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomDetail whereUpdatedAt($value)
 */
	class RoomDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $cities
 * @property-read int|null $cities_count
 * @property-read \App\Models\Country $country
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereName($value)
 */
	class State extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\UserProfile|null $profile
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $gender
 * @property string|null $dob
 * @property string|null $address
 * @property int|null $city_id
 * @property string|null $pincode
 * @property string|null $id_type
 * @property string|null $id_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $pic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereIdType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUserId($value)
 */
	class UserProfile extends \Eloquent {}
}

