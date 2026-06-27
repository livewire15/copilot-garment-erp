<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rishub Handicraft ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .sidebar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: calc(100vh - 76px);
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 10px 15px;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
        }
        .sidebar .nav-link.active {
            background-color: #e7f1ff;
            border-left-color: #0d6efd;
            color: #0d6efd;
            font-weight: 600;
        }
        .main-content {
            padding: 30px 20px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .badge-draft {
            background-color: #ffc107;
            color: #000;
        }
        .badge-balance_pending {
            background-color: #dc3545;
            color: #fff;
        }
        .badge-cleared {
            background-color: #28a745;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
        }
        .metric-card {
            text-align: center;
            padding: 20px;
        }
        .metric-card .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .metric-card .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-shop"></i> Rishub Handicraft ERP
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent cursor-pointer">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <nav class="nav flex-column mt-3">
                    <a class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                    <a class="nav-link {{ str_starts_with(Route::currentRouteName(), 'products') ? 'active' : '' }}" 
                       href="{{ route('products.index') }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                    <a class="nav-link {{ str_starts_with(Route::currentRouteName(), 'invoices') || str_starts_with(Route::currentRouteName(), 'payments') ? 'active' : '' }}" 
                       href="{{ route('invoices.index') }}">
                        <i class="bi bi-receipt"></i> Invoices
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
