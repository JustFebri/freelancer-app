@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">



        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Order Table</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Service Id</th>
                                        <th>Client Name</th>
                                        <th>Freelancer Name</th>
                                        <th>Status</th>
                                        <th>Price</th>
                                        <th>Payment Method</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($type as $key => $item)
                                        <tr>
                                            <td><a href="#">{{$item->order_id}}</a></td>
                                            <td>{{$item->service_id}}
                                            </td>
                                            <td>{{$item->buyer}}</td>
                                            <td>{{$item->seller}}</td>
                                            <td>{{$item->order_status}}</td>
                                            <td>Rp {{$item->amount}}</td>
                                            <td>{{$item->payment}}</td>
                                            <td>{{$item->created_at}}</td>
                                            <td>
                                                {{$item->updated_at}}
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
