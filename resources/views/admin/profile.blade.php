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
                    </div>
                </div>
            </div>
            <!-- left wrapper end -->
            <!-- middle wrapper start -->
            <div class="col-md-8 col-xl-8 middle-wrapper">
                <div class="row">
                    <div class="card">
                        <div class="card-body">

                            <h6 class="card-title">Update Admin Profile</h6>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data"
                                action="{{ route('profile.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="exampleInputUsername1" class="form-label">Username</label>
                                    <input name="name" type="text" class="form-control" id="exampleInputUsername1"
                                        autocomplete="off" placeholder="Username" value="{{ $profileData->name }}" @disabled(true)>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                                    <input name="email" type="email" class="form-control" id="exampleInputEmail1"
                                        placeholder="Email" value="{{ $profileData->email }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formFile">File upload</label>
                                    <input class="form-control" type="file" id="image" accept="image/*" name="photo">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formFile"></label>
                                    <img id="showImage" class="wd-80 ht-80 rounded-circle border border-dark"
                                        src="{{ !empty($profileData->file) ? 'data:' . $profileData->filetype . ';base64,' . base64_encode($profileData->file) : url('backend/assets/images/no_image.jpg') }}"
                                        alt="profile" style="object-fit: cover; ">
                                </div>

                                <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>
@endsection
