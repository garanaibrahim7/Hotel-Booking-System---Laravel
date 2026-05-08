@extends('layouts.adminlte')

@section('title', 'Block Room | ' . $room->title)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-0">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0 text-uppercase" style="letter-spacing: 1px;">Set Room Block</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Display Context -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted text-uppercase small fw-bold">Room Category</h6>
                            <h4 class="fw-bold">{{ $room->title }}</h4>
                            <p class="text-secondary mb-0">
                                <i class="bi bi-building me-2"></i>{{ $room->hotel->name }}
                            </p>
                        </div>

                        <form action="{{ route('admin.rooms.store-block') }}" method="POST">
                            @csrf
                            <input type="hidden" name="room_detail_id" value="{{ $room->id }}">

                            <div class="row g-3">
                                <!-- From Date -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase">Block From</label>
                                    <input type="date" name="from"
                                        class="form-control rounded-0 @error('from') is-invalid @enderror"
                                        value="{{ old('from', date('Y-m-d')) }}" required>
                                    @error('from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- To Date -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase">Block Until</label>
                                    <input type="date" name="to"
                                        class="form-control rounded-0 @error('to') is-invalid @enderror"
                                        value="{{ old('to') }}" required>
                                    @error('to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Reason -->
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-uppercase">Reason for Blocking</label>
                                    <textarea name="reason" rows="3" class="form-control rounded-0 @error('reason') is-invalid @enderror"
                                        placeholder="e.g. Annual Maintenance, Deep Cleaning, or Private Event">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This reason will be visible to staff on the availability
                                        calendar.</div>
                                </div>
                            </div>

                            <div class="mt-5 d-flex gap-2">
                                <button type="submit" class="btn btn-dark px-5 py-2 rounded-0 text-uppercase fw-bold">
                                    Save Block
                                </button>
                                <a href="{{ url()->previous() }}"
                                    class="btn btn-outline-secondary px-4 py-2 rounded-0 text-uppercase fw-bold">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
