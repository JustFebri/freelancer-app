@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary" style="margin-right: 10px">
                    <i data-feather="file-text" style="padding-right: 5px"></i> Service Request
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddClient">
                    <i data-feather="file-plus" style="padding-right: 5px"></i> Add Service
                </button>
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
                                        <label for="InputLocation" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="InputLocation" autocomplete="off"
                                            placeholder="Location">
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
                                        <th>Category</th>
                                        <th>Freelancer</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td><a href="#">Product-001</a></td>
                                        <td>
                                            <img style="object-fit: cover;"
                                                src="https://fiverr-res.cloudinary.com/images/t_smartwm/t_main1,q_auto,f_auto,q_auto,f_auto/attachments/delivery/asset/f8cf0e0d6a01c51cb75aa4fab2644b50-1679520154/recursivefaults_hipinspire/design-a-creative-and-unique-mobile-app.png"
                                                class="rounded me-2" alt="profile-image">
                                            <span>I will do mobile app ui ux design</span>
                                        </td>
                                        <td>App Design</td>
                                        <td><a href="#">Denis Kravets</a></td>
                                        <td>Rp 4.500.000</td>
                                        <td>Active</td>
                                        <td>2023-07-09 18:06:58</td>
                                        <td>2023-07-13 12:44:33</td>
                                        <td>
                                            <a href="" class="btn btn-inverse-warning">Edit</a>
                                            <a href="" class="btn btn-inverse-danger">Delete</a>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
