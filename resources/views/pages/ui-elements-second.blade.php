@extends('layouts.adminlte')

@section('title', 'UI Elements')

@section('content')

    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Icons</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Icons
                                </li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!-- Timelime example  -->
                    <div class="row">
                        <div class="col-12">
                            <!-- The icons -->
                            <div class="col-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Icons</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>You can use any font library you like with AdminLTE 4.</p>
                                        <strong>Recommendations</strong>
                                        <ul class="mt-1">
                                            <li><a href="https://fontawesome.com/" target="_blank">Font Awesome</a></li>
                                            <li><a href="https://useiconic.com/open/" target="_blank">Iconic Icons</a></li>
                                            <li><a href="https://ionicons.com/" target="_blank">Ion Icons</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
    </div>
    <!--end::App Wrapper-->

@endsection
