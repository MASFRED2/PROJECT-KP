<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | POS KASIR FUZZA MART</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #021f3b !important; /* Abu sangat terang */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 400px;
        }

        .card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden;
        }

        /* Header Login dengan Warna Navy Gelap */
        .card-header {
            background-color: #1E293B !important;
            color: #FFFFFF !important;
            text-align: center;
            padding: 30px 20px !important;
            border-bottom: none !important;
        }

        .brand-text {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: #94A3B8;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Form Styling */
        .form-control {
            border-radius: 8px !important;
            height: 48px;
            border-color: #E2E8F0;
            color: #334155;
            padding-left: 15px;
        }

        .form-control:focus {
            border-color: #B69377 !important;
            box-shadow: 0 0 0 3px rgba(182, 147, 119, 0.15) !important;
        }

        .input-group-text {
            border-radius: 0 8px 8px 0 !important;
            background-color: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            color: #94A3B8 !important;
        }

        /* Tombol Utama Pastel Chocolate */
        .btn-primary {
            background-color: #2b5bdf !important;
            border-color: #B69377 !important;
            height: 48px;
            border-radius: 8px !important;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #A38363 !important;
            border-color: #A38363 !important;
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        /* Error Message Handling */
        .invalid-feedback {
            font-size: 0.8rem;
            font-weight: 500;
            color: #EF4444;
            margin-top: 5px;
            padding-left: 2px;
        }

        .icheck-primary label::before {
            border-radius: 4px !important;
        }
    </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="card card-outline">
        <div class="card-header">
            <div class="brand-text">POS <span style="color: #B69377;">KASIR FUZZA MART</span></div>
            <div class="brand-subtitle">Sistem Retail Fintech Modern</div>
        </div>
        
        <div class="card-body login-card-body p-4">
            <p class="login-box-msg text-muted small pb-3">Silakan masukkan kredensial akun Anda untuk membuka sistem.</p>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Pengguna" value="{{ old('email') }}" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="input-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kata Sandi" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row align-items-center mb-2">
                    <div class="col-7">
                        <div class="custom-control custom-checkbox small">
                            <input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label text-muted font-weight-normal" style="cursor:pointer;" for="remember">Ingat Saya</label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block">
                            Masuk <i class="fas fa-sign-in-alt ml-1" style="font-size: 0.9rem;"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>