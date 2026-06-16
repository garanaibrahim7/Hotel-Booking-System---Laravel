@extends('layouts.adminlte')

@section('title', 'Add New User')

@section('content')
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-dark small fw-bold">
                <i class="bi bi-arrow-left me-1"></i> BACK TO DIRECTORY
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold text-uppercase small" style="letter-spacing: 1px;">Register New Account</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Full Name</label>
                                    <input type="text" name="name"
                                        class="form-control rounded-0 @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="e.g. John Doe" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Email Address</label>
                                    <input type="email" name="email"
                                        class="form-control rounded-0 @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="john@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                                    <input type="text" name="phone"
                                        class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" placeholder="+1 234 567 890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Assign Role</label>
                                    <select name="role" class="form-select rounded-0 @error('role') is-invalid @enderror"
                                        required>
                                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer
                                        </option>
                                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager
                                        </option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4 text-muted">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Password</label>
                                    <input type="password" name="password"
                                        class="form-control rounded-0 @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-0"
                                        required>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-dark rounded-0 px-5 text-uppercase fw-bold py-2">
                                    Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
