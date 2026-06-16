@extends('layouts.adminlte')

@section('title', 'Edit User | ' . $user->name)

@section('content')
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('admin.users.show', $user->id) }}" class="text-decoration-none text-dark small fw-bold">
                <i class="bi bi-arrow-left me-1"></i> BACK TO PROFILE
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold text-uppercase small" style="letter-spacing: 1px;">Update Account Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Full Name</label>
                                    <input type="text" name="name"
                                        class="form-control rounded-0 @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Email Address</label>
                                    <input type="email" name="email"
                                        class="form-control rounded-0 @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                                    <input type="text" name="phone"
                                        class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">System Role</label>
                                    <select name="role" class="form-select rounded-0 @error('role') is-invalid @enderror"
                                        required>
                                        <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>
                                            Customer</option>
                                        <option value="manager"
                                            {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            Administrator</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 my-3">
                                    <div class="p-3 bg-light border-start border-4 border-dark">
                                        <p class="mb-0 small text-muted"><i class="bi bi-info-circle me-2"></i>Leave
                                            password fields empty if you do not wish to change the current password.</p>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">New Password</label>
                                    <input type="password" name="password"
                                        class="form-control rounded-0 @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-0">
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-dark rounded-0 px-5 text-uppercase fw-bold py-2">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
