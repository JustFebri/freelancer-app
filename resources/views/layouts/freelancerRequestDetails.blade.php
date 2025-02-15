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
                                Freelancer Data</h6>
                            <div class="d-flex">
                                {{-- <form method="POST"
                                    action="{{ route('freelancer.request.reject', ['user_id' => $freelancer->user_id, 'freelancer_id' => $freelancer->freelancer_id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-icon me-1">
                                        <i data-feather="x"></i>
                                    </button>
                                </form> --}}
                                <button type="submit" class="btn btn-danger btn-icon me-1" data-bs-toggle="modal"
                                    data-bs-target="#modalReason">
                                    <i data-feather="x"></i>
                                </button>
                                <div class="modal fade" id="modalReason" tabindex="-1" aria-labelledby="modalReasonTitle"
                                    aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalReasonTitle">Rejection Response</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="btn-close"></button>
                                            </div>
                                            @if ($errors->any())
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                        toastr.error("Validation Error");
                                                    });
                                                </script>
                                            @endif
                                            <form class="forms-sample" method="POST" enctype="multipart/form-data"
                                                action="{{ route('freelancer.request.reject') }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="user_id" value="{{ $freelancer->user_id }}">
                                                    <input type="hidden" name="freelancer_id" value="{{ $freelancer->freelancer_id }}">
                                                    <div class="scrollable-content">
                                                        <textarea name="reason" id="inputReason" class="form-control" maxlength="1000" rows="10"
                                                            placeholder="This textarea has a limit of 1000 chars." @error('reason') is-invalid @enderror></textarea>
                                                        @error('reason')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Send Response</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST"
                                    action="{{ route('freelancer.request.approve', ['user_id' => $freelancer->user_id, 'freelancer_id' => $freelancer->freelancer_id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-icon ms-1">
                                        <i data-feather="check"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="row profile-body">
                            <!-- left wrapper start -->
                            <div class="d-none d-md-block col-md-4 col-xl-4 left-wrapper">
                                <div class="card rounded">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <img class="img-thumbnail"
                                                    src="{{ !empty($freelancer->picasset) ? asset($freelancer->picasset) : url('backend/assets/images/no_image.jpg') }}"
                                                    alt="profile" style="object-fit: cover; height: 300px; width:300px">
                                            </div>
                                        </div>
                                        <p class="mt-3" style="font-size: 24px" text-transform: capitalize;>
                                            {{ $freelancer->name }}</p>
                                        <p class="text-muted mt-0" style="font-size: 16px" text-transform: capitalize;>
                                            {{ $freelancer->email }}</p>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Description</label>
                                            <p>{{ $freelancer->description }}</p>
                                        </div>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Language</label>
                                            @foreach ($language as $key => $item)
                                                <p class="mb-2">{{ $item->language_name }} - <span
                                                        class="text-muted">{{ $item->proficiency_level }}</span>
                                                </p>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Occupation</label>
                                            @foreach ($occupation as $key => $item)
                                                <p class="text-muted mb-2" style="font-size: 16px">
                                                    {{ $item->category_name }}
                                                </p>
                                            @endforeach
                                            <div class="d-flex flex-wrap">
                                                @foreach ($sub_occupation as $key => $item)
                                                    <span style="font-weight: lighter;"
                                                        class="badge badge-dark me-2">{{ $item->subcategory_name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Skills</label>
                                            <div class="d-flex flex-wrap">
                                                @foreach ($skills as $key => $item)
                                                    <span style="font-weight: lighter; text-transform: capitalize;"
                                                        class="badge badge-dark me-2">{{ $item->skill_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mt-3">
                                            <label class="fw-bolder mb-2" style="font-size: 16px">Personal Website</label>
                                            @foreach ($url as $key => $item)
                                                <p>
                                                    <a class="link-opacity-100-hover" href="{{ $item->personalUrl }}"
                                                        target="_blank"
                                                        style="text-decoration: underline;">{{ $item->personalUrl }}</a>
                                                </p>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- left wrapper end -->
                            <!-- middle wrapper start -->
                            <div class="col-md-8 col-xl-8 middle-wrapper">
                                <div class="row">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Freelancer KTP Information</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">NIK</label>
                                                <p>{{ $freelancer->identity_number }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Name</label>
                                                <p style="text-transform: capitalize;">{{ $freelancer->name }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Gender</label>
                                                <p style="text-transform: capitalize;">{{ $freelancer->identity_gender }}
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Address</label>
                                                <p style="text-transform: capitalize;">{{ $freelancer->identity_address }}
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">KTP Picture</label>
                                                <br>
                                                <img class="rounded "
                                                    src=" {{ !empty($freelancer->p1) ? asset($freelancer->p1) : url('https://placehold.co/500x500') }}"
                                                    alt="ktpSelfie" style="max-width: 100%; max-height: 300px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted">KTP Picture with Selfie</label>
                                                <br>
                                                <img class="rounded "
                                                    src=" {{ !empty($freelancer->p2) ? asset($freelancer->p2) : url('https://placehold.co/500x500') }}"
                                                    alt="ktp"
                                                    style="img-thumbnail max-width: 100%; max-height: 300px;">
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
    </div>
@endsection
