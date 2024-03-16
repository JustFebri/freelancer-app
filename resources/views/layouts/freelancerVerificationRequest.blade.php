@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Freelancer Verification Request</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NIK</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                        <th>Is Approved</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($db_freelancer as $key => $item)
                                        <tr>
                                            <td>
                                                <a
                                                    href="{{ route('freelancer.request.details', $item->freelancer_id) }}">Freelancer-{{ $item->freelancer_id }}</a>
                                            </td>

                                            <td>
                                                @if ($item->picasset == null)
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ url('backend/assets/images/no_image.jpg') }}" alt="profile"
                                                        style="object-fit: cover; ">
                                                @else
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ asset($item->picasset)  }}"
                                                        alt="profile" style="object-fit: cover; ">
                                                @endif
                                                <span>{{ $item->name }}</span>
                                            </td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->identity_number }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td style="text-transform: uppercase">{{ $item->IsApproved }}</td>
                                            <td>
                                                <a class="btn btn-inverse-warning" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditfreelancer{{ $item->freelancer_id }}">Edit</a>
                                                @if ($item->picture_id != null)
                                                    <a href="" class="btn btn-inverse-danger"
                                                        id="delete">Delete</a>
                                                @else
                                                    <a href="" class="btn btn-inverse-danger"
                                                        id="delete">Delete</a>
                                                @endif

                                            </td>
                                            <div class="modal fade" id="modalEditfreelancer{{ $item->freelancer_id }}"
                                                tabindex="-1" aria-labelledby="modalEditfreelancerTitle" aria-hidden="true"
                                                style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalEditfreelancerTitle">Edit
                                                                Freelancer</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="btn-close"></button>
                                                        </div>
                                                        <form class="forms-sample" method="POST"
                                                            enctype="multipart/form-data" action="">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id"
                                                                    value="{{ $item->user_id }}">
                                                                <div class="mb-3">
                                                                    <label for="InputName{{ $item->freelancer_id }}"
                                                                        class="form-label">Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="InputName{{ $item->freelancer_id }}"
                                                                        autocomplete="off" placeholder="Name" name="name"
                                                                        @error('name') is-invalid @enderror"
                                                                        value="{{ $item->name }}">
                                                                    @error('name')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputEmail{{ $item->freelancer_id }}"
                                                                        class="form-label">Email
                                                                        address</label>
                                                                    <input type="email" name="email"
                                                                        class="form-control"
                                                                        id="InputEmail{{ $item->freelancer_id }}"
                                                                        placeholder="Email"
                                                                        @error('Email') is-invalid @enderror"
                                                                        value="{{ $item->email }}">
                                                                    @error('email')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        for="Inputidentity_number{{ $item->freelancer_id }}"
                                                                        class="form-label">Identity Number</label>
                                                                    <input type="numeric" class="form-control"
                                                                        id="Inputidentity_number{{ $item->freelancer_id }}"
                                                                        autocomplete="off"
                                                                        value="{{ $item->identity_number }}"
                                                                        placeholder="Identity Number/NIK"
                                                                        name="identity_number"
                                                                        @error('identity_number') is-invalid @enderror>
                                                                    @error('identity_number')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputInformation{{ $item->freelancer_id }}"
                                                                        class="form-label">Freelancer Information</label>
                                                                    <textarea name="information" id="InputInformation{{ $item->freelancer_id }}" class="form-control" maxlength="200"
                                                                        rows="4" placeholder="This textarea has a limit of 200 chars.">{{ $item->description }}</textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputStatus{{ $item->freelancer_id }}"
                                                                        class="form-label">Status</label>
                                                                    <select class="form-select"
                                                                        id="InputStatus{{ $item->freelancer_id }}"
                                                                        id="InputStatus" name="status">
                                                                        <option selected="">{{ $item->status }}
                                                                        </option>
                                                                        <option>
                                                                            @if ($item->status == 'active')
                                                                                suspended
                                                                            @else
                                                                                active
                                                                            @endif
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="image{{ $item->freelancer_id }}">Profile
                                                                        Picture</label>
                                                                    <input class="form-control" accept="image/*"
                                                                        type="file"
                                                                        id="image{{ $item->freelancer_id }}"
                                                                        name="photo">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="showImage{{ $item->freelancer_id }}"></label>
                                                                    <img id="showImage{{ $item->freelancer_id }}"
                                                                        class="wd-80 ht-80 rounded-circle border border-dark"
                                                                        src="{{ !empty($item->file) ? 'data:' . $item->filetype . ';base64,' . base64_encode($item->file) : url('backend/assets/images/no_image.jpg') }}"
                                                                        alt="profile" style="object-fit: cover;">
                                                                </div>
                                                                <script type="text/javascript">
                                                                    $(document).ready(function() {
                                                                        $('#image{{ $item->freelancer_id }}').change(function(e) {
                                                                            var reader = new FileReader();
                                                                            reader.onload = function(e) {
                                                                                $('#showImage{{ $item->freelancer_id }}').attr('src', e.target.result);
                                                                            }
                                                                            reader.readAsDataURL(e.target.files['0']);
                                                                            console.log("Image changed");
                                                                        });
                                                                    });
                                                                </script>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
