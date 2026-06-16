@extends('client.layouts.template')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light" style="background: #f8f5f0;">
    <div class="card border-0 shadow-lg w-100" style="max-width: 500px; border-radius: 0;">
        <div class="card-body p-5">
            
            <div class="text-center mb-4">
                <div class="d-inline-block p-3 border border-2 mb-3" style="border-color: #bca47f !important;">
                    <i class="bi bi-key fs-1" style="color: #bca47f;"></i>
                </div>
                <h3 class="fw-bold text-uppercase mb-2" style="letter-spacing: 2px; color: #1a1a1a;">Set New Password</h3>
                <div style="width: 40px; height: 3px; background: #bca47f;" class="mx-auto"></div>
            </div>

            @if (session('success'))
                <div class="text-center py-4">
                    <div class="alert alert-success border-0 rounded-0 mb-4" style="background-color: #e8f5e9; color: #2e7d32;">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-brand w-100 py-3 fw-bold text-uppercase">Go to Login</a>
                </div>
            @elseif(session('fail'))
                <div class="alert alert-danger border-0 rounded-0 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('fail') }}
                </div>
            @else
                <p class="text-center text-muted small text-uppercase mb-4" style="letter-spacing: 1px;">
                    Resetting password for: <br>
                    <span class="fw-bold text-dark">{{ request()->email }}</span>
                </p>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->email }}">

                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">New Password</label>
                        <input type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Min. 8 characters" required>
                        @error('password')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" 
                               class="form-control" 
                               placeholder="Repeat your password" required>
                        @error('password_confirmation')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-brand btn-lg w-100 py-3 fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">
                        Update Password
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>

<style>
    /* Sharp Edges */
    .form-control, .btn, .alert {
        border-radius: 0 !important;
    }

    /* Luxury Theme Colors */
    .btn-brand {
        background-color: #bca47f;
        color: white;
        border: none;
        transition: 0.3s;
    }

    .btn-brand:hover {
        background-color: #1a1a1a;
        color: #bca47f;
    }

    /* Form Styling */
    .form-control {
        border: 1px solid #e0e0e0;
        padding: 12px;
        background-color: #fff;
    }

    .form-control:focus {
        border-color: #bca47f;
        box-shadow: none;
    }

    .invalid-feedback {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: bold;
    }
</style>
@endsection