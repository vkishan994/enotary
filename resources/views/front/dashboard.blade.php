@extends('front.layouts.common')
@section('content')

    <!-- dashboard header start -->
    <section class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="logo-sec">
                        <a href="#"><img src="img/logo/logo.png" alt="" /> </a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-end">
                        <div class="profile dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <span> John Doe</span>
                                <img src="img/home/down-icon.png" alt="Profile" class="rounded-circle mx-2">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <div class="dropdown-header">
                                        <h6 class="mb-0">John Doe</h6>
                                        <small class="text-muted">john.doe@example.com</small>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-person me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="menu-toogle">
                            <img src="img/home/menu-toogle.svg" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- dashboard header end -->

    <!-- asidebar start -->
    <aside class="sidebar">
         <a href="#" class="close-sidebar"><i class="fa fa-close"></i>  </a>
        <div class="logo-sec mb-4">
            <a href="#"><img src="img/logo/white-horse.png" alt="" /> </a>
        </div>
        <div class="navbar-nav">
            <a class="nav-link" href="#">
                Dashboard
            </a>
            <a class="nav-link" href="#">
                New Notarisation
            </a>
            <a class="nav-link" href="#">
                My Notarisations
            </a>
            <a class="nav-link" href="#">
                Billing
            </a>
            <a class="nav-link" href="#">
                Help & Support
            </a>
        </div>
    </aside>
    <!-- asidebar start -->

    <!-- Main content start -->
    <main class="main-content">
        <div class="section-title">
            <h2>Hello Asik</h2>
        </div>
        <div class="document-upcoming">
            <div class="row">
                <div class="col-lg-6">
                    <div class="document-card">
                        <h4>No upcoming appointments</h4>
                        <p>There are currently no sessions booked. </p>
                        <p>Please schedule a new one if needed.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="document-card">
                        <h4>Notarise a new document</h4>
                        <p>Notarise your document online and<br> schedule a video appointment.</p>
                        <a href="#" class="btn btn-primary w-100 py-2 mt-2">Notarise a new document +</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="document-pending">
            <div class="section-title">
                <h4>Pending Documents for Notarisation</h4>
            </div>

            <ul class="nav nav-tabs" id="notarisationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="passport-tab" data-bs-toggle="tab" data-bs-target="#passport"
                        type="button" role="tab" aria-controls="passport" aria-selected="true">
                        Passport Notarisation
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="document-tab" data-bs-toggle="tab" data-bs-target="#document"
                        type="button" role="tab" aria-controls="document" aria-selected="false">
                        Document Notarisation
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="notarisationTabContent">
                <!-- Passport Notarisation Tab -->
                <div class="tab-pane fade show active" id="passport" role="tabpanel" aria-labelledby="passport-tab">
                    <div class="pending-list mb-4">
                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon4.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Verify your identity</h5>
                                    <p>Confirm your identity in just a few minutes to continue with your notarisation.
                                    </p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon5.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Upload your document</h5>
                                    <p>Upload your document to begin the notarisation process.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon6.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Schedule a video call meeting</h5>
                                    <p>Schedule your video appointment to complete your notarisation.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"><i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon7.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Download your notarised documents</h5>
                                    <p>Download your officially notarised documents here.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Notarisation Tab -->
                <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                    <div class="pending-list mb-4">
                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon4.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Verify your identity</h5>
                                    <p>Confirm your identity in just a few minutes to continue with your notarisation.
                                    </p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon5.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Upload your document</h5>
                                    <p>Upload your document to begin the notarisation process.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon6.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Schedule a video call meeting</h5>
                                    <p>Schedule your video appointment to complete your notarisation.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"><i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-icon">
                                    <img src="img/home/icon7.png" alt="" />
                                </div>
                                <div class="pending-text">
                                    <h5>Download your notarised documents</h5>
                                    <p>Download your officially notarised documents here.</p>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a href="#"> <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Main content end -->


@endsection
