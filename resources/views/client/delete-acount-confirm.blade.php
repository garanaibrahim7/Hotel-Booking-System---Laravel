@extends('client.layouts.template')

@section('title', 'Delete Account')

@section('content')
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg" style="border-radius: 0; border-top: 5px solid #dc3545;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-octagon text-danger" style="font-size: 4rem;"></i>
                        <h2 class="fw-bold text-uppercase mt-3" style="letter-spacing: 2px;">Delete Account</h2>
                        <p class="text-muted small">GDPR COMPLIANCE & DATA PRIVACY</p>
                    </div>

                    <div class="alert alert-danger border-0 rounded-0 p-4 mb-4">
                        <h6 class="fw-bold text-uppercase"><i class="bi bi-info-circle me-2"></i> Warning: This is Permanent</h6>
                        <p class="small mb-0">By deleting your account, you will lose access to all your booking history, saved preferences, and personal information. As per GDPR, we will purge all your data from our active servers.</p>
                    </div>

                    <form action="{{ route('client.delete-account') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4">
                            <label class="small fw-bold text-uppercase text-muted mb-2">Confirm Your Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password to confirm" required>
                            @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="confirm_deletion" id="confirmCheck" required>
                            <label class="form-check-label small text-muted" for="confirmCheck">
                                I understand that my data will be permanently deleted and cannot be recovered.
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger py-3 fw-bold text-uppercase" style="border-radius: 0; letter-spacing: 2px;">
                                Permanently Delete My Account
                            </button>
                            <a href="{{  route('client.profile') }}" class="btn btn-light py-3 border rounded-0 fw-bold text-uppercase">
                                Cancel & Go Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control { border-radius: 0; padding: 12px; border: 1px solid #ddd; }
    .form-control:focus { border-color: #dc3545; box-shadow: none; }
</style>
@endsection