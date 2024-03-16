@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb" style="display: flex; justify-content: flex-end;">

                <a href="{{ route('service.request') }}" class="btn btn-primary" role="button" style="margin-right: 20px; position: relative; ">
                    <i data-feather="file-text" style="padding-right: 5px;"></i>Service Request
                    @if ($pendingServiceCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-danger">
                            {{ $pendingServiceCount }}
                            <span class="visually-hidden">New Request</span>
                        </span>
                    @endif
                </a>
                <div class="modal fade" id="modalAddClient" tabindex="-1" aria-labelledby="modalAddClientTitle"
                    aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAddClientTitle">Add Service</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="btn-close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="forms-sample">
                                    <div class="mb-3">
                                        <label for="InputName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="InputName" autocomplete="off"
                                            placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputEmail" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="InputEmail" placeholder="Email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="InputPassword" autocomplete="off"
                                            placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="formFile">Profile Picture</label>
                                        <input class="form-control" accept="image/*" / type="file" id="formFile">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </ol>

        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Service Table</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Freelancer</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($db_service as $key => $item)
                                        <tr>
                                            <td><a href="#">Product-{{ $item->service_id }}</a></td>

                                            <td>
                                                @php
                                                    $imageCount = App\Models\service_img::where('service_id', $item->service_id)->count();
                                                    if ($imageCount > 0) {
                                                        $count = 1;
                                                        $imageData = App\Models\service_img::join('picture as p', 'service_img.picture_id', '=', 'p.picture_id')
                                                        ->where('service_img.service_id', '=', $item->service_id)
                                                        ->first();
                                                    } else {
                                                        $count = 0;
                                                    }
                                                @endphp
                                                @if ($count == 0)
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ url('backend/assets/images/no_image.jpg') }}" alt="profile"
                                                        style="object-fit: cover; ">
                                                @else
                                                    <img id="showImage"
                                                        class="wd-80 ht-80 rounded-circle border border-dark me-3"
                                                        src="{{ asset($imageData->picasset) }}" alt="profile"
                                                        style="object-fit: cover; ">
                                                @endif
                                                <span>{{ $item->title }}</span>
                                            </td>
                                            <td><a
                                                    href="{{ route('freelancer.profile', $item->freelancer_id) }}">{{ $item->name }}</a>
                                            </td>
                                            <td>{{ $item->type }}</td>
                                            <td>
                                                @if ($item->location == null)
                                                    Null
                                                @else
                                                    {{ $item->location }}
                                                @endif
                                            </td>
                                            <td>{{ $item->category_name }}</td>
                                            <td>{{ $item->subcategory_name }}</td>
                                            <td>
                                                @if ($item->status == null)
                                                    Null
                                                @else
                                                    {{ $item->status }}
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
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
