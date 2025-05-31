<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RealClin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .login-error {
            color: #842029;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            text-align: center;
        }

        .container-fluid {
            margin: 0;
            padding: 0;
        }

        .row {
            margin: 0;
            padding: 0;
        }

        .left-side {
            padding: 0;
            margin: 0;
        }

        .left-side img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            display: block;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .brand-title {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row g-0 h-100">

            <div class="col-md-6 d-none d-md-block left-side">
                <img src="{{ asset('images/clinica.jpg') }}" alt="Imagem da clínica">
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="login-container">
                    <div class="brand-title text-center mb-4">
                        RealClin <span style="color: gold;">★</span>
                    </div>
                    @if(session('login_error'))
                        <div class="login-error">
                            {{ session('login_error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Entrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>