@extends('layouts.adminlte')

@section('title', 'Dashboard')

@section('content')


    <div class="card shadow-sm border-0 my-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark w-25"><i class="bi bi-buildings me-2"></i>{{ __('hotel.list_title') }}</h5>

                <div class="d-flex flex-column gap-2 w-100">
                    <div class="d-flex gap-2 justify-content-end">
                        @foreach ($countries as $id => $country)
                            <a href="{{ route('admin.hotels.index', request('country') != $id ? ['country' => $id] : []) }}"
                                class="btn btn-{{ request('country') == $id ? '' : 'outline-' }}dark">{{ \Str::title($country) }}</a>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <form method="get" class="w-25">
                            <input type="text" name="search" class="form-control"
                                placeholder="{{ __('hotel.placeholders.search') }}" value="{{ request('search') }}"
                                onchange="this.form.submit()">
                        </form>
                        <a class="btn btn-dark shadow-sm border px-3" href="{{ route('admin.hotels.create') }}">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('hotel.add_hotel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px">#</th>
                            <th>{{ __('hotel.name') }}</th>
                            <th>{{ __('hotel.city') }} / {{ __('hotel.country') }}</th>
                            <th class="text-center">{{ __('hotel.bookings') }}</th>
                            <th class="text-center">{{ __('hotel.cancellation_charge') }}</th>
                            <th class="text-center">{{ __('hotel.status') }}</th>
                            <th class="text-end pe-4">{{ __('hotel.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hotels as $key => $hotel)
                            <tr>
                                <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $hotel->images->first() ? asset('storage/' . $hotel->images->first()->path) : asset('images/placeholder.jpg') }}"
                                            class="rounded shadow-sm me-3"
                                            style="width: 50px; height: 50px; object-fit: cover;" />
                                        <div>
                                            <div class="fw-bold text-dark">{{ $hotel->name }}</div>
                                            <small class="text-muted d-block"
                                                style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $hotel->address }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block fw-semibold">{{ $hotel->city->name }}</span>
                                    <small class="text-muted text-uppercase"
                                        style="font-size: 0.7rem;">{{ $hotel->city->state->country->name }}</small>
                                </td>
                                <td class="text-center">
                                    @if ($hotel->bookings_count && $hotel->bookings_count > 0)
                                        <a href="{{ route('admin.bookings.index', ['hotel' => $hotel->id]) }}"
                                            class="btn btn-outline-primary p-1">
                                            <i class="bi bi-eye"></i>
                                            {{ $hotel->bookings_count ?? '0' }}
                                            Bookings
                                        </a>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger-subtle px-3 py-2">
                                            No Bookings
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-danger border border-danger-subtle px-3 py-2">
                                        {{ $hotel->user_currency_symbol ?? '$' }}{{ number_format($hotel->cancellation_charge, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        {{-- <input class="form-check-input" type="checkbox" role="switch" checked disabled> --}}
                                        <span class="small text-success">{{ __('hotel.status_running') }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('admin.categories.show', $hotel->id) }}"
                                            class="btn btn-white btn-sm" title="View Available Categories">
                                            <i class="bi bi-door-open text-dark"></i>
                                        </a>
                                        <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="btn btn-white btn-sm"
                                            title="View Details">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="btn btn-white btn-sm"
                                            title="Edit">
                                            <i class="bi bi-pencil-square text-warning"></i>
                                        </a>
                                        <button class="btn btn-white btn-sm" onclick="deleteAlert({{ $hotel }})"
                                            title="Delete">
                                            <i class="bi bi-trash3 text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    {{ __('hotel.empty_msg') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-alert-model id="staticBackdrop" title="{{ __('hotel.delete') }}">
        {{ __('hotel.delete_confirm') }} - <span id="hotelName"></span> ?
        <x-slot:action>
            <form id="deleteHotelForm" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('hotel.delete_confirm_btn') }}</button>
            </form>
        </x-slot>
    </x-alert-model>
    @if (session('message'))
        <div id="flashAlert" class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show">

            {{ session('message') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif

@endsection


@push('scripts')
    <script>
        function deleteAlert(hotel) {
            console.log(hotel.id);
            document.getElementById('hotelName').innerHTML = hotel.name;
            document.getElementById('deleteHotelForm').action = `/admin/hotels/${hotel . id}`;
            let modal =
                new bootstrap.Modal(
                    document.getElementById('staticBackdrop')
                )
            modal.show()
        }
    </script>
@endpush
