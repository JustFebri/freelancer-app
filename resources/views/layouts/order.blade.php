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
                                        <th>Package Id</th>
                                        <th>Custom Id</th>
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
                                            <td><a href="#"
                                                    id="openModal{{ $item->order_id }}">{{ $item->order_id }}</a></td>
                                            <td>{{ $item->service_id }}</td>
                                            <td>{{ $item->package_id !== null ? $item->package_id : 'null' }}</td>
                                            <td>{{ $item->custom_id !== null ? $item->custom_id : 'null' }}</td>
                                            <td>{{ $item->buyer }}</td>
                                            <td>{{ $item->seller }}</td>
                                            <td>{{ $item->order_status }}</td>
                                            <td>Rp {{ $item->amount }}</td>
                                            <td>{{ $item->payment }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                        </tr>

                                        <!-- Modal for current item -->
                                        <div class="modal fade" id="exampleModal{{ $item->order_id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalLabel{{ $item->order_id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="exampleModalLabel{{ $item->order_id }}">
                                                            {{ $item->service->title }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if ($item->package->title == null)
                                                        <p>Package: Custom</p>
                                                        @else
                                                        <p>Package: {{ $item->package->title }}</p>
                                                        @endif
                                                        <p>Package Description: {{ $item->package->description }}</p>
                                                        <p>Freelancer Name: {{ $item->freelancer->name }}</p>
                                                        <p>Revision Left: {{ $item->revision }}</p>
                                                        <p>Delivery Days: {{ $item->package->delivery_days }}</p>
                                                        <p>Price: {{ $item->amount }}</p>
                                                        <p>Order Status: {{ $item->order_status }}</p>
                                                        <p>Payment Method: {{ $item->payment }}</p>
                                                        <p>Due Date: {{ $item->due_date }}</p>

                                                        @if ($item->onsite_date != null)
                                                            <p>Onsite Date: {{ $item->onsite_date }}</p>
                                                            <p>Address: {{ $item->address }}</p>
                                                        @endif

                                                        <a>Seller Name: 
                                                            <a href="{{ route('freelancer.profile', $item->freelancer->freelancer_id) }}">{{ $item->freelancer->name }}</a>

                                                        </a>                                                        </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- JavaScript to handle modal -->
                                        <script>
                                            document.getElementById("openModal{{ $item->order_id }}").addEventListener("click", function() {
                                                $('#exampleModal{{ $item->order_id }}').modal('show');
                                            });
                                        </script>
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
