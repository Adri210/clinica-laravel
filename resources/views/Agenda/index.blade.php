@extends('layouts.app')

@section('title', 'Agenda Médica')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
  
</head>
<body>
    
    <style>
        .agenda-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }
    
        #calendar {
            flex: 2;
            border: 1px solid #dee2e6;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    
        .event-panel {
            flex: 1;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            height: 100%;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    
        .event-panel h5 {
            color: #0d6efd;
            font-weight: bold;
        }
    
        .event-item {
            background-color: #fff;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
    
        .select-medico {
            margin-bottom: 20px;
        }
    </style>
    
    <div class="container mt-5">
        <h2 class="mb-4 text-primary">Agenda Médica</h2>
    
        <div class="select-medico">
            <label for="medico" class="form-label text-primary">Selecionar Médico:</label>
            <select id="medico" class="form-select form-select-sm w-50">
                <option value="1">Dr. João Andrade</option>
                <option value="2">Dra. Maria Lins</option>
                <option value="3">Dr. Pedro Costa</option>
            </select>
        </div>
    
        <div class="agenda-container">
            <!-- Calendário -->
            <div id="calendar"></div>
    
            <!-- Agendamentos do dia -->
            <div class="event-panel">
                <h5>Agendamentos do Dia</h5>
    
                <div class="event-item">
                    <strong>08:00</strong> - Ana Souza<br>
                    <small>Consulta Clínica Geral</small>
                </div>
    
                <div class="event-item">
                    <strong>09:30</strong> - Carlos Lima<br>
                    <small>Retorno Ortopedia</small>
                </div>
    
                <div class="event-item">
                    <strong>11:00</strong> - Fernanda Dias<br>
                    <small>Exame de Rotina</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inicializar o Bootstrap Calendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Certifique-se de que o jQuery e o Bootstrap Calendar foram carregados corretamente
            if (typeof $ !== 'undefined' && $.fn.calendar) {
                $('#calendar').calendar({
                    events: [
                        {
                            title: 'Ana Souza - Clínica Geral',
                            start: new Date().toISOString().split('T')[0] + 'T08:00:00',
                            color: '#0d6efd'
                        },
                        {
                            title: 'Carlos Lima - Ortopedia',
                            start: new Date().toISOString().split('T')[0] + 'T09:30:00',
                            color: '#ffc107'
                        },
                        {
                            title: 'Fernanda Dias - Rotina',
                            start: new Date().toISOString().split('T')[0] + 'T11:00:00',
                            color: '#198754'
                        }
                    ]
                });
            } else {
                console.error('O Bootstrap Calendar ou o jQuery não foram carregados corretamente.');
            }
        });
    </script>
    
    @endsection
</body>
</html>
