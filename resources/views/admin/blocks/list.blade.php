@extends('layouts.adminlte')

@section('title', 'Manage Room Blocks')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="headingfonts fw-bold text-uppercase mb-1">Room Availability Blocks</h2>
                <p class="text-muted small">Overview of all manually blocked room dates</p>
            </div>
            {{-- Link to the create view we made earlier --}}
            <a href="{{ route('admin.rooms.add-block', 1) }}"
                class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fw-bold small">
                <i class="bi bi-plus-lg me-2"></i>New Block
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold" style="letter-spacing: 1px;">Hotel & Room
                                </th>
                                <th class="py-3 text-uppercase small fw-bold" style="letter-spacing: 1px;">Duration</th>
                                <th class="py-3 text-uppercase small fw-bold" style="letter-spacing: 1px;">Status</th>
                                <th class="py-3 text-uppercase small fw-bold" style="letter-spacing: 1px;">Reason</th>
                                <th class="pe-4 py-3 text-end text-uppercase small fw-bold" style="letter-spacing: 1px;">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blocks as $block)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $block->details->title }}</div>
                                        <div class="text-muted small">{{ $block->details?->hotel->name }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">
                                            {{ \Carbon\Carbon::parse($block->from)->format('d M, Y') }}
                                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($block->to)->format('d M, Y') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($block->from)->diffInDays($block->to) + 1 }} Days
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $today = now()->startOfDay();
                                            $from = \Carbon\Carbon::parse($block->from)->startOfDay();
                                            $to = \Carbon\Carbon::parse($block->to)->startOfDay();
                                        @endphp

                                        @if ($today->between($from, $to))
                                            <span class="badge bg-danger px-3 py-2 rounded-0 text-uppercase"
                                                style="font-size: 0.65rem;">Active Now</span>
                                        @elseif($today->lt($from))
                                            <span class="badge bg-primary px-3 py-2 rounded-0 text-uppercase"
                                                style="font-size: 0.65rem;">Upcoming</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 rounded-0 text-uppercase"
                                                style="font-size: 0.65rem;">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="text-secondary small">{{ Str::limit($block->reason ?? 'No reason provided', 40) }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-white btn-sm" onclick="deleteAlert({{ $block }})"
                                            title="Remove Block">
                                            {{-- <i class="bi bi-trash3 fs-5"></i> --}}
                                            <i class="bi bi-trash3 text-danger fs-5"></i>
                                        </button>
                                        {{-- <form action="{{ route('admin.rooms.remove-block', $block->id) }}" method="POST"
                                            onsubmit="return confirm('Remove this block and make dates available?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                DElete
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-calendar-x display-4 text-muted d-block mb-3"></i>
                                        <p class="text-muted">No room blocks found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($blocks->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $blocks->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-alert-model id="staticBackdrop" title="Sure">
        Are you Sure to Remove this Block ?
        <x-slot:action>
            <form id="deleteBlockForm" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Remove</button>
            </form>
        </x-slot>
    </x-alert-model>
@endsection

@push('scripts')
    <script>
        function deleteAlert(block) {
            console.log(block.id);
            document.getElementById('deleteBlockForm').action = `/admin/room/${block . id}/remove-block`;
            let modal =
                new bootstrap.Modal(
                    document.getElementById('staticBackdrop')
                )
            modal.show()
        }
    </script>
@endpush
