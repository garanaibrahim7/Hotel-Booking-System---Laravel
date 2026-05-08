@extends('layouts.adminlte')

@section('title', 'All Transactions')

@section('content')

    <div class="container-fluid pt-4">

        @if ($verified)
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-end">
                    <select name="currency" id="" class="form-select w-25"
                        onchange="window.location.href='{{ route('admin.transactions', ':currency') }}'.replace(':currency', this.value)">
                        <option value="">Select Currency for Transactions</option>
                        @foreach ($currencies as $countryName => $currency_code)
                            <option value="{{ $currency_code }}" {{ $currency === $currency_code ? 'selected' : '' }}>
                                {{ $countryName }} ({{ $currency_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="d-flex align-items-center p-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                                <i class="bi bi-graph-down-arrow fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Total Credits</small>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format($totalCredit, 2) }} <span
                                        class="small fw-normal text-muted">{{ strtoupper($currency) }}</span></h3>
                            </div>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="d-flex align-items-center p-3">
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle me-3">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Total Debits</small>
                                <h3 class="mb-0 fw-bold text-danger">{{ number_format($totalDebit, 2) }} <span
                                        class="small fw-normal text-muted">{{ strtoupper($currency) }}</span></h3>
                            </div>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">

                @if (!$verified)
                    <div class="alert alert-danger shadow-lg border-start border-5 border-danger p-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-1 me-3"></i>
                            <div>
                                {{ $verified }}
                                <h4 class="alert-heading fw-bold">Security Breach Detected!</h4>
                                <p class="mb-0">The transaction ledger hash chain has been broken. Access to transaction
                                    data is suspended to prevent the display of tampered financial records.</p>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0 small italic">Please contact the system administrator to audit the database integrity
                            immediately.</p>
                    </div>
                @else
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title font-weight-bold">Transactions</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-uppercase small text-muted">
                                            <th class="ps-4">Transaction</th>
                                            <th>Reference</th>
                                            <th>Financials</th>
                                            <th>Tax</th>
                                            <th class="pe-4">Mode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $transaction)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle me-2"
                                                            style="width: 10px; height: 10px; background-color: {{ $transaction->type === 'credit' ? '#28a745' : '#dc3545' }};">
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $transaction->note }}</div>
                                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                                {{ $transaction->created_at->format('M d, Y • g:i A') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-primary border mb-1">
                                                        @php
                                                            $url = strtolower(
                                                                class_basename($transaction->transactionable_type),
                                                            );
                                                            if ($url === 'refund') {
                                                                $url = route(
                                                                    'admin.refund.show',
                                                                    $transaction->transactionable_id,
                                                                );
                                                            } else {
                                                                $url = route(
                                                                    'admin.bookings.show',
                                                                    $transaction->transactionable_id,
                                                                );
                                                            }
                                                        @endphp
                                                        <a href="{{ $url }}" class="text-decoration-none">
                                                            {{ class_basename($transaction->transactionable_type) }}
                                                            #{{ $transaction->transactionable_id }}
                                                        </a>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">
                                                        {{ number_format($transaction->amount, 2) }} <span
                                                            class="small text-muted">{{ strtoupper($currency) }}</span>
                                                    </div>
                                                    @if ((float) $transaction->exchange_rate > 0 && $transaction->currency !== $transaction->converted_currency)
                                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                            {{ number_format($transaction->converted_amount, 2) }}
                                                            {{ strtoupper($transaction->converted_currency) }}
                                                            <span class="ms-1">| Rate:
                                                                {{ (float) $transaction->exchange_rate }}</span>
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ((float) $transaction->tax_amount > 0)
                                                        <div class="small fw-bold">
                                                            {{ number_format($transaction->tax_amount, 2) }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">
                                                            {{ (float) $transaction->tax }}% Tax</div>
                                                    @else
                                                        <span class="text-muted small">No Tax</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4">
                                                    <span class="badge border text-dark bg-white text-capitalize">
                                                        {{ $transaction->mode }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">No transactions
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($transactions->hasPages())
                            <div class="card-footer bg-white border-top">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
