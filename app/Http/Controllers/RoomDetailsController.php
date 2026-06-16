<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomDetailsRequest;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\RoomDetail;
use App\Services\RoomsAddService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RoomDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $hotel = request('hotel');

        $query = RoomDetail::select(['id', 'hotel_id', 'description', 'category', 'type', 'price', 'qty', 'max_adults', 'max_children'])
            ->with(['hotel:id,name,city_id', 'hotel.city', 'hotel.city.state', 'hotel.city.state.country', 'images']);

        // $hotels = Hotel::get(['id', 'name'])->pluck('name', 'id');
        // return $hotels;

        if ($hotel) {
            $query->where('hotel_id', $hotel);
        }
        if ($search) {
            $query->where('type', 'like', "%%{$search}%")
                ->orWhere('category', 'like', "%%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhereHas('hotel', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('hotel.city', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('hotel.city.state', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('hotel.city.state.country', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $query->withCount(['rooms' => function ($query) {
            $query->where('status', 1);
        }])
            ->orderBy('id', 'desc');

        $categories = $query->paginate(10);

        return view('admin.categories.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = ['single', 'double', 'twin', 'family'];
        $categories = ['standard', 'suite', 'deluxe', 'premium', 'luxury'];
        $hotels = Hotel::with('city.state.country')->get()->map(function ($hotel) {
            return (object) [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'currency_code' => $hotel->city->state->country->currency_code ?? 'USD',
                'currency_symbol' => $hotel->city->state->country->currency_symbol ?? '$',
            ];
        });
        // $hotels = Hotel::select(['id', 'name', 'city_id'])
        //     ->with('city.state.country')
        //     ->get();
        session()->put('room_detail_add_previous_url', url()->previous());

        return view('admin.categories.add', compact('hotels', 'types', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomDetailsRequest $request, RoomsAddService $service)
    {
        // return $request;
        try {
            $roomDetail = RoomDetail::create($request->only(['hotel_id', 'type', 'category', 'description', 'qty', 'price', 'max_adults', 'max_children']));
            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $file) {
                    $paths[] = [
                        'path' => $file->store('assets/rooms', 'public'),
                    ];
                }

                $roomDetail->images()->createMany($paths);
            }
            if (
                $request->has(['room_number', 'room_number_prefix', 'room_number_from']) &&
                $request['qty'] != 0
            ) {
                if (
                    $request['qty'] == 1 &&
                    ! empty($request['room_number'])
                ) {
                    $roomsDetail = $request->only([
                        'room_number',
                        'hotel_id',
                    ]);
                    $roomsDetail['room_detail_id'] = $roomDetail->id;
                    $roomsDetail['status'] = true;
                    $roomsDetail['room-add-option'] = 'single-room';

                    $service->insertRooms($roomsDetail, false);
                }
                if (
                    $request['qty'] > 1 &&
                    $request->filled(['room_number_prefix', 'room_number_from'])
                ) {
                    $roomsDetail = $request->only([
                        'room_number_prefix',
                        'room_number_from',
                        'hotel_id',
                    ]);

                    $roomsDetail['room_number_to'] = $roomsDetail['room_number_from'] + (int) $roomDetail->qty - 1;
                    $roomsDetail['room_detail_id'] = $roomDetail->id;
                    $roomsDetail['status'] = true;
                    $roomsDetail['room-add-option'] = 'multiple-rooms';

                    $service->insertRooms($roomsDetail, false);
                }
            }

            return redirect(session()->get('room_detail_add_previous_url') ?? url()->previous())->with('success', 'Room Category and Rooms Added');
        } catch (Exception $e) {
            Log::channel('debug')->info("Exception at RoomDetailController on Store Action : {$e->getMessage()}");

            return back()->with('error', 'Something not working');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $countries = array_map(function ($country) {
            return [
                'currency' => $country['currency'],
                'symbol' => $country['symbol'],
            ];
        }, config('countries'));

        $categories = RoomDetail::select(['id', 'hotel_id', 'description', 'category', 'type', 'price', 'qty', 'max_adults', 'max_children'])
            ->with(['hotel:id,name,city_id'])
            ->where('hotel_id', $id)
            ->withCount(['rooms' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.categories.list', compact('categories', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomDetail $category)
    {
        $types = ['single', 'double', 'twin', 'family'];
        $categories = ['standard', 'suite', 'deluxe', 'premium', 'luxury'];
        $hotel = Hotel::with('city.state.country')
            ->findOrFail($category->hotel_id);
        $hotel = (object) [
            'id' => $hotel->id,
            'name' => $hotel->name,
            'currency_code' => $hotel->city->state->country->currency_code ?? 'USD',
            'currency_symbol' => $hotel->city->state->country->currency_symbol ?? '$',
        ];
        session()->put('room_detail_add_previous_url', url()->previous());

        return view('admin.categories.edit', compact('types', 'categories', 'hotel', 'category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomDetailsRequest $request, RoomDetail $category)
    {
        // return $request;
        $category->update($request->only(['price', 'description', 'type', 'category', 'max_adults', 'max_children']));

        if ($request->deleteImages) {
            foreach ($request->deleteImages as $imageId) {
                $image = Image::find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('assets/rooms', 'public');
                $category->images()->create(['path' => $path]);
            }
        }

        return redirect(session()->get('room_detail_add_previous_url') ?? route('admin.categories.index'))->with('success', 'Room Category and Rooms Added');

        return redirect()->route('admin.categories.index')->with('success', 'Room Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomDetail $category)
    {
        $category->delete();

        return back()->with('success', 'Room Category Deleted with All Rooms Data');
    }
}
