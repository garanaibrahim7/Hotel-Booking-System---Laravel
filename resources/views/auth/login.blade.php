@extends('layouts.adminlte')

@section('title', 'Login')

@section('content')

    <div class="d-flex flex-column justify-content-center align-items-center mt-4">
        <div class="login-box">
            <div class="login-logo">
                <a href="../index2.html"><b>Hotel Booking System</b></a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="Email"
                                    value="{{ old('email') }}" />
                                <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                            </div>
                            @error('email')
                                <label class="text-danger" for="email">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" placeholder="Password" />
                                <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                            </div>
                            @error('password')
                                <label class="text-danger" for="password">{{ $message }}</label>
                            @enderror
                        </div>
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="remember"
                                        id="flexCheckDefault" {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="flexCheckDefault"> Remember Me </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!--end::Row-->
                    </form>
                    <div class="social-auth-links text-center mb-3 d-grid gap-2">
                        <p>- OR -</p>
                        <a href="#" class="btn btn-primary">
                            <i class="bi bi-facebook me-2"></i> Sign in using Facebook
                        </a>
                        <a href="#" class="btn btn-danger">
                            <i class="bi bi-google me-2"></i> Sign in using Google+
                        </a>
                    </div>
                    <!-- /.social-auth-links -->
                    {{-- <p class="mb-1"><a href="{{ route('password.request') }}">I forgot my password</a></p> --}}
                    <p class="mb-0">
                        <a href="register.html" class="text-center"> Register a new membership </a>
                    </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
    </div>
@endsection
