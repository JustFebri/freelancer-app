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
                                    <img class="wd-70 ht-70 rounded-circle"
                                        style="object-fit: cover; border: 1px solid #999; "
                                        src="{{ url('backend/assets/images/no_image.jpg') }}" alt="profile">
                                @else
                                    <img class="wd-70 ht-70 rounded-circle" style="object-fit: cover; "
                                        src="{{ asset($freelancer->picasset) }}" alt="profile">
                                @endif

                                <div class="row ms-2">
                                    <span class="h4 text-dark">{{ $freelancer->name }}</span>
                                    <span class="h5 text-muted">{{ $freelancer->email }}</span>
                                </div>
                            </div>
                            {{-- <div class="d-none d-md-block">
                                <button class="btn btn-primary btn-icon-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-edit btn-icon-prepend">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg> Edit profile
                                </button>
                            </div> --}}
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
                            @foreach ($dataLG as $key => $item)
                                <div class="d-flex align-items-center">
                                    <span class="text-muted" style="font-size: 2rem;">
                                        <i class="mdi mdi-translate"></i>
                                    </span>
                                    <div class="row ms-2">
                                        <p class="text-muted" style="font-size: 0.8rem;">{{ $item->language_name }}</p>
                                        <p class="text-info">{{ $item->proficiency_level }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Skills</label>
                            <div class="d-flex flex-wrap">
                                @foreach ($dataSK as $key => $item)
                                    <span style="font-weight: lighter;"
                                        class="badge badge-dark me-2">{{ $item->skill_name }}</span>
                                @endforeach

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- left wrapper end -->
            <!-- middle wrapper start -->
            <div class="col-md-8 col-xl-5 middle-wrapper">
                <div class="row">
                    @foreach ($services as $key => $item)
                        <div class="col-md-12 grid-margin">
                            <div class="card rounded">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="ms-2">
                                            <a href="{{ route('service.details', $item->service_id) }}">{{$item->title}}</a>
                                            <p class="tx-11 mt-1 text-muted">{{$item->updated_at}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column"> <!-- Added this div -->
                                        <p class="mb-3 tx-14">{{ $item->description }}</p>

                                        <img src="{{ asset($item->picasset) }}" alt="..." class="mb-3"
                                            style=" object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- middle wrapper end -->
            <!-- right wrapper start -->
            <div class="d-none d-xl-block col-xl-4">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="card rounded">
                            <div class="card-body">
                                @if (!$portfolios->isEmpty())
                                    <h6 class="card-title">Portfolio
                                        {{-- <a href="#" class="float-end">See All</a> --}}
                                    </h6>
                                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach ($portfolios as $key => $item)
                                                <div class="carousel-item active">
                                                    <div
                                                        style="position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                                                        <img src="{{ asset($item->picasset) }}"
                                                            class="d-block w-100 img-fluid" alt="..."
                                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                    <div class="carousel-caption d-none d-md-block">
                                                        <h5>{{ $item->title }}</h5>
                                                    </div>
                                                </div>
                                            @endforeach
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
                                @else
                                    <h6 class="card-title">No Portfolio
                                        {{-- <a href="#" class="float-end">See All</a> --}}
                                    </h6>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 grid-margin">
                        <div class="card rounded">
                            <div class="card-body">
                                <h6 class="card-title">Costumer Review</h6>
                                <div class="item-container">
                                    <i style="color: #ffc107;" class="star-icon mdi mdi-star"></i>
                                    <a class="text-dark">{{ $avgRating }}</a>
                                    <a class="text-dark">({{ $reviewCount }} reviews)</a>
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
