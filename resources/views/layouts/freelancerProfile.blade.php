@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="position-relative">
                        <figure class="overflow-hidden mb-0 d-flex justify-content-center">
                            <img src="{{ url('backend/assets/images/wallpaper.jpg') }}" class="rounded-top" alt="profile cover"
                                style="object-fit: cover; border-radius: 10px;" width="100%" height="370">
                        </figure>
                        <div
                            class="d-flex justify-content-between align-items-center position-absolute top-90 w-100 px-2 px-md-4 mt-n4">
                            <div class="d-flex align-items-center">
                                @if ($freelancer->picasset == null)
                                    <img class="wd-70 ht-70 rounded-circle" style="object-fit: cover; border: 1px solid #999; "
                                        src="{{ url('backend/assets/images/no_image.jpg') }}" alt="profile">
                                @else
                                    <img class="wd-70 ht-70 rounded-circle" style="object-fit: cover; "
                                        src="{{ asset($freelancer->picasset) }}"
                                        alt="profile">
                                @endif

                                <div class="row ms-2">
                                    <span class="h4 text-dark">{{ $freelancer->name }}</span>
                                    <span class="h5 text-muted">{{ $freelancer->email }}</span>
                                </div>
                            </div>
                            <div class="d-none d-md-block">
                                <button class="btn btn-primary btn-icon-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-edit btn-icon-prepend">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg> Edit profile
                                </button>
                            </div>
                        </div>
                    </div>
                 
                </div>
            </div>
        </div>
        <div class="row profile-body">
            <!-- left wrapper start -->
            <div class="d-none d-md-block col-md-4 col-xl-3 left-wrapper">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="card-title mb-0">About</h6>
                        </div>
                        <p>{{ $freelancer->description }}</p>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Member Since</label>
                            <p class="text-muted">{{ $formattedDate }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Email</label>
                            <p class="text-muted">{{ $freelancer->email }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Languages</label>
                            <div class="d-flex align-items-center">
                                <span class="text-muted" style="font-size: 2rem;">
                                    <i class="mdi mdi-translate"></i>
                                </span>
                                <div class="row ms-2">
                                    <p class="text-muted" style="font-size: 0.8rem;">English</p>
                                    <p class="text-info">Conversational</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Skills</label>
                            <div class="d-flex flex-wrap">
                                <span style="font-weight: lighter;" class="badge badge-dark me-2">Logo Design</span>
                                <span style="font-weight: lighter;" class="badge badge-dark me-2">Flutter</span>
                                <span style="font-weight: lighter;" class="badge badge-dark me-2">Drawing</span>
                                <!-- Add more tags as needed -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- left wrapper end -->
            <!-- middle wrapper start -->
            <div class="col-md-8 col-xl-5 middle-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="card rounded">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img class="img-xs rounded-circle" src="https://via.placeholder.com/37x37"
                                            alt="">
                                        <div class="ms-2">
                                            <p>Mike Popescu</p>
                                            <p class="tx-11 text-muted">1 min ago</p>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-more-horizontal icon-lg pb-3px">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-meh icon-sm me-2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="8" y1="15" x2="16" y2="15">
                                                    </line>
                                                    <line x1="9" y1="9" x2="9.01" y2="9">
                                                    </line>
                                                    <line x1="15" y1="9" x2="15.01" y2="9">
                                                    </line>
                                                </svg> <span class="">Unfollow</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-corner-right-up icon-sm me-2">
                                                    <polyline points="10 9 15 4 20 9"></polyline>
                                                    <path d="M4 20h7a4 4 0 0 0 4-4V4"></path>
                                                </svg> <span class="">Go to post</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-share-2 icon-sm me-2">
                                                    <circle cx="18" cy="5" r="3"></circle>
                                                    <circle cx="6" cy="12" r="3"></circle>
                                                    <circle cx="18" cy="19" r="3"></circle>
                                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49">
                                                    </line>
                                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49">
                                                    </line>
                                                </svg> <span class="">Share</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-copy icon-sm me-2">
                                                    <rect x="9" y="9" width="13" height="13"
                                                        rx="2" ry="2"></rect>
                                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                    </path>
                                                </svg> <span class="">Copy link</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-3 tx-14">Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                    Accusamus
                                    minima delectus nemo unde quae recusandae assumenda.</p>
                                <img class="img-fluid" src="https://via.placeholder.com/689x430" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card rounded">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img class="img-xs rounded-circle" src="https://via.placeholder.com/37x37"
                                            alt="">
                                        <div class="ms-2">
                                            <p>Mike Popescu</p>
                                            <p class="tx-11 text-muted">5 min ago</p>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-more-horizontal icon-lg pb-3px">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-meh icon-sm me-2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="8" y1="15" x2="16" y2="15">
                                                    </line>
                                                    <line x1="9" y1="9" x2="9.01" y2="9">
                                                    </line>
                                                    <line x1="15" y1="9" x2="15.01" y2="9">
                                                    </line>
                                                </svg> <span class="">Unfollow</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-corner-right-up icon-sm me-2">
                                                    <polyline points="10 9 15 4 20 9"></polyline>
                                                    <path d="M4 20h7a4 4 0 0 0 4-4V4"></path>
                                                </svg> <span class="">Go to post</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-share-2 icon-sm me-2">
                                                    <circle cx="18" cy="5" r="3"></circle>
                                                    <circle cx="6" cy="12" r="3"></circle>
                                                    <circle cx="18" cy="19" r="3"></circle>
                                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49">
                                                    </line>
                                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49">
                                                    </line>
                                                </svg> <span class="">Share</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-copy icon-sm me-2">
                                                    <rect x="9" y="9" width="13" height="13"
                                                        rx="2" ry="2"></rect>
                                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                    </path>
                                                </svg> <span class="">Copy link</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-3 tx-14">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                                <img class="img-fluid" src="https://via.placeholder.com/689x430" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- middle wrapper end -->
            <!-- right wrapper start -->
            <div class="d-none d-xl-block col-xl-4">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="card rounded">
                            <div class="card-body">
                                <h6 class="card-title">Portfolio
                                    <a href="#" class="float-end">See All</a>
                                </h6>
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <div
                                                style="position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                                                <img src="{{ url('backend/assets/images/wallpaper.jpg') }}"
                                                    class="d-block w-100 img-fluid" alt="..."
                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5>First slide label</h5>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div
                                                style="position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                                                <img src="{{ url('backend/assets/images/try1.jpg') }}"
                                                    class="d-block w-100 img-fluid" alt="..."
                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5>Second slide label</h5>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div
                                                style="position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                                                <img src="{{ url('backend/assets/images/try2.jpg') }}"
                                                    class="d-block w-100 img-fluid" alt="..."
                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5>Third slide label</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" data-bs-target="#carouselExampleControls"
                                        role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" data-bs-target="#carouselExampleControls"
                                        role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 grid-margin">
                        <div class="card rounded">
                            <div class="card-body">
                                <h6 class="card-title">Costumer Review</h6>
                                <div class="item-container"> 
                                    <i style="color: #ffc107;" class="star-icon mdi mdi-star"></i>
                                    <a class="text-dark">4.7</a>
                                    <a class="text-dark">(169 reviews)</a>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- right wrapper end -->
        </div>

    </div>
@endsection
