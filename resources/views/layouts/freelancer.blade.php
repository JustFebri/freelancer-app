@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb" style="display: flex; justify-content: flex-end;">
                <a href="{{ route('freelancer.request') }}" class="btn btn-primary" role="button"
                    style="margin-right: 20px; position: relative; ">
                    <i data-feather="file-text" style="padding-right: 5px;"></i>Freelancer Request
                    @if ($pendingFreelancerCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-danger">
                            {{ $pendingFreelancerCount }}
                            <span class="visually-hidden">New Request</span>
                        </span>
                    @endif
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddfreelancer">
                    <i data-feather="user-plus" style="padding-right: 5px"></i> Add Freelancer
                </button>
                <div class="modal fade" id="modalAddfreelancer" tabindex="-1" aria-labelledby="modalAddfreelancerTitle"
                    aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAddfreelancerTitle">Add Freelancer</h5>
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
                                action="{{ route('freelancer.store') }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="scrollable-content">
                                        <div class="mb-3">
                                            <label for="InputName" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="InputName" autocomplete="off"
                                                placeholder="Name" name="name" @error('name') is-invalid @enderror">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="InputEmail" class="form-label">Email address</label>
                                            <input type="email" name="email" class="form-control" id="InputEmail"
                                                placeholder="Email" @error('Email') is-invalid @enderror>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="InputPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="InputPassword"
                                                autocomplete="off" placeholder="Password" name="password"
                                                @error('Password') is-invalid @enderror>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="Inputidentity_number" class="form-label">Identity Number</label>
                                            <input type="numeric" class="form-control" id="Inputidentity_number"
                                                autocomplete="off" placeholder="Identity Number/NIK" name="identity_number"
                                                @error('identity_number') is-invalid @enderror>
                                            @error('identity_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="InputInformation" class="form-label">Freelancer Information</label>
                                            <textarea name="description" id="InputInformation" class="form-control" maxlength="500" rows="4"
                                                placeholder="This textarea has a limit of 500 chars."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="InputStatus" class="form-label">Status</label>
                                            <select class="form-select" id="InputStatus" id="InputStatus" name="status">
                                                <option selected="">active</option>
                                                <option>suspended</option>
                                                <option>pending approval</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="image">Profile Picture</label>
                                            <input class="form-control" accept="image/*" type="file" id="image"
                                                name="photo" @error('photo') is-invalid @enderror>
                                            @error('photo')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="showImage"></label>
                                            <img id="showImage" class="wd-80 ht-80 rounded-circle border border-dark"
                                                src=" {{ url('backend/assets/images/no_image.jpg') }} " alt="profile"
                                                style="object-fit: cover;">
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </ol>

        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">freelancer Table</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NIK</th>
                                        <th>Rating</th>
                                        <th>Total Sales</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($db_freelancer as $key => $item)
                                        <tr>
                                            <td>
                                                <a
                                                    href="{{ route('freelancer.profile', $item->freelancer_id) }}">Freelancer-{{ $item->freelancer_id }}</a>
                                            </td>

                                            <td>
                                                @if ($item->picasset == null)
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ url('backend/assets/images/no_image.jpg') }}"
                                                        alt="profile" style="object-fit: cover; ">
                                                @else
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ asset($item->picasset) }}" alt="profile"
                                                        style="object-fit: cover; ">
                                                @endif
                                                <span>{{ $item->name }}</span>
                                            </td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->identity_number }}</td>
                                            <td>{{ $item->rating }}</td>
                                            <td>{{ $item->revenue }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td>
                                                <a class="btn btn-inverse-warning" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditfreelancer{{ $item->freelancer_id }}">Edit</a>
                                                @if ($item->picture_id != null)
                                                    <a href="{{ route('freelancer.delete.pic', ['freelancer_id' => $item->freelancer_id, 'user_id' => $item->user_id, 'picture_id' => $item->picture_id]) }}"
                                                        class="btn btn-inverse-danger" id="delete">Delete</a>
                                                @else
                                                    <a href="{{ route('freelancer.delete', ['freelancer_id' => $item->freelancer_id, 'user_id' => $item->user_id]) }}"
                                                        class="btn btn-inverse-danger" id="delete">Delete</a>
                                                @endif

                                            </td>
                                            <div class="modal fade" id="modalEditfreelancer{{ $item->freelancer_id }}"
                                                tabindex="-1" aria-labelledby="modalEditfreelancerTitle"
                                                aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalEditfreelancerTitle">Edit
                                                                Service</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                        </div>
                                                        <form class="forms-sample" method="POST"
                                                            enctype="multipart/form-data"
                                                            action="{{ route('freelancer.edit') }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id"
                                                                    value="{{ $item->user_id }}">
                                                                <div class="mb-3">
                                                                    <label for="InputName{{ $item->freelancer_id }}"
                                                                        class="form-label">Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="InputName{{ $item->freelancer_id }}"
                                                                        autocomplete="off" placeholder="Name"
                                                                        name="name" @error('name') is-invalid @enderror"
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
                                                                    <label
                                                                        for="InputInformation{{ $item->freelancer_id }}"
                                                                        class="form-label">Freelancer Information</label>
                                                                    <textarea name="description" id="description{{ $item->freelancer_id }}" class="form-control" maxlength="500"
                                                                        rows="4" placeholder="This textarea has a limit of 500 chars."
                                                                        @error('description') is-invalid @enderror>{{ $item->description }}</textarea>
                                                                    @error('description')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
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
                                                                        src="{{ !empty($item->picasset) ? asset($item->picasset) : url('backend/assets/images/no_image.jpg') }}"
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
                console.log("Image changed");
            });
        });
    </script>
@endsection
