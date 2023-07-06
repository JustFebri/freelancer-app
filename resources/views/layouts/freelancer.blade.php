@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary" style="margin-right: 10px;">
                    Freelancer Request
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddFreelancer">
                    Add Freelancer
                </button>
                <div class="modal fade" id="modalAddFreelancer" tabindex="-1" aria-labelledby="modalAddFreelancerTitle"
                    aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAddFreelancerTitle">Add Freelancer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="btn-close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="forms-sample">
                                    <div class="mb-3">
                                        <label for="InputName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="InputName" autocomplete="off"
                                            placeholder="Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputEmail" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="InputEmail" placeholder="Email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputIdentity" class="form-label">Identity Number</label>
                                        <input type="number" class="form-control" id="InputIdentity" placeholder="Email" max="16" min="16">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="InputPassword" autocomplete="off"
                                            placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputLocation" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="InputLocation" autocomplete="off"
                                            placeholder="Location">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="formFile">Profile Picture</label>
                                        <input class="form-control" accept="image/*" / type="file" id="formFile">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label for="InputInformation" class="col-form-label">Information</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <textarea id="InputInformation" class="form-control" maxlength="150" rows="8" placeholder="Enter your freelancer information here...(max limit 150 chars)"></textarea>
                                        </div>
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
                        <h6 class="card-title">Freelancer Table</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($collection as $item)
                                        <tr>
                                            <td>{{ $item['Name'] }}</td>
                                            <td>{{ $item['Email'] }}</td>
                                            <td>{{ $item['Location'] }}</td>
                                            <td>{{ $item['Created at'] }}</td>
                                            <td>{{ $item['Updated at'] }}</td>
                                            <td>{{ $item['Status'] }}</td>
                                            <td>
                                                <a href="" class="btn btn-inverse-warning">Edit</a>
                                                <a href="" class="btn btn-inverse-danger">Delete</a>
                                            </td>
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
