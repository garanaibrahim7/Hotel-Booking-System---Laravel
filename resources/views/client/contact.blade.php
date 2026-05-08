@extends('client.layouts.template')

@section('content')
    <div class="position-relative vh-40 d-flex align-items-center justify-content-center bg-dark"
        style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1534536281715-e28d76689b4d?q=80&w=2070') center/cover;">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bold text-uppercase" style="letter-spacing: 8px;">Contact Us</h1>
            <p class="small text-uppercase mt-2" style="color: #bca47f; letter-spacing: 3px;">We are here to assist you 24/7
            </p>
        </div>
    </div>

    <div class="container py-5 my-5">
        <div class="row g-5">

            <div class="col-lg-4">
                <h3 class="fw-bold text-uppercase mb-4" style="color: #1a1a1a;">Get In Touch</h3>
                <p class="text-muted mb-5">Have a question or need a special arrangement? Our dedicated team is ready to
                    provide you with the finest hospitality.</p>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-light p-3 border-start border-4" style="border-color: #bca47f !important;">
                        <i class="bi bi-geo-alt fs-4" style="color: #bca47f;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold text-uppercase mb-1">Our Location</h6>
                        <p class="small text-muted mb-0">123 Luxury Avenue, Marine Drive,<br>Mumbai, Maharashtra 400001</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-light p-3 border-start border-4" style="border-color: #bca47f !important;">
                        <i class="bi bi-telephone fs-4" style="color: #bca47f;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold text-uppercase mb-1">Reservations</h6>
                        <p class="small text-muted mb-0">+91 98765 43210<br>+91 22 2345 6789</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-light p-3 border-start border-4" style="border-color: #bca47f !important;">
                        <i class="bi bi-envelope fs-4" style="color: #bca47f;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold text-uppercase mb-1">Support Email</h6>
                        <p class="small text-muted mb-0">stay@classicluxury.com<br>events@classicluxury.com</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h6 class="fw-bold text-uppercase mb-3 small">Follow Our Journey</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-0"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-0"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-0"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-lg p-4 p-md-5" style="border-radius: 0; background: #fafafa;">
                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="small fw-bold text-uppercase text-muted mb-2">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-uppercase text-muted mb-2">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <label class="small fw-bold text-uppercase text-muted mb-2">Subject</label>
                                <select name="subject" class="form-control">
                                    <option>General Inquiry</option>
                                    <option>Room Reservation</option>
                                    <option>Event & Banquet</option>
                                    <option>Feedback</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="small fw-bold text-uppercase text-muted mb-2">Message</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-brand w-100 py-3 fw-bold text-uppercase"
                                    style="letter-spacing: 2px;">
                                    Send Message <i class="bi bi-send ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-0 mb-n5">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3773.809186714221!2d72.8234!3d18.9444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTjCsDU2JzQwLjAiTiA3MsKwNDknMjQuMiJF!5e0!3m2!1sen!2sin!4v1634567890123"
            width="100%" height="450" style="border:0; filter: grayscale(100%) invert(90%);" allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>

    <style>
        .vh-40 {
            height: 40vh;
        }

        .form-control {
            border-radius: 0 !important;
            border: 1px solid #ddd;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #bca47f;
            box-shadow: none;
        }

        .btn-brand {
            background-color: #bca47f;
            color: white;
            border: none;
            border-radius: 0;
            transition: 0.3s;
        }

        .btn-brand:hover {
            background-color: #1a1a1a;
            color: #bca47f;
        }

        /* Google Map Grayscale look for luxury feel */
        iframe {
            display: block;
        }
    </style>
@endsection
