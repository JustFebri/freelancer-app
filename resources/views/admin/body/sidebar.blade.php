<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            Freelancer<span>App</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#user" role="button" aria-expanded="false"
                    aria-controls="user">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">User</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="user">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('client') }}" class="nav-link">Client</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('freelancer') }}" class="nav-link">Freelancer</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('service') }}" class="nav-link">
                    <i class="link-icon" data-feather="book"></i>
                    <span class="link-title">Services</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('order') }}" class="nav-link">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Order</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('report') }}" class="nav-link">
                    <i class="link-icon" data-feather="flag"></i>
                    <span class="link-title">Report</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
