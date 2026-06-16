@extends('client.layouts.template')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light" style="background: #f8f5f0;">
    <div class="row g-0 shadow-lg w-100" style="max-width: 500px;">
        
        <div class="col-md-12 bg-white p-5 order-2 order-md-1">
            <div class="mb-5 text-center text-md-start">
                <h3 class="fw-bold text-uppercase mb-2" style="letter-spacing: 1px; color: #1a1a1a;">Join Us</h3>
                <p class="small text-muted text-uppercase">Create an account for exclusive booking benefits.</p>
                <div style="width: 50px; height: 3px; background: #bca47f;" class="mx-auto mx-md-0"></div>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="small fw-bold text-uppercase text-muted mb-2">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" utofocus>
                    @error('name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-uppercase text-muted mb-2">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-uppercase text-muted mb-2">Phone Number</label>
                    <input type="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-brand btn-lg w-100 py-3 fw-bold mb-4 mt-2">
                    Register Now
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Already a member?</p>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none text-uppercase small" style="color: #1a1a1a; border-bottom: 2px solid #bca47f;">Login to Account</a>
                </div>
            </form>
        </div>

        
    </div>
</div>

<style>
    /* Sharp Edges Everywhere */
    .form-control, .btn, .shadow-lg, .form-check-input {
        border-radius: 0 !important;
    }

    /* Brand Button */
    .btn-brand {
        background-color: #bca47f;
        color: white;
        border: none;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: 0.3s;
    }

    .btn-brand:hover {
        background-color: #a8926d;
        color: white;
    }

    /* Input Focus State */
    .form-control {
        border: 1px solid #e0e0e0;
        padding: 12px;
        background-color: #fafafa;
    }

    .form-control:focus {
        border-color: #bca47f;
        background-color: #fff;
        box-shadow: none;
    }
</style>
@endsection