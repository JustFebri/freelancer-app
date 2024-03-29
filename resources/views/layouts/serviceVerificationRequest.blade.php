@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Service Verification Request</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Freelancer</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>IsApproved</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($db_service as $key => $item)
                                        <tr>
                                            <td><a
                                                    href="{{ route('service.request.details', $item->service_id) }}">Product-{{ $item->service_id }}</a>
                                            </td>

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
                                            <td>{{ $item->category_name }}</td>
                                            <td>{{ $item->subcategory_name }}</td>
                                            <td>{{ $item->IsApproved }}</td>
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
