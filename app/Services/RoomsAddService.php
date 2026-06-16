<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomDetail;
use Arr;
use Illuminate\Support\Facades\DB;

class RoomsAddService
{
    public function insertRooms(array $roomsDetail, bool $addQty)
    {
        // return $roomsDetail;
        if ($roomsDetail['room-add-option'] === 'multiple-rooms') {
            DB::transaction(function () use ($roomsDetail, $addQty) {
                $from = (int)$roomsDetail['room_number_from'];
                $to = (int)$roomsDetail['room_number_to'];
                $rooms = [];
                for ($i = $from; $i <= $to; $i++) {
                    $rooms[] = [
                        'hotel_id' => $roomsDetail['hotel_id'],
                        'room_detail_id' => $roomsDetail['room_detail_id'],
                        'room_number' => $roomsDetail['room_number_prefix'] . $i,
                        'status' => $roomsDetail['status'],
                    ];
                }
                Room::insert($rooms);
                if ($addQty)
                    RoomDetail::findOrFail($roomsDetail['room_detail_id'])->increment('qty', sizeof($rooms));
            });
        } else {
            DB::transaction(function () use ($roomsDetail, $addQty) {
                Room::create(Arr::only($roomsDetail, ['room_number', 'hotel_id', 'room_detail_id', 'status']));
                if ($addQty)
                    RoomDetail::findOrFail($roomsDetail['room_detail_id'])->increment('qty');
            });
        }
        return true;
    }
}
