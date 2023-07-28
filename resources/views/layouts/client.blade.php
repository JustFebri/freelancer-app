@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddClient">
                    <i data-feather="user-plus" style="padding-right: 5px"></i> Add Client
                </button>
                <div class="modal fade" id="modalAddClient" tabindex="-1" aria-labelledby="modalAddClientTitle"
                    aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAddClientTitle">Add Client</h5>
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
                                action="{{ route('client.store') }}">
                                @csrf
                                <div class="modal-body">
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
                                            placeholder="Email" @error('email') is-invalid @enderror">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="InputPassword" autocomplete="off"
                                            placeholder="Password" name="password" @error('Password') is-invalid @enderror">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputLocation" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="InputLocation" autocomplete="off"
                                            placeholder="Location" name="location">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputStatus" class="form-label">Status</label>
                                        <select class="form-select" id="InputStatus" id="InputStatus" name="status">
                                            <option selected="">active</option>
                                            <option>suspended</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="image">Profile Picture</label>
                                        <input class="form-control" accept="image/*" type="file" id="image"
                                            name="photo">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="showImage"></label>
                                        <img id="showImage" class="wd-80 ht-80 rounded-circle border border-dark"
                                            src=" {{ url('backend/assets/images/no_image.jpg') }} " alt="profile"
                                            style="object-fit: cover;">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        <h6 class="card-title">Client Table</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($type as $key => $item)
                                        <tr>
                                            <td>{{ $item->client_id }}</td>
                                            <td>
                                                @if ($item->file == null)
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ url('backend/assets/images/no_image.jpg') }}"
                                                        alt="profile" style="object-fit: cover; ">
                                                @else
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ 'data:' . $item->filetype . ';base64,' . base64_encode($item->file) }}"
                                                        alt="profile" style="object-fit: cover; ">
                                                @endif
                                                <span>{{ $item->name }}</span>
                                            </td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @if ($item->location == null)
                                                    Null
                                                @else
                                                    {{ $item->location }}
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td>
                                                <a class="btn btn-inverse-warning" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditClient{{ $item->client_id }}">Edit</a>
                                                @if ($item->picture_id != null)
                                                    <a href="{{ route('client.delete.pic', ['client_id' => $item->client_id, 'picture_id' => $item->picture_id]) }}"
                                                        class="btn btn-inverse-danger" id="delete">Delete</a>
                                                @else
                                                    <a href="{{ route('client.delete', $item->client_id) }}"
                                                        class="btn btn-inverse-danger" id="delete">Delete</a>
                                                @endif

                                            </td>
                                            <div class="modal fade" id="modalEditClient{{ $item->client_id }}"
                                                tabindex="-1" aria-labelledby="modalEditClientTitle" aria-hidden="true"
                                                style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalEditClientTitle">Edit
                                                                Client</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                        </div>
                                                        <form class="forms-sample" method="POST"
                                                            enctype="multipart/form-data"
                                                            action="{{ route('client.edit') }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id"
                                                                    value="{{ $item->client_id }}">
                                                                <div class="mb-3">
                                                                    <label for="InputName{{ $item->client_id }}"
                                                                        class="form-label">Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="InputName{{ $item->client_id }}"
                                                                        autocomplete="off" placeholder="Name"
                                                                        name="name" @error('name') is-invalid @enderror"
                                                                        value="{{ $item->name }}">
                                                                    @error('name')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputEmail{{ $item->client_id }}"
                                                                        class="form-label">Email
                                                                        address</label>
                                                                    <input type="email" name="email"
                                                                        class="form-control"
                                                                        id="InputEmail{{ $item->client_id }}"
                                                                        placeholder="Email"
                                                                        @error('email') is-invalid @enderror"
                                                                        value="{{ $item->email }}">
                                                                    @error('email')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputLocation{{ $item->client_id }}"
                                                                        class="form-label">Location</label>
                                                                    <input type="text" class="form-control"
                                                                        id="InputLocation{{ $item->client_id }}"
                                                                        autocomplete="off" placeholder="Location"
                                                                        name="location" value="{{ $item->location }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="InputStatus{{ $item->client_id }}"
                                                                        class="form-label">Status</label>
                                                                    <select class="form-select"
                                                                        id="InputStatus{{ $item->client_id }}"
                                                                        name="status">
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
                                                                        for="image{{ $item->client_id }}">Profile
                                                                        Picture</label>
                                                                    <input class="form-control" accept="image/*"
                                                                        type="file" id="image{{ $item->client_id }}"
                                                                        name="photo">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="showImage{{ $item->client_id }}"></label>
                                                                    <img id="showImage{{ $item->client_id }}"
                                                                        class="wd-80 ht-80 rounded-circle border border-dark"
                                                                        src="{{ !empty($item->file) ? 'data:' . $item->filetype . ';base64,' . base64_encode($item->file) : url('backend/assets/images/no_image.jpg') }}"
                                                                        alt="profile" style="object-fit: cover;">
                                                                </div>
                                                                <script type="text/javascript">
                                                                    $(document).ready(function() {
                                                                        $('#image{{ $item->client_id }}').change(function(e) {
                                                                            var reader = new FileReader();
                                                                            reader.onload = function(e) {
                                                                                $('#showImage{{ $item->client_id }}').attr('src', e.target.result);
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
