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
