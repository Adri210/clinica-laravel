@extends('layouts.app')

@section('title', 'Agenda Médica')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>

<style>
    .agenda-container {
        display: flex;
        flex-direction: row;
        gap: 20px;
    }

    #calendar {
        flex: 2;
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 15px;
    }

    .event-panel {
        flex: 1;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 15px;
        overflow-y: auto;
        max-height: 700px;
    }

    .fc-event {
        cursor: pointer;
        margin-bottom: 2px;
    }
</style>

<div class="container mt-5">
    <div class="mb-3">
        <label for="medico" class="form-label text-primary">Selecionar Médico:</label>
        <select id="medico" class="form-select form-select-sm w-50">
            @foreach($medicos as $medico)
                <option value="{{ $medico->id }}">{{ $medico->nome }} {{ $medico->sobrenome }} - {{ $medico->especialidade }}</option>
            @endforeach
        </select>
    </div>

    <div class="agenda-container">
        <div id="calendar"></div>

        <div class="event-panel">
            <h5>Agendamentos do Dia</h5>
            <div id="event-list">
                <p class="text-muted">Selecione um médico e clique em uma data no calendário para agendar.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="agendaModal" tabindex="-1" aria-labelledby="agendaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agendaModalLabel">Agendar Consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="agendaForm">
                <div class="modal-body">
                    <input type="hidden" id="agenda_id" name="agenda_id">
                    <div class="mb-3">
                        <label for="paciente" class="form-label">Paciente*</label>
                        <input type="text" class="form-control" id="paciente" name="paciente" required>
                    </div>
                    {{-- REMOVED THE "ESPECIALIDADE" DROPDOWN FROM HERE --}}
                    {{-- Instead, we'll use a hidden input or just rely on JS to send the value --}}
                    <input type="hidden" id="especialidade" name="especialidade"> {{-- HIDDEN FIELD FOR SPECIALTY --}}
                    <div class="mb-3">
                        <label for="data_hora" class="form-label">Data e Hora*</label>
                        <input type="datetime-local" class="form-control" id="data_hora" name="data_hora" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-danger" id="deleteEventButton" style="display: none;">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const medicoSelect = document.getElementById('medico');
    const calendarEl = document.getElementById('calendar');
    const modal = new bootstrap.Modal(document.getElementById('agendaModal'));
    const form = document.getElementById('agendaForm');
    const eventList = document.getElementById('event-list');
    const deleteButton = document.getElementById('deleteEventButton');
    // Get the hidden specialty input
    const especialidadeInput = document.getElementById('especialidade'); 
    
    let calendar;
    let currentEventId = null; 

    function initCalendar() {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            editable: true,
            timeZone: 'local',
            events: function(fetchInfo, successCallback, failureCallback) {
                const medicoId = medicoSelect.value;
                if (!medicoId) {
                    console.log('Nenhum médico selecionado');
                    return successCallback([]);
                }
                
                const url = new URL('/agenda/events', window.location.origin);
                url.searchParams.append('start', fetchInfo.start.toISOString());
                url.searchParams.append('end', fetchInfo.end.toISOString());
                url.searchParams.append('medico_id', medicoId);
                
                console.log('Buscando eventos para:', {
                    medicoId,
                    start: fetchInfo.start,
                    end: fetchInfo.end,
                    url: url.toString()
                });
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erro HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Eventos recebidos:', data);
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Erro ao buscar eventos:', error);
                        failureCallback(error);
                        alert('Erro ao carregar eventos. Verifique o console para detalhes.');
                    });
            },
            dateClick: function(info) {
                const medicoId = medicoSelect.value;
                if (!medicoId) {
                    alert('Por favor, selecione um médico primeiro.');
                    return;
                }
                
                form.reset();
                document.getElementById('agenda_id').value = '';
                deleteButton.style.display = 'none'; 
                currentEventId = null; 
                
                const clickedDate = info.date;
                const year = clickedDate.getFullYear();
                const month = String(clickedDate.getMonth() + 1).padStart(2, '0');
                const day = String(clickedDate.getDate()).padStart(2, '0');
                const hours = String(clickedDate.getHours()).padStart(2, '0');
                const minutes = String(clickedDate.getMinutes()).padStart(2, '0');
                
                document.getElementById('data_hora').value = `${year}-${month}-${day}T${hours}:${minutes}`;
                
                // --- CÓDIGO PARA PREENCHER A ESPECIALIDADE NO CAMPO ESCONDIDO ---
                const selectedMedicoOption = medicoSelect.options[medicoSelect.selectedIndex];
                const medicoSpecialtyText = selectedMedicoOption.text;
                // A regex assume que a especialidade vem após " - "
                const specialtyMatch = medicoSpecialtyText.match(/ - (.*)$/); 

                if (specialtyMatch && specialtyMatch[1]) {
                    especialidadeInput.value = specialtyMatch[1]; // Set value to hidden input
                } else {
                    especialidadeInput.value = ''; // Clear if no specialty found
                }
                // --- FIM DO CÓDIGO ---

                modal.show();
            },
            eventClick: function(info) {
                const event = info.event;
                currentEventId = event.id; 
                
                document.getElementById('agenda_id').value = event.id;
                document.getElementById('paciente').value = event.extendedProps.paciente;
                especialidadeInput.value = event.extendedProps.especialidade; // Preenche o input escondido ao editar
                
                const eventDate = new Date(event.start);
                const year = eventDate.getFullYear();
                const month = String(eventDate.getMonth() + 1).padStart(2, '0');
                const day = String(eventDate.getDate()).padStart(2, '0');
                const hours = String(eventDate.getHours()).padStart(2, '0');
                const minutes = String(eventDate.getMinutes()).padStart(2, '0');
                
                document.getElementById('data_hora').value = `${year}-${month}-${day}T${hours}:${minutes}`;
                deleteButton.style.display = 'inline-block'; 
                modal.show();
            },
            eventDidMount: function(info) {
                info.el.setAttribute('title', 
                    `Paciente: ${info.event.extendedProps.paciente}\n` +
                    `Especialidade: ${info.event.extendedProps.especialidade}\n` +
                    `Data: ${info.event.start.toLocaleString('pt-BR')}`
                );
            },
            eventDrop: function(info) {
                // Formatando a data para o formato esperado pelo backend
                const eventDate = new Date(info.event.start);
                const year = eventDate.getFullYear();
                const month = String(eventDate.getMonth() + 1).padStart(2, '0');
                const day = String(eventDate.getDate()).padStart(2, '0');
                const hours = String(eventDate.getHours()).padStart(2, '0');
                const minutes = String(eventDate.getMinutes()).padStart(2, '0');
                
                const dateStr = `${year}-${month}-${day}T${hours}:${minutes}`;
                
                updateEvent({
                    id: info.event.id,
                    paciente: info.event.extendedProps.paciente,
                    especialidade: info.event.extendedProps.especialidade, // Envia a especialidade ao mover
                    medico_id: info.event.extendedProps.medico_id,
                    data_hora: dateStr
                });
            },
            loading: function(isLoading) {
                if (isLoading) {
                    console.log('Carregando eventos...');
                } else {
                    console.log('Eventos carregados');
                }
            }
        });
        
        calendar.render();
        updateDayEvents(); // Carrega os eventos do dia inicial
    }
    
    function saveEvent(eventData) {
        const url = eventData.id ? `/agenda/${eventData.id}` : '/agenda';
        const method = eventData.id ? 'PUT' : 'POST';
        
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ...eventData,
                medico_id: medicoSelect.value
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        });
    }
    
    function updateEvent(eventData) {
        return saveEvent(eventData)
            .then(data => {
                if (!data.success) {
                    calendar.refetchEvents(); // Reverte a UI se a atualização falhar
                    throw new Error(data.message || 'Erro ao atualizar evento');
                }
                return data;
            });
    }
    
    function deleteEvent(eventId) {
        if (confirm(`Deseja realmente excluir a consulta?`)) {
            fetch(`/agenda/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    modal.hide(); 
                    calendar.refetchEvents();
                    updateDayEvents();
                    alert('Consulta cancelada com sucesso!');
                } else {
                    throw new Error(data.message || 'Erro ao excluir evento');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao cancelar consulta: ' + error.message);
            });
        }
    }
    
    function updateDayEvents() {
        const today = new Date();
        const start = new Date(today.setHours(0, 0, 0, 0));
        const end = new Date(today.setHours(23, 59, 59, 999));
        
        const medicoId = medicoSelect.value;
        if (!medicoId) {
            eventList.innerHTML = '<p class="text-muted">Selecione um médico para ver os agendamentos.</p>';
            return;
        }
        
        const url = new URL('/agenda/events', window.location.origin);
        url.searchParams.append('start', start.toISOString());
        url.searchParams.append('end', end.toISOString());
        url.searchParams.append('medico_id', medicoId);
        
        console.log('Buscando eventos do dia:', url.toString());
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(events => {
                console.log('Eventos do dia recebidos:', events);
                if (events.length === 0) {
                    eventList.innerHTML = '<p class="text-muted">Nenhum agendamento para hoje.</p>';
                    return;
                }
                
                let html = '<div class="list-group">';
                events.forEach(event => {
                    const date = new Date(event.start);
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${event.extendedProps.paciente}</h6>
                                <small>${date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'})}</small>
                            </div>
                            <p class="mb-1">${event.extendedProps.especialidade}</p>
                            <small class="text-muted">Dr. ${event.extendedProps.medico_nome || ''}</small>
                        </div>
                    `;
                });
                html += '</div>';
                eventList.innerHTML = html;
            })
            .catch(error => {
                console.error('Erro ao buscar eventos do dia:', error);
                eventList.innerHTML = '<p class="text-danger">Erro ao carregar agendamentos.</p>';
            });
    }
    
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const eventData = {
            paciente: document.getElementById('paciente').value,
            // Get specialty from the hidden input field
            especialidade: especialidadeInput.value, 
            medico_id: medicoSelect.value,
            data_hora: document.getElementById('data_hora').value
        };
        
        const eventId = document.getElementById('agenda_id').value;
        if (eventId) {
            eventData.id = eventId;
        }
        
        saveEvent(eventData)
            .then(data => {
                if (data.success) {
                    modal.hide();
                    calendar.refetchEvents();
                    updateDayEvents();
                    
                    if (eventId) {
                        alert('Consulta atualizada com sucesso!');
                    } else {
                        alert('Consulta agendada com sucesso!');
                    }
                } else {
                    throw new Error(data.message || 'Erro ao salvar consulta');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar consulta: ' + error.message);
            });
    });

    
    deleteButton.addEventListener('click', function() {
        if (currentEventId) {
            deleteEvent(currentEventId);
        } else {
            alert('Nenhum evento selecionado para excluir.');
        }
    });
    

    medicoSelect.addEventListener('change', function() {
        console.log('Médico alterado para:', this.value);
        calendar.refetchEvents();
        updateDayEvents();
    });
    
    // Inicializa o calendário
    initCalendar();
});
</script>
@endsection