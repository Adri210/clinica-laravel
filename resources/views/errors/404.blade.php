<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página não encontrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }
        h1 {
            color: #dc2626;
            font-size: 4rem;
            margin: 0;
        }
        h2 {
            color: #1f2937;
            margin: 1rem 0;
        }
        p {
            color: #4b5563;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            margin: 0 5px;
        }
        .btn:hover {
            background-color: #2563eb;
        }
        .btn-secondary {
            background-color: #6b7280;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Página não encontrada</h2>
        <p>Desculpe, a página que você está procurando não existe ou foi movida.</p>
        <div>
            <a href="javascript:history.back()" class="btn">Voltar</a>
            <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>
</body>
</html>