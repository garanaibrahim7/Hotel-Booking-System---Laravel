@extends('layouts.adminlte')

@section('title', 'Room Categories')

@section('content')
    <div class="container-fluid">
        <div class="card my-4 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
                @if (!$categories->isEmpty())
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="w-25">
                            <h3 class="fw-bold mb-0" style="color: #333;">Room Categories</h3>
                            <small class="text-muted">
                                {{ request()->routeIs('admin.categories.show') ? 'Managing categories for ' . $categories->first()->hotel->name : 'Overview of all hotel room types' }}
                            </small>
                        </div>
                        <div class="d-flex flex-column gap-2 w-100">
                            <div class="d-flex gap-2 justify-content-end">
                                @if (request()->routeIs('admin.categories.show'))
                                    {{-- @foreach ($hotels as $id => $hotel)
                                    <a href="{{ route('admin.hotels.index', request('hotel') != $id ? ['hotel' => $id] : []) }}"
                                        class="btn btn-{{ request('hotel') == $id ? '' : 'outline-' }}dark">{{ \Str::title($hotel) }}</a>
                                @endforeach --}}
                                @endif
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <form method="get" class="w-25">
                                    <input type="text" name="search" class="form-control" placeholder="Search Category"
                                        value="{{ request('search') }}" onchange="this.form.submit()">
                                </form>
                                <a class="btn btn-dark shadow-sm border px-3"
                                    href="{{ isset($id) ? route('admin.categories.create', ['hotel' => $id]) : route('admin.categories.create') }}">
                                    <i class="bi bi-plus"></i> Add New Category
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
                                <th class="ps-4 border-0 text-uppercase small fw-bold text-muted">Category Info</th>
                                <th class="border-0 text-uppercase small fw-bold text-muted">Hotel & Location</th>
                                <th class="border-0 text-uppercase small fw-bold text-muted">Capacity</th>
                                <th class="border-0 text-uppercase small fw-bold text-muted text-center">Price</th>
                                <th class="border-0 text-uppercase small fw-bold text-muted">Inventory</th>
                                {{-- <th class="border-0 text-uppercase small fw-bold text-muted text-center">Status</th> --}}
                                <th class="border-0 text-uppercase small fw-bold text-muted text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 position-relative">
                                                @if ($category->images->isNotEmpty())
                                                    <img src="/storage/{{ $category->images->first()->path }}"
                                                        class="rounded-3 shadow-sm"
                                                        style="width: 80px; height: 60px; object-fit: cover;">
                                                    @if ($category->images->count() > 1)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-light"
                                                            style="font-size: 0.6rem;">
                                                            +{{ $category->images->count() - 1 }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                                                        style="width: 80px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $category->title }}</div>
                                                <div class="text-muted small" title="{{ $category->description }}">
                                                    {{ \Str::limit($category->description, 35) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-dark small fw-semibold">{{ $category->hotel->name }}</div>
                                        <div class="text-muted extra-small"><i
                                                class="bi bi-geo-alt-fill me-1"></i>{{ $category->hotel->city->name }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="small d-flex align-items-center gap-3">
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-person-fill text-muted me-1 fs-5"></i>
                                                <span class="fw-semibold text-dark">{{ $category->max_adults }}</span>
                                            </span>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-person-arms-up text-muted me-1"
                                                    style="font-size: 0.85rem;"></i>
                                                <span class="fw-semibold text-dark">{{ $category->max_children }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ $category->local_price }}</span>
                                        <div class="extra-small text-muted">per night</div>
                                    </td>

                                    <td>
                                        <div class="small fw-semibold">{{ $category->qty }} Units</div>
                                        <div class="progress mt-1" style="height: 4px; width: 60px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%">
                                            </div>
                                        </div>
                                    </td>

                                    {{-- <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-success text-success px-3"
                                            style="background-color: #e8fadf;">
                                            Trending
                                        </span>
                                    </td> --}}

                                    <td class="pe-4 text-end">
                                        <div class="btn-group shadow-sm" role="group"
                                            style="border-radius: 8px; overflow: hidden;">
                                            <a href="{{ route('admin.rooms.index', ['room_detail_id' => $category->id]) }}"
                                                class="btn btn-white btn-sm border-end" title="View Rooms">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('admin.rooms.add-block', $category->id) }}"
                                                class="btn btn-white btn-sm border-end" title="Add Block">
                                                <i class="bi bi-ban text-danger"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                class="btn btn-white btn-sm border-end" title="Edit Category">
                                                <i class="bi bi-pen text-warning"></i>
                                            </a>
                                            <button class="btn btn-white btn-sm" onclick="deleteAlert({{ $category }})"
                                                title="Delete">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3 border-0" style="border-radius: 0 0 15px 15px;">
                {{ $categories->links('pagination::bootstrap-5') }}
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-bed fa-4x text-light"></i>
                    </div>
                    <h4 class="text-muted">No Room Categories Defined</h4>
                    <p class="text-secondary mb-4">Start by adding your first room type (e.g., Deluxe, Suite).</p>
                    <a class="btn btn-dark px-4" href="{{ route('admin.categories.create') }}">Add First Category</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Custom CSS for "Luxury" feel --}}
    <style>
        .extra-small {
            font-size: 0.75rem;
        }

        .bg-soft-success {
            background-color: #d1f2eb;
            color: #1abc9c;
        }

        .btn-white {
            background: #fff;
            color: #333;
            border: 1px solid #eee;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }

        .table thead th {
            letter-spacing: 0.05em;
        }
    </style>

    <x-alert-model id="staticBackdrop" title="Delete Category">
        <div class="text-center p-3">
            <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
            <p class="mb-0">Are you sure you want to delete <span id="roomCategoryName" class="fw-bold"></span>?</p>
            <p class="small text-muted">This action will permanently remove all specific rooms associated with this
                category.</p>
        </div>
        <x-slot:action>
            <form id="deleteRoomForm" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger px-4">Yes, Delete Everything</button>
            </form>
        </x-slot>
    </x-alert-model>

@endsection

@push('scripts')
    <script>
        function deleteAlert(category) {
            // Updated to match the object property "title" instead of "category"
            document.getElementById('roomCategoryName').innerHTML = category.type + ' - ' + category.category + ' Room';
            document.getElementById('deleteRoomForm').action = `/admin/categories/${category.id}`;
            let modal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
            modal.show()
        }

        // Auto-hide alerts
        setTimeout(() => {
            let alert = document.getElementById('flashAlert');
            if (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    </script>
@endpush
