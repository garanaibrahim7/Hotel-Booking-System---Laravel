@extends('layouts.adminlte')

@section('title', 'Discounts Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm w-100" style="border-radius: 15px;">
            <div class="bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center w-100">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Discount Coupons</h4>
                    <p class="text-muted small mb-0">Manage promotional codes and seasonal offers</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="get">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Search Coupens" onchange="this.submit()">
                    </form>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a class="btn btn-outline-dark px-3 rounded-pill"
                        href="{{ route('admin.discounts.create', ['seasonal' => 1]) }}">
                        <i class="bi bi-calendar-range me-1"></i> Seasonal
                    </a>
                    <a class="btn btn-dark px-3 rounded-pill shadow-sm" href="{{ route('admin.discounts.create') }}">
                        <i class="bi bi-plus-lg me-1"></i> Create New
                    </a>
                </div>

            </div>

            <div class="card-body p-0 mt-3">
                @if ($discounts->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 text-uppercase small fw-bold text-muted"
                                        style="font-size: 0.7rem;">Code</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">
                                        Type & Value</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted text-center"
                                        style="font-size: 0.7rem;">Usage</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted text-center"
                                        style="font-size: 0.7rem;">Status</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted text-end pe-4"
                                        style="font-size: 0.7rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($discounts as $discount)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-soft-primary rounded-3 d-flex align-items-center justify-content-center me-3"
                                                    style="width: 40px; height: 40px; background-color: #e7f1ff; color: #0d6efd;">
                                                    <i
                                                        class="bi bi-{{ $discount->required_code ? 'ticket-perforated' : 'calendar-range' }} fs-5"></i>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark letter-spacing-1 text-uppercase">{{ $discount->coupen_code }}</span>
                                            </div>
                                        </td>


                                        <td>
                                            <div class="fw-semibold text-dark">{{ $discount->formatted_value }}</div>
                                            <div class="extra-small text-muted text-uppercase" style="font-size: 0.65rem;">
                                                {{ $discount->type }} Discount
                                            </div>
                                        </td>


                                        <td class="text-center">
                                            <div class="fw-bold text-dark">{{ $discount->used_count }}</div>
                                            <div class="extra-small text-muted" style="font-size: 0.65rem;">Times Used</div>
                                        </td>


                                        <td class="text-center">
                                            @php
                                                $isActive = $discount->active_status == 1;
                                                $bg = $isActive ? '#e8fadf' : '#f0f2f5';
                                                $color = $isActive ? '#28a745' : '#6c757d';
                                            @endphp
                                            <span class="badge rounded-pill px-3 py-2"
                                                style="background-color: {{ $bg }}; color: {{ $color }}; font-size: 0.7rem;">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                                {{ $isActive ? 'Active' : 'Discontinued' }}
                                            </span>
                                        </td>


                                        <td class="pe-4 text-end">
                                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">

                                                <a href='{{ route('admin.discounts.show', $discount->id) }}'
                                                    class="btn btn-white btn-sm px-3" title="Show Usage">
                                                    <i class="bi bi-eye text-primary"></i>
                                                </a>
                                                <form action="{{ route('admin.discounts.edit') }}" method="POST"
                                                    class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $discount->id }}">
                                                    <button type="submit" class="btn btn-white btn-sm border-end px-3"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square text-warning"></i>
                                                    </button>
                                                </form>

                                                @if ($isActive)
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        onclick="triggerBookingModal(
                                                        '{{ route('admin.discounts.toggle', $discount->id) }}',
                                                        'Discontinue Coupon',
                                                        'Are you sure you want to stop accepting {{ $discount->coupen_code }} ?',
                                                        'PATCH'
                                                    )"
                                                        title="De-activate">
                                                        <i class="bi bi-slash-circle text-danger"></i>
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.discounts.toggle', $discount->id) }}"
                                                        method="POST" class="m-0">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-white btn-sm px-3"
                                                            title="Re-activate">
                                                            <i class="bi bi-check-circle text-success"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-percent display-1 text-light"></i>
                        <h4 class="text-muted mt-3">No active promotions found</h4>
                        <a href="{{ route('admin.discounts.create') }}"
                            class="btn btn-dark mt-2 rounded-pill px-4 small">Create First Coupon</a>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white border-0 py-3">
                {{ $discounts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <style>
        .btn-white {
            background: #fff;
            border: 1px solid #f0f2f5;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }

        .extra-small {
            font-size: 0.75rem;
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
            color: #0d6efd;
        }
    </style>
@endsection
