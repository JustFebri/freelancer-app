@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">
        <div class="row chat-wrapper">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row position-relative">
                            <div class="col-lg-3 chat-aside border-end-lg">
                                <div class="aside-content" style="height: 100%; display: flex; flex-direction: column">
                                    <div class="aside-header">
                                        <div class="d-flex justify-content-between align-items-center pb-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $id = Auth::user()->admin_id;
                                                    $profileData = App\Models\admin::find($id);
                                                    if (!empty($profileData->picture_id)) {
                                                        $profileData = App\Models\admin::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                                                            ->select('picture.picasset', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                                                            ->find($id);
                                                    }
                                                @endphp
                                                <figure class="me-2 mb-0">
                                                    <img src="{{ !empty($profileData->picasset) ? asset($profileData->picasset) : url('backend/assets/images/no_image.jpg') }}"
                                                        style="object-fit: cover;" class="img-sm rounded-circle"
                                                        alt="profile">
                                                </figure>
                                                <div>
                                                    <h6>{{ $profileData->name }}</h6>
                                                    <p class="text-muted tx-13">Admin</p>
                                                </div>
                                            </div>
                                        </div>
                                        <form class="search-form">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-search cursor-pointer">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                        </line>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" id="searchForm"
                                                    placeholder="Search here...">
                                            </div>
                                        </form>
                                    </div>
                                    @if ($type->isEmpty())
                                        <div class="noticket"
                                            style="flex: 1;display:flex; justify-content: center; align-items: center; text-align: center;">
                                            <p>No ticket Yet</p>
                                        </div>
                                    @else
                                        <div class="aside-body">
                                            <div class="tab-content mt-1">
                                                <div class="tab-pane  active ps ps--active-y" id="chats" role="tabpanel"
                                                    aria-labelledby="chats-tab">
                                                    <div>
                                                        <div class="list-group">
                                                            @foreach ($type as $key => $item)
                                                                <a href="javascript:;"
                                                                    onclick="selectReport(this, {{ json_encode($item) }})"
                                                                    class="list-group-item">
                                                                    <div class="col" style="">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="col-md-auto me-2">
                                                                                <img src="{{ !empty($item->picasset) ? asset($item->picasset) : url('backend/assets/images/no_image.jpg') }}"
                                                                                    class="img-xs rounded-circle"
                                                                                    alt="user"
                                                                                    style="object-fit: cover;">
                                                                            </div>
                                                                            <div class="col" style="padding-left: 0;">
                                                                                <p class="text-body fw-bolder">
                                                                                    {{ $item->name }}
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-md-auto"
                                                                                style="padding-right: 0;">
                                                                                <p class="text-muted tx-13 mb-1">
                                                                                    {{ $item->updated_at }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <p class="text-muted tx-13"
                                                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                                    {{ $item->description }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        @if ($item->status == 'open')
                                                                            <div
                                                                                class="badge rounded-pill text-capitalize open mt-1">
                                                                                {{ $item->status }}
                                                                            </div>
                                                                        @elseif($item->status == 'closed')
                                                                            <div
                                                                                class="badge rounded-pill text-capitalize closed mt-1">
                                                                                {{ $item->status }}
                                                                            </div>
                                                                        @else
                                                                            <div
                                                                                class="badge rounded-pill text-capitalize in-progress mt-1">
                                                                                {{ $item->status }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                                        <div class="ps__thumb-x" tabindex="0"
                                                            style="left: 0px; width: 0px;"></div>
                                                    </div>
                                                    <div class="ps__rail-y" style="top: 0px; height: auto; right: 0px;">
                                                        <div class="ps__thumb-y" tabindex="0"
                                                            style="top: 0px; height: auto;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 chat-content border-end-lg">
                                <div class="chat-header border-bottom pb-2">
                                    <div class="d-flex justify-content-center">
                                        <h5 id="title-report">Lorep Ipsum</h3>
                                    </div>
                                </div>
                                <div class="chat-body ps ps--active-y">
                                    <ul class="messages">
                                        <li class="message-item friend">
                                            <img style="object-fit: cover;" id="report-avatar"
                                                src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p id="ticket-desc">Lorem Ipsum is simply dummy text of the printing
                                                            and typesetting
                                                            industry.</p>
                                                    </div>
                                                    <span>8:12 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        {{-- <li class="message-item me">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry printing and typesetting industry.</p>
                                                    </div>
                                                </div>
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum.</p>
                                                    </div>
                                                    <span>8:13 PM</span>
                                                </div>
                                            </div>
                                        </li> --}}
                                    </ul>
                                    <div class="ps__rail-x" style="left: 0px; bottom: -85px;">
                                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                    </div>
                                    <div class="ps__rail-y" style="top: 85px; height: 621px; right: 0px;">
                                        <div class="ps__thumb-y" tabindex="0" style="top: 75px; height: 546px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-footer d-flex">
                                    <form class="search-form flex-grow-1 me-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-pill" id="chatForm"
                                                placeholder="Type a message">
                                        </div>
                                    </form>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-icon rounded-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-send">
                                                <line x1="22" y1="2" x2="11" y2="13">
                                                </line>
                                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 ticket-content">
                                <div style="text-align: center">
                                    <img src="{{ url('backend/assets/images/no_image.jpg') }}" style="object-fit: cover;"
                                        class="wd-100 ht-100 rounded-circle img-thumbnail" alt="profile"
                                        id="ticket_avatar">
                                    <div class="mt-3 mb-3">
                                        <label id="ticket_name" class="h4 fw-bolder text-capitalize text-capitalize">Lorem
                                            Ipsum</label>
                                        <p id="ticket_email" class="text-muted">Lorem@gmail.com</p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mt-3 mb-3">Ticket Information</h6>
                                <hr>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Ticket ID: </label>
                                    <p id="ticket_id" style="margin-left: 0.5em;" class="text-muted">Null</p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Report Type: </label>
                                    <p id="ticket_type" style="margin-left: 0.5em;" class="text-muted">Null</p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Order ID: </label>
                                    <p id="ticket_order" style="margin-left: 0.5em;" class="text-muted">Null</p>
                                </div>
                                <div class="mb-3 align-items-center" style="display: flex">
                                    <label class="txt-11 fw-bolder me-2">Status: </label>
                                    <div class="dropdown">
                                        <button aria-labelledby="dropdownMenuButton"
                                            class="btn btn-secondary btn-sm dropdown-toggle text-capitalize"
                                            type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Null
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" onclick="changeStatus('open')"
                                                href="#">Open</a>
                                            <a class="dropdown-item" onclick="changeStatus('in progress')"
                                                href="#">In Progress</a>
                                            <a class="dropdown-item" onclick="changeStatus('closed')"
                                                href="#">Closed</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Created: </label>
                                    <p id="ticket_created" style="margin-left: 0.5em;" class="text-muted">Null
                                    </p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Updated: </label>
                                    <p id="ticket_updated" style="margin-left: 0.5em;" class="text-muted">Null
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function nl2br(str) {
            return str.replace(/\n/g, '<br>');
        }

        function selectReport(element, item) {
            var avatar = document.getElementById("ticket_avatar");
            var reAvatar = document.getElementById("report-avatar");
            var id = document.getElementById("ticket_id");
            var status = document.getElementById("ticket_status");
            var created_at = document.getElementById("ticket_created");
            var updated_at = document.getElementById("ticket_updated");
            var order_id = document.getElementById("ticket_order");
            var type = document.getElementById("ticket_type");
            var name = document.getElementById("ticket_name");
            var email = document.getElementById("ticket_email");
            var title = document.getElementById("title-report");
            var desc = document.getElementById("ticket-desc");

            $('.list-group-item').removeClass('active');
            $(element).addClass('active');
            console.log(item);
            if (item.picasset === null || item.picasset === undefined) {
                avatar.src = "{{ url('backend/assets/images/no_image.jpg') }}";
            } else {
                avatar.src = item.picasset;
                reAvatar.src = item.picasset;
            }
            if (item.order_id === null || item.order_id === undefined) {
                order_id.textContent = 'Null';
            } else {
                order_id.textContent = item.order_id;
            }
            id.textContent = item.report_id;
            created_at.textContent = item.created_at;
            updated_at.textContent = item.updated_at;
            type.textContent = item.report_type;
            title.textContent = item.report_type;
            name.textContent = item.name;
            email.textContent = item.email;
            // desc.textContent = item.description;
            desc.innerHTML = nl2br(item.description);

            $('#dropdownMenuButton').text(item.status);
        }

        function changeStatus(value) {
            $('#dropdownMenuButton').text(value);

            var ticketId = document.getElementById('ticket_id').textContent.trim();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'PUT',
                url: "{{ route('report.status', ['report_id' => ':ticketId', 'status' => ':status']) }}".replace(
                    ':ticketId', encodeURIComponent(ticketId)).replace(':status', encodeURIComponent(value)),
                success: function(response) {
                    window.location.href = "{{ route('report') }}";
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
@endsection
