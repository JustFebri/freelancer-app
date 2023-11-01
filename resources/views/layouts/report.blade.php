@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">
        <div class="row chat-wrapper">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row position-relative">
                            <div class="col-lg-3 chat-aside border-end-lg">
                                <div class="aside-content">
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
                                    <div class="aside-body">
                                        <div class="tab-content mt-3">
                                            <div class="tab-pane  active ps ps--active-y" id="chats" role="tabpanel"
                                                aria-labelledby="chats-tab">
                                                <div>
                                                    <ul class="list-unstyled chat-list px-1">
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body fw-bolder">John Doe</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">4:32 PM</p>
                                                                        <div class="badge rounded-pill bg-primary ms-auto">
                                                                            5</div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status offline"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body fw-bolder">Carl Henson</p>
                                                                        <div class="d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                class="feather feather-image text-muted icon-md mb-2px">
                                                                                <rect x="3" y="3"
                                                                                    width="18" height="18"
                                                                                    rx="2" ry="2"></rect>
                                                                                <circle cx="8.5" cy="8.5"
                                                                                    r="1.5"></circle>
                                                                                <polyline points="21 15 16 10 5 21">
                                                                                </polyline>
                                                                            </svg>
                                                                            <p class="text-muted ms-1">Photo</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">05:24 PM</p>
                                                                        <div class="badge rounded-pill bg-danger ms-auto">3
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status offline"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">John Doe</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">Yesterday</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">Jensen Combs</p>
                                                                        <div class="d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                class="feather feather-video text-muted icon-md mb-2px">
                                                                                <polygon points="23 7 16 12 23 17 23 7">
                                                                                </polygon>
                                                                                <rect x="1" y="5"
                                                                                    width="15" height="14"
                                                                                    rx="2" ry="2"></rect>
                                                                            </svg>
                                                                            <p class="text-muted ms-1">Video</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">2 days ago</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status offline"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">Yaretzi Mayo</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">4 week ago</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status offline"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body fw-bolder">John Doe</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">4:32 PM</p>
                                                                        <div class="badge rounded-pill bg-primary ms-auto">
                                                                            5</div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body fw-bolder">Leonardo Payne</p>
                                                                        <div class="d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                class="feather feather-image text-muted icon-md mb-2px">
                                                                                <rect x="3" y="3"
                                                                                    width="18" height="18"
                                                                                    rx="2" ry="2"></rect>
                                                                                <circle cx="8.5" cy="8.5"
                                                                                    r="1.5"></circle>
                                                                                <polyline points="21 15 16 10 5 21">
                                                                                </polyline>
                                                                            </svg>
                                                                            <p class="text-muted ms-1">Photo</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">6:11 PM</p>
                                                                        <div class="badge rounded-pill bg-danger ms-auto">3
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">John Doe</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">Yesterday</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">Leonardo Payne</p>
                                                                        <div class="d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                class="feather feather-video text-muted icon-md mb-2px">
                                                                                <polygon points="23 7 16 12 23 17 23 7">
                                                                                </polygon>
                                                                                <rect x="1" y="5"
                                                                                    width="15" height="14"
                                                                                    rx="2" ry="2"></rect>
                                                                            </svg>
                                                                            <p class="text-muted ms-1">Video</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">2 days ago</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li class="chat-item pe-1">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="https://via.placeholder.com/37x37"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body">John Doe</p>
                                                                        <p class="text-muted tx-13">Hi, How are you?</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-muted tx-13 mb-1">4 week ago</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                                    <div class="ps__thumb-x" tabindex="0"
                                                        style="left: 0px; width: 0px;"></div>
                                                </div>
                                                <div class="ps__rail-y" style="top: 0px; height: 576px; right: 0px;">
                                                    <div class="ps__thumb-y" tabindex="0"
                                                        style="top: 0px; height: 482px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 chat-content border-end-lg">
                                <div class="chat-header border-bottom pb-2">
                                    <div class="d-flex justify-content-center">
                                        <h5>Error message on login</h3>
                                    </div>
                                </div>
                                <div class="chat-body ps ps--active-y" id="chatBody">
                                    <ul class="messages">
                                        <li class="message-item friend">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry.</p>
                                                    </div>
                                                    <span>8:12 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item me">
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
                                        </li>
                                        <li class="message-item friend">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry.</p>
                                                    </div>
                                                    <span>8:15 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item me">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry printing and typesetting industry.</p>
                                                    </div>
                                                    <span>8:15 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item friend">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry.</p>
                                                    </div>
                                                    <span>8:17 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item me">
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
                                                    <span>8:18 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item friend">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry.</p>
                                                    </div>
                                                    <span>8:22 PM</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="message-item me">
                                            <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                alt="avatar">
                                            <div class="content">
                                                <div class="message">
                                                    <div class="bubble">
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                                            industry printing and typesetting industry.</p>
                                                    </div>
                                                    <span>8:30 PM</span>
                                                </div>
                                            </div>
                                        </li>
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
                                    <img src="{{ !empty($profileData->picasset) ? asset($profileData->picasset) : url('backend/assets/images/no_image.jpg') }}"
                                        style="object-fit: cover;" class="wd-100 ht-100 rounded-circle" alt="profile">
                                    <div class="mt-3 mb-3">
                                        <label class="h4 fw-bolder text-capitalize">{{ $profileData->name }}</label>
                                        <p class="text-muted">{{ $profileData->email }}</p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mt-3 mb-3">Ticket Information</h6>
                                <hr>
                                <div class="mb-3 align-items-center" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Status: </label>
                                    <p style="margin-left: 0.5em;" class="text-muted">Open</p>
                                    <i class="link-arrow" data-feather="arrow-down-circle" style="width: 16; height:16; margin-left: 0.5em;"></i>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Created: </label>
                                    <p style="margin-left: 0.5em;" class="text-muted">June 12 2020</p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Updated: </label>
                                    <p style="margin-left: 0.5em;" class="text-muted">June 12 2020</p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Avg Response Time: </label>
                                    <p style="margin-left: 0.5em;" class="text-muted">2 hours 20 mins</p>
                                </div>
                                <div class="mb-3" style="display: flex">
                                    <label class="txt-11 fw-bolder text-capitalize">Ticket: </label>
                                    <p style="margin-left: 0.5em;" class="text-muted">23</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Get the chat body element
        const chatBody = document.getElementById('chatBody');

        // Scroll to the bottom of the chat body
        chatBody.scrollTop = chatBody.scrollHeight;
    </script>
@endsection
