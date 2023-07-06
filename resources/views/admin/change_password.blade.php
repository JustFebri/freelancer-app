@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">

        <div class="row profile-body">
            <!-- left wrapper start -->
            <div class="d-none d-md-block col-md-4 col-xl-4 left-wrapper">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <div>
                                {{-- <img class="wd-100 rounded-circle border border-dark"
                                    src="{{ !empty($profileData->file) ? url('upload/profile_images/' . $profileData->profile_images) : url('backend/assets/images/no_image.jpg') }}"
                                    alt="profile"> --}}
                                <img class="wd-100 ht-100 rounded-circle border border-dark"
                                    src="{{ !empty($profileData->file) ? 'data:' . $profileData->filetype . ';base64,' . base64_encode($profileData->file) : url('backend/assets/images/no_image.jpg') }}"
                                    alt="profile" style="object-fit: cover;">
                                <span class="h4 ms-3 text-dark">{{ $profileData->name }}</span>
                            </div>

                        </div>

                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">name</label>
                            <p class="text-muted">{{ $profileData->name }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">email</label>
                            <p class="text-muted">{{ $profileData->email }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">created at</label>
                            <p class="text-muted">{{ $profileData->created_at }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">updated at</label>
                            <p class="text-muted">{{ $profileData->updated_at }}</p>
                        </div>
                        <div class="mt-3 d-flex social-links">
                            <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                                <i data-feather="github"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                                <i data-feather="twitter"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                                <i data-feather="instagram"></i>
                            </a>
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

                            <h6 class="card-title">Admin Change Password</h6>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data"
                                action="{{ route('update.password') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="old_password" class="form-label">Old Password</label>
                                    <input name="old_password" type="password"
                                        class="form-control @error('old_password') is-invalid @enderror" id="old_password"
                                        autocomplete="off" ">
                                    @error('old_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input name="new_password" type="password"
                                        class="form-control @error('new_password') is-invalid @enderror" id="new_password"
                                        autocomplete="off">
                                    @error('new_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input name="new_password_confirmation" type="password"
                                        class="form-control" id="new_password_confirmation"
                                        autocomplete="off">
                                    </div>

                                <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
