@extends('client.layouts.template')

@section('title', 'Forgot Password')

@section('content')

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light" style="background: #f8f5f0;">
    <div class="card border-0 shadow-lg w-100" style="max-width: 500px; border-radius: 0;">
        <div class="card-body p-5">
            
            <div class="text-center mb-4">
                <div class="d-inline-block p-3 border border-2 mb-3" style="border-color: #bca47f !important;">
                    <i class="bi bi-shield-lock fs-1" style="color: #bca47f;"></i>
                </div>
                <h3 class="fw-bold text-uppercase mb-2" style="letter-spacing: 2px; color: #1a1a1a;">Reset Password</h3>
                <div style="width: 40px; height: 3px; background: #bca47f;" class="mx-auto"></div>
            </div>

            @if (session('status'))
                {{-- Laravel default status message --}}
                <div class="alert alert-success border-0 rounded-0 mb-4" style="background-color: #e8f5e9; color: #2e7d32;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                </div>
            @else
                <p class="text-center text-muted small text-uppercase mb-4" style="letter-spacing: 1px;">
                    Enter your email address and we will send you a link to reset your password.
                </p>

                <form action="{{ route('password.reset') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Email Address</label>
                        <div class="input-group">
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="e.g. name@example.com"
                                   value="{{ old('email') }}" required autofocus>
                            <span class="input-group-text bg-white border-start-0 text-muted">
                                <i class="bi bi-envelope"></i>
                            </span>
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-brand btn-lg w-100 py-3 fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">
                        Send Reset Link
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none small fw-bold text-uppercase" style="color: #1a1a1a;">
                            <i class="bi bi-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    /* Global Sharp Look */
    .form-control, .btn, .input-group-text, .alert {
        border-radius: 0 !important;
    }

    /* Brand Color */
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

    /* Input Styling */
    .form-control {
        border: 1px solid #e0e0e0;
        padding: 12px;
    }

    .form-control:focus {
        border-color: #bca47f;
        box-shadow: none;
    }

    .input-group-text {
        border: 1px solid #e0e0e0;
    }
</style>
@endsection
