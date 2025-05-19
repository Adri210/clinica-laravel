@extends('layouts.app')

@section('title', 'Agenda Médica')

@section('content')
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
</style>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Agenda Médica</h2>

    <div class="select-medico mb-4">
        <label for="medico" class="form-label text-primary">Selecionar Médico:</label>
        <select id="medico" class="form-select form-select-sm w-50">
            <option value="">Todos os Médicos</option>
            @foreach($medicos as $medico)
                <option value="{{ $medico->id }}">{{ $medico->nome_completo }} - {{ $medico->especialidade }}</option>
            @endforeach
        </select>
    </div>

    <div class="agenda-container">
        <div id="calendar"></div>
        
        <div class="event-panel">
            <h5>Agendamentos do Dia</h5>
            <div id="eventos-do-dia"></div>
        </div>
    </div>
</div>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const medicoSelect = document.getElementById('medico');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 600,
            events: function(fetchInfo, successCallback, failureCallback) {
                const medicoId = medicoSelect.value;
                let url = '/api/eventos';
                
                if(medicoId) {
                    url += `?medico_id=${medicoId}`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map(event => ({
                            title: `${event.paciente} - ${event.especialidade}`,
                            start: event.data_hora,
                            extendedProps: {
                                medico: event.medico.nome_completo
                            },
                            color: getEventColor(event.especialidade)
                        }));
                        successCallback(events);
                    })
                    .catch(error => {
                        failureCallback(error);
                    });
            },
            dateClick: function(info) {
                loadEventosDoDia(info.dateStr);
            }
        });

        calendar.render();
        
        // Carrega eventos do dia atual inicialmente
        loadEventosDoDia(new Date().toISOString().split('T')[0]);
        
        // Filtra ao mudar médico
        medicoSelect.addEventListener('change', function() {
            calendar.refetchEvents();
            loadEventosDoDia(calendar.getDate().toISOString().split('T')[0]);
        });
        
        function loadEventosDoDia(dateStr) {
            const medicoId = medicoSelect.value;
            let url = `/api/eventos/do-dia?date=${dateStr}`;
            
            if(medicoId) {
                url += `&medico_id=${medicoId}`;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const eventosHtml = data.map(event => `
                        <div class="event-item mb-3 p-3 bg-white rounded shadow-sm">
                            <strong>${formatTime(event.data_hora)}</strong> - ${event.paciente}<br>
                            <small>${event.especialidade} com ${event.medico.nome_completo}</small>
                        </div>
                    `).join('');
                    
                    document.getElementById('eventos-do-dia').innerHTML = eventosHtml || 
                        '<div class="text-muted">Nenhum agendamento para este dia</div>';
                });
        }
        
        function formatTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        
        function getEventColor(especialidade) {
            const colors = {
                'Clínica Geral': '#0d6efd',
                'Ortopedia': '#ffc107',
                'Cardiologia': '#dc3545',
                'Pediatria': '#198754',
                'Dermatologia': '#6f42c1'
            };
            
            return colors[especialidade] || '#6c757d';
        }
    });
</script>
@endsection