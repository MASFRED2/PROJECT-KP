<!DOCTYPE html>
<html lang="en">
<head>
    <style>
    /* Tipografi Modern */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: #F8FAFC !important;
        color: #334155;
    }

    /* Navbar & Sidebar Dark Blue */
    .main-header, .main-sidebar {
        background-color: #1E293B !important;
        border: none !important;
    }
    
    .brand-link, .nav-link p, .nav-header, .user-panel .info a {
        color: #F8FAFC !important;
    }

    /* Menu Aktif Pastel Chocolate */
    .nav-pills .nav-link.active {
        background-color: #B69377 !important; /* Pastel Chocolate */
        color: #FFFFFF !important;
    }

    /* Card & Container */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    /* Button Style */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-primary { background-color: #B69377; border: none; }
    .btn-primary:hover { background-color: #A38363; }
    
    .btn-warning { background-color: #F59E0B; border: none; color: white; }
    .btn-warning:hover { background-color: #D97706; color: white; }

    /* Tabel Minimalis */
    .table thead th {
        background-color: #1E293B;
        color: #FFFFFF;
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .table tbody tr:hover {
        background-color: #FDF8F3; /* Pastel Chocolate sangat muda */
    }
</style>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FUZZA MART</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  @include('layouts.navbar')

  @include('layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>@yield('judul')</h1>
      </div>
    </section>

    <section class="content">
      @yield('isi')
    </section>
  </div>

  @include('layouts.footer')
</div>

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 (AdminLTE 3 pake BS4, tapi kita bisa timpa dengan BS5 nanti) -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

@stack('scripts')
</body>
</html>