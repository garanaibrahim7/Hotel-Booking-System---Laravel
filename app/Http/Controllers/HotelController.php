<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelFormRequest;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\RoomDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class  HotelController extends Controller
{
    private $amenities = [
        ['id' => 1, 'title' => 'Free Wi-Fi', 'icon' => 'bi-wifi'],
        ['id' => 2, 'title' => 'Air Conditioning', 'icon' => 'bi-snow'],
        ['id' => 3, 'title' => 'Flat-screen TV', 'icon' => 'bi-tv'],

        // Leisure & Wellness
        ['id' => 4, 'title' => 'Swimming Pool', 'icon' => 'bi-water'],
        ['id' => 5, 'title' => 'Fitness Center', 'icon' => 'bi-bicycle'],
        ['id' => 6, 'title' => 'Spa & Wellness', 'icon' => 'bi-magic'],

        // Food & Drink
        ['id' => 7, 'title' => 'Restaurant', 'icon' => 'bi-cup-hot'],
        ['id' => 8, 'title' => 'Bar / Lounge', 'icon' => 'bi-glass-cocktail'],
        ['id' => 9, 'title' => 'Room Service', 'icon' => 'bi-bell'],

        // Services & Facilities
        ['id' => 10, 'title' => 'Free Parking', 'icon' => 'bi-p-circle'],
        ['id' => 11, 'title' => '24/7 Front Desk', 'icon' => 'bi-person-badge'],
        ['id' => 12, 'title' => 'Airport Shuttle', 'icon' => 'bi-bus-front'],
        ['id' => 13, 'title' => 'Laundry Service', 'icon' => 'bi-tsunami'],

        // Business & Safety
        ['id' => 14, 'title' => 'Conference Room', 'icon' => 'bi-people'],
        ['id' => 15, 'title' => 'Safe Deposit Box', 'icon' => 'bi-safe'],
        ['id' => 16, 'title' => 'Pet Friendly', 'icon' => 'bi-heart'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $country = request('country');
        $search = request('search');

        // return $country;
        $query = Hotel::with('city.state.country');
        $countries = $query->get()->pluck('city.state.country.name', 'city.state.country.id');

        if ($country) {
            $query->whereHas('city.state', function ($q) use ($country) {
                $q->where('country_id', $country);
            });
        }
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhereHas('city', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('city.state', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('city.state.country', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $hotels = $query->withCount('bookings')->orderBy('bookings_count', 'DESC')->get();
        // $hotels = Hotel::with('city.state.country')->get();
        // return $countries;

        return view('admin.hotel.list', compact('hotels', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('admin.hotel.add', ['cities' => City::all(), 'amenities' => $this->amenities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HotelFormRequest $request)
    {
        // return $request;
        $hotelRequest = $request->validated();
        $hotel = Hotel::create($hotelRequest);

        if ($request->has('amenities')) {
            foreach ($request->amenities as $amenity) {
                $hotel->amenities()->create([
                    'title' => $this->amenities[$amenity]['title'],
                    'icon' => $this->amenities[$amenity]['icon'],
                ]);
            }
        }

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $path = $file->store('/assets/hotel', 'public');
                $hotel->images()->create([
                    'path' => $path,
                ]);
            }
        }
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        return view('admin.hotel.info', compact('hotel'));
        $countries = array_map(function ($country) {
            return [
                'currency' => $country['currency'],
                'symbol' => $country['symbol'],
            ];
        }, config('countries'));

        $categories = RoomDetail::select(['id', 'hotel_id', 'description', 'category', 'type', 'price', 'qty', 'max_adults', 'max_children'])
            ->with(['hotel:id,name,city_id'])
            // ->where('hotel_id', $id)
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
    public function edit(Hotel $hotel)
    {
        return view('admin.hotel.edit', [
            'hotel' => $hotel,
            'cities' => City::all(),
            'amenities' => $this->amenities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HotelFormRequest $request, Hotel $hotel)
    {

        // return $request;
        $hotel->update($request->validated());

        $hotel->amenities()->delete();
        if ($request->has('amenities')) {
            $masterAmenities = collect($this->amenities);

            foreach ($request->amenities as $amenity) {
                $hotel->amenities()->create([
                    'title' => $this->amenities[$amenity]['title'],
                    'icon' => $this->amenities[$amenity]['icon'],
                ]);
            }
        }

        if ($request->hasFile('replaceImages')) {
            foreach ($request->file('replaceImages') as $imageId => $file) {
                $image = Image::find($imageId);
                if ($image) {

                    $this->deleteImage($image->path);


                    $newPath = $file->store('assets/hotel', 'public');
                    $image->update(['path' => $newPath]);
                }
            }
        }


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('assets/hotel', 'public');
                $hotel->images()->create(['path' => $path]);
            }
        }


        if ($request->deleteImages) {
            foreach ($request->deleteImages as $imageId) {
                $image = Image::find($imageId);
                if ($image) {
                    $this->deleteImage($image->path);
                    $image->delete();
                }
            }
        }

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel ' . $hotel->name . ' Updated Successfully!');
    }

    private function deleteImage(string $imagePath)
    {
        Storage::disk('public')->delete($imagePath);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        foreach ($hotel->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $hotel->amenities()->delete();
        $hotel->images()->delete();
        $hotel->delete();
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel Deleted Successfully');
    }
}
