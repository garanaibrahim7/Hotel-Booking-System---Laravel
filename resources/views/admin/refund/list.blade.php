@extends('layouts.adminlte')
@section('title', 'Refund Requests')

@php
    $statusLabels = [
        0 => 'Pending',
        1 => 'Completed',
        2 => 'Failed',
        3 => 'Processing',
        4 => 'Rejected',
    ];
@endphp

@section('content')
    <div class="container-fluid py-4">
        <h2 class="mb-4">Refund Management</h2>

        {{-- Sort by key to ensure 0 (Pending) comes first --}}
        @forelse ($refunds->sortKeys() as $status => $group)
            <div class="card mb-4 shadow-sm {{ $status == 0 ? 'border-primary' : '' }}">
                <div
                    class="card-header {{ $status == 0 ? 'bg-primary text-white' : 'bg-light' }} d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold">
                        {{ $statusLabels[$status] ?? 'Unknown' }}
                        <span class="badge {{ $status == 0 ? 'bg-white text-primary' : 'bg-secondary' }} ms-2">
                            {{ count($group) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Hotel / Room</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                    <th>Date Requested</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group as $refund)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $refund->user?->name ?? 'Guest' }}</div>
                                            <small class="text-muted text-uppercase">Booking
                                                #{{ $refund->booking_id }}</small>
                                        </td>
                                        <td>
                                            {{-- Assuming booking has hotel relationship --}}
                                            <div>{{ $refund->booking?->hotel?->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $refund->booking?->room_type ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">
                                                {{ number_format($refund->amount, 2) }} {{ $refund->currency }}
                                            </span>
                                        </td>
                                        <td>
                                            <span title="{{ $refund->reason }}">
                                                {{ Str::limit($refund->reason, 30) }}
                                            </span>
                                        </td>
                                        <td>{{ $refund->created_at->format('d M Y, H:i') }}</td>
                                        <td class="text-end px-4">
                                            @if ($refund->status == 0)
                                                <button class="btn btn-sm btn-success"
                                                    onclick="confirmAction('{{ route('admin.refund.process', $refund->id) }}',
                                                        'Process Refund',
                                                        'Are you sure you want to process this refund of {{ number_format($refund->amount, 2) }} {{ $refund->currency }}?',
                                                        'btn-success')">
                                                    <i class="fas fa-check"></i> Process
                                                </button>

                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirmAction('{{ route('admin.refund.reject', $refund->id) }}',
                                                        'Reject Refund',
                                                        'Are you sure you want to Reject this refund of {{ number_format($refund->amount, 2) }} {{ $refund->currency }}?',
                                                        'btn-danger')">
                                                    <i class="fas fa-check"></i> Reject
                                                </button>

                                            @else
                                                <span class="badge rounded-pill bg-gray text-dark border px-3">
                                                    {{ $statusLabels[$refund->status] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No refund requests found in the system.</p>
            </div>
        @endforelse
    </div>
@endsection
