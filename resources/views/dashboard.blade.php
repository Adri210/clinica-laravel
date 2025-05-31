<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - RealClin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap e FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            background-color: #f1f1f1;
            justify-content: center; 
            align-items: center;     
            transition: margin-left 0.3s ease;
        }

        .sidebar {
            width: 250px;
            background-color: #003366; 
            padding: 1rem;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h4 {
            font-weight: bold;
            color: #FFD700; 
            display: flex;
            justify-content: space-between; 
            align-items: center;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .sidebar .nav-link:hover {
            background-color: #FFD700; 
            border-radius: 5px;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .sidebar-title {
            font-weight: bold;
            margin-top: 1.5rem;
            color: #FFD700; 
        }

        .sidebar .nav-link.active {
            background-color: #FFD700; 
            color: #003366; 
        }

        .toggle-sidebar {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            border: 2px solid white;
        }

        .toggle-sidebar i {
            color: white; 
        }

        .toggle-sidebar:hover {
            background-color: #FFD700;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 1rem;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed h4 span {
            display: none;
        }

        .sidebar.collapsed .sidebar-title, 
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .content.collapsed {
            margin-left: 60px;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            margin-left: 250px;
            padding: 20px;
            text-align: center;
            height: 100vh; 
        }

        .content img {
            max-width: 80%;  
            height: auto;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%; 
            }
            .toggle-sidebar {
                top: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <h4>
            <span>RealClin<span style="color: gold;">★</span></span>
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
        </h4>

        @if(Auth::user()->tipo_usuario === 'admin')
            <div class="sidebar-title">Usuários</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('usuarios.create') }}">
                        <i class="fas fa-user-plus"></i> <span>Cadastrar Usuário</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('usuarios.index') }}">
                        <i class="fas fa-users"></i> <span>Mostrar Usuários</span>
                    </a>
                </li>
            </ul>
        @endif

        {{-- Agenda para admin e recepcionista --}}
        @if(Auth::user()->tipo_usuario === 'admin' || Auth::user()->tipo_usuario === 'recepcionista')
            <div class="sidebar-title">Agenda</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('agenda.index') }}">
                        <i class="fas fa-calendar-alt"></i> <span>Ver Agenda</span>
                    </a>
                </li>
            </ul>
        @endif

        {{-- Apenas para admin --}}
        @if(Auth::user()->tipo_usuario === 'admin')
            <div class="sidebar-title">Médicos</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medicos.create') }}">
                        <i class="fas fa-user-md"></i> <span>Cadastrar Médico</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medicos.index') }}">
                        <i class="fas fa-stethoscope"></i> <span>Mostrar Médicos</span>
                    </a>
                </li>
            </ul>
        @endif

        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="color: white; background: none; border: none;">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div class="content" id="content">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Realclin">
    </div>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });
    </script>

</body>
</html>
