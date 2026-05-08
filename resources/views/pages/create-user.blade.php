@extends('layouts.adminlte')

@section('title', 'UI Elements')

@section('content')

    <div class="card card-info card-outline mb-4 w-50 m-auto mt-5">
        <!--begin::Header-->
        <div class="card-header">
            <div class="card-title">Add User</div>
        </div>
        <!--end::Header-->
        <!--begin::Form-->
        <form action="{{ route('create-user') }}" method="POST" class="needs-validation">
            @csrf
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Row-->
                <div class="row g-3">
                    <!--begin::Col-->
                    <div class="col-md-12">
                        <label for="validationCustom01" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="validationCustom01" name="name" value="" />
                        @error('name')
                            <label for="name" class="text-danger">
                                {{ $message }}
                            </label>
                        @enderror
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-md-12">
                        <label for="validationCustomUsername" class="form-label">Email</label>
                        <div class="input-group has-validation">
                            <input type="text" name="email" class="form-control" id="validationCustomUsername" />
                            {{-- aria-describedby="inputGroupPrepend" /> --}}
                            {{-- <span class="input-group-text" id="inputGroupPrepend">@</span> --}}
                        </div>
                        @error('email')
                            <label for="name" class="text-danger">
                                {{ $message }}
                            </label>
                        @enderror
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-md-12">
                        <label for="validationCustom03" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="validationCustom03" />
                        @error('password')
                            <label for="name" class="text-danger">
                                {{ $message }}
                            </label>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Body-->
            <!--begin::Footer-->
            <div class="card-footer">
                <button class="btn btn-info" type="submit">Submit form</button>
            </div>
            <!--end::Footer-->
        </form>
        <!--end::Form-->
    </div>

@endsection
