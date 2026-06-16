@extends('client.layouts.template')

@section('content')
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light mt-4"
        style="background: #f8f5f0;">
        <div class="row g-0 shadow-lg w-100" style="max-width: 500px;">

            {{-- <div class="col-md-6 d-none d-md-block position-relative">
            <div class="h-100 w-100" style="background: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80') center/cover;">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(26, 26, 26, 0.6);"></div>
                <div class="position-absolute bottom-0 start-0 p-5 text-white">
                    <h2 class="fw-bold text-uppercase" style="letter-spacing: 3px;">Welcome Back</h2>
                    <p class="small text-uppercase" style="color: #bca47f;">Experience luxury at its finest.</p>
                </div>
            </div>
        </div> --}}

            <div class="col-md-12 bg-white p-5">
                <div class="mb-5 text-center text-md-start">
                    <h3 class="fw-bold text-uppercase mb-2" style="letter-spacing: 1px; color: #1a1a1a;">Login</h3>
                    <div style="width: 50px; height: 3px; background: #bca47f;" class="mx-auto mx-md-0"></div>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color: #a18151;">
                            Forgot Password ?
                        </a>
                        @error('password')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-uppercase fw-semibold" for="remember">Keep me signed
                            in</label>
                    </div>

                    <button type="submit" class="btn btn-brand btn-lg w-100 py-3 fw-bold mb-4">
                        Sign In
                    </button>

                    <div class="text-center">
                        <p class="small text-muted mb-0">Don't have an account?</p>
                        <a href="{{ route('register') }}"
                            class="fw-bold text-decoration-none text-uppercase text-dark small">Create Account</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control,
        .btn,
        .shadow-lg {
            border-radius: 0 !important;
        }

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

        .form-control {
            border: 1px solid #e0e0e0;
            padding: 12px;
            background-color: #fff;
        }

        .form-control:focus {
            border-color: #bca47f;
            box-shadow: none;
        }

        .form-check-input:checked {
            background-color: #bca47f;
            border-color: #bca47f;
        }   
    </style>
@endpush
