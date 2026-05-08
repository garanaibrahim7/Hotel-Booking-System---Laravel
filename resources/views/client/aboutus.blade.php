@extends('client.layouts.template')

@section('content')
<div class="position-relative vh-50 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2070') center/cover fixed;">
    <div class="text-center text-white">
        <h1 class="display-3 fw-bold text-uppercase" style="letter-spacing: 10px;">Our Story</h1>
        <div style="width: 80px; height: 3px; background: #bca47f;" class="mx-auto mt-3"></div>
    </div>
</div>

<div class="container py-5 my-5">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <h6 class="text-uppercase fw-bold mb-3" style="color: #bca47f; letter-spacing: 3px;">Since 1998</h6>
            <h2 class="display-5 fw-bold text-uppercase mb-4" style="color: #1a1a1a;">Defining Luxury <br>In Every Detail</h2>
            <p class="lead text-muted mb-4">
                Welcome to a world where elegance meets comfort. Our journey began with a simple vision: to create a sanctuary of luxury that feels like home.
            </p>
            <p class="text-secondary">
                For over two decades, we have been the preferred choice for travelers seeking unparalleled service and timeless sophistication. Every corner of our hotel is crafted to tell a story of heritage and modern excellence.
            </p>
            <div class="mt-4 pt-3 border-top w-75">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e5/Signature_of_John_Hancock.svg" alt="Signature" style="height: 60px; filter: grayscale(1);">
                <p class="small text-uppercase fw-bold mt-2 mb-0">The Founder's Promise</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070" class="img-fluid shadow-lg" style="border-radius: 0;">
                <div class="position-absolute bottom-0 start-0 bg-white p-4 shadow-sm d-none d-md-block" style="transform: translate(-30px, 30px); border-left: 5px solid #bca47f;">
                    <h4 class="fw-bold mb-0">25+ Years</h4>
                    <p class="small text-muted mb-0">Of Excellence in Hospitality</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 py-5">
        <div class="col-md-4">
            <div class="p-5 text-center border h-100 transition-hover shadow-sm">
                <i class="bi bi-gem fs-1 mb-4 d-block" style="color: #bca47f;"></i>
                <h5 class="fw-bold text-uppercase mb-3">Timeless Design</h5>
                <p class="small text-muted">Architecture that blends classical heritage with contemporary luxury aesthetics.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-5 text-center bg-dark text-white h-100 shadow-lg">
                <i class="bi bi-heart fs-1 mb-4 d-block" style="color: #bca47f;"></i>
                <h5 class="fw-bold text-uppercase mb-3">Guest First</h5>
                <p class="small" style="color: #aaa;">Personalized service that anticipates your every need before you even ask.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-5 text-center border h-100 transition-hover shadow-sm">
                <i class="bi bi-award fs-1 mb-4 d-block" style="color: #bca47f;"></i>
                <h5 class="fw-bold text-uppercase mb-3">Gold Standards</h5>
                <p class="small text-muted">A commitment to the highest quality of sanitation, comfort, and culinary arts.</p>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid bg-light py-5 border-top border-bottom">
    <div class="row text-center">
        <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h2 class="fw-bold mb-0">120+</h2>
            <p class="small text-uppercase text-muted fw-semibold">Luxury Rooms</p>
        </div>
        <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h2 class="fw-bold mb-0">12</h2>
            <p class="small text-uppercase text-muted fw-semibold">Gourmet Cuisines</p>
        </div>
        <div class="col-6 col-md-3">
            <h2 class="fw-bold mb-0">50K+</h2>
            <p class="small text-uppercase text-muted fw-semibold">Happy Guests</p>
        </div>
        <div class="col-6 col-md-3">
            <h2 class="fw-bold mb-0">15</h2>
            <p class="small text-uppercase text-muted fw-semibold">Global Awards</p>
        </div>
    </div>
</div>

<style>
    /* Sharp Corners & Animations */
    .img-fluid, .border, .bg-dark, .btn {
        border-radius: 0 !important;
    }

    .vh-50 { height: 50vh; }

    .transition-hover:hover {
        background-color: #fafafa;
        border-color: #bca47f !important;
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    .display-5 { letter-spacing: -1px; line-height: 1.1; }
</style>
@endsection