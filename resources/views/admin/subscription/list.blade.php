@extends('layouts.adminlte')

@section('title', 'Subscription Plans Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm w-100" style="border-radius: 15px;">
            <div class="bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center w-100">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Subscription Plans</h4>
                    <p class="text-muted small mb-0">Manage premium packages and pricing tiers</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="get">
                        <input type="text" name="search" class="form-control bg-light border-0" value="{{ request('search') }}"
                            placeholder="Search Plans" onchange="this.submit()">
                    </form>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a class="btn btn-dark px-3 rounded-pill shadow-sm" href="{{ route('admin.subscription.create') }}">
                        <i class="bi bi-plus-lg me-1"></i> Create New
                    </a>
                </div>
            </div>

            <div class="card-body px-0 bg-white">
                @if(isset($plans) && $plans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase" style="font-size: 0.8rem;">
                                <tr>
                                    <th class="px-4 border-0">ID</th>
                                    <th class="border-0">Plan Name</th>
                                    <th class="border-0">Price</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Stripe ID</th>
                                    <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $plan)
                                    <tr>
                                        <td class="px-4 text-muted">#{{ $plan->id }}</td>
                                        <td class="fw-bold text-dark">{{ $plan->name }}</td>
                                        <td class="fw-semibold">{{ $plan->currency_symbol }}{{ $plan->price }} <small class="text-muted">{{ $plan->currency }}</small></td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 text-capitalize">
                                                {{ $plan->type }}
                                            </span>
                                        </td>
                                        <td class="text-muted small"><code>{{ $plan->stripe_price_id }}</code></td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('admin.subscription.edit', $plan->id) }}" class="btn btn-white btn-sm shadow-sm rounded-circle" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.subscription.destroy', $plan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-white btn-sm shadow-sm rounded-circle text-danger" title="Delete" onclick="return confirm('Delete this subscription plan?')">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-gem display-1 text-light"></i>
                        <h4 class="text-muted mt-3">No active plans found</h4>
                        <a href="{{ route('admin.subscription.create') }}"
                            class="btn btn-dark mt-2 rounded-pill px-4 small">Create First Plan</a>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white border-0 py-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                @if(isset($plans))
                    {{ $plans->links('pagination::bootstrap-5') }}
                @endif
            </div>
        </div>
    </div>

    <style>
        .btn-white { background: #fff; border: 1px solid #f0f2f5; height: 35px; width: 35px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-white:hover { background: #f8f9fa; }
        .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
    </style>
@endsection
