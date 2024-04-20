@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6
                                style="text-transform: uppercase;
                            font-size: 0.875rem;
                            font-weight: 500;">
                                Service Data</h6>
                        </div>
                        <div class="row profile-body">
                            <!-- left wrapper start -->
                            <div class="d-none d-md-block col-md-4 col-xl-4 left-wrapper">
                                <div class="card rounded">
                                    <div class="card-body">
                                        <div id="carouselExampleIndicators" class="carousel slide carousel-dark"
                                            data-bs-ride="carousel">
                                            <div class="carousel-indicators">
                                                @foreach ($picture as $key => $item)
                                                    @if ($loop->index == 0)
                                                        <button type="button" data-bs-target="#carouselExampleIndicators"
                                                            data-bs-slide-to="{{ $loop->index }}" class="active"
                                                            aria-current="true"
                                                            aria-label="Slide {{ $loop->index + 1 }}"></button>
                                                    @else
                                                        <button type="button" data-bs-target="#carouselExampleIndicators"
                                                            data-bs-slide-to="{{ $loop->index }}"
                                                            aria-label="Slide {{ $loop->index + 1 }}"></button>
                                                    @endif
                                                @endforeach
                                            </div>

                                            <div class="carousel-inner">
                                                @foreach ($picture as $key => $item)
                                                    @if ($loop->index == 0)
                                                        <div class="carousel-item active" data-bs-interval="3000">
                                                            <img src="{{ asset($item->picasset) }}"
                                                                style="display: block; object-fit: contain; width: 100%; height: 200px;">
                                                        </div>
                                                    @else
                                                        <div class="carousel-item" data-bs-interval="3000">
                                                            <img src="{{ asset($item->picasset) }}"
                                                                style="display: block; object-fit: contain; width: 100%; height: 500px;">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>

                                            <button class="carousel-control-prev" type="button"
                                                data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button"
                                                data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                        <p class="mt-3" style="font-size: 24px" text-transform: capitalize;>
                                            {{ $service->title }} ({{ $service->type }})</p>
                                        <a class="text-muted mt-0" style="font-size: 16px" text-transform: capitalize;
                                            href="{{ route('freelancer.profile', $service->freelancer_id) }}">
                                            {{ $service->name }}</a>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Description</label>
                                            <p>{!! nl2br(e($service->description)) !!}</p>
                                        </div>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Category</label>
                                            <p class="mb-2">{{ $service->category_name }}</p>
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Sub-Category</label>
                                            <p class="mb-2">{{ $service->subcategory_name }}</p>
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Custom Order</label>
                                            <p class="mb-2 text-capitalize" style="color:cornflowerblue">
                                                {{ $service->custom_order }}</p>
                                            @if ($service->location != null || $service->location != '')
                                                <label class="fw-bolder mb-2" style="font-size: 16px">Location</label>

                                                <p class="mb-2">{{ $service->location }}</p>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xl-8 middle-wrapper">
                                <div class="row">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Service Package Information</h6>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Name</th>
                                                        @foreach ($package as $key => $item)
                                                            <th scope="col" style="">
                                                                {{ $item->title }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">Description</th>

                                                        @foreach ($package as $key => $item)
                                                            <td style="white-space: pre-wrap;">{{ $item->description }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Price</th>
                                                        @foreach ($package as $key => $item)
                                                            <td>Rp. {{ $item->price }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    @if ($service->type != 'On-Site Service')
                                                        <tr>
                                                            <th scope="row">Delivery Days</th>
                                                            @foreach ($package as $key => $item)
                                                                <td>
                                                                    {{ $item->delivery_days }} Days Delivery</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Revision</th>
                                                            @foreach ($package as $key => $item)
                                                                <td>{{ $item->revision }}
                                                                    Revision
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
