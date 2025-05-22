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
    
    .fc-event {
        cursor: pointer;
    }
    
    .event-item {
        transition: all 0.2s;
    }
    
    .event-item:hover {
        transform: translateX(5px);
    }
</style>

<!-- Modal para adicionar/editar -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Adicionar Consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                @csrf
                <input type="hidden" id="eventId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="paciente" class="form-label">Paciente</label>
                        <input type="text" class="form-control" id="paciente" name="paciente" required>
                        <div class="invalid-feedback">Por favor, informe o nome do paciente.</div>
                    </div>
                    <div class="mb-3">
                        <label for="medico_id" class="form-label">Médico</label>
                        <select class="form-select" id="medico_id" name="medico_id" required>
                            <option value="">Selecione um médico</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}">{{ $medico->nome_completo }} - {{ $medico->especialidade }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Por favor, selecione um médico.</div>
                    </div>
                    <div class="mb-3">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <input type="text" class="form-control" id="especialidade" name="especialidade" required>
                        <div class="invalid-feedback">Por favor, informe a especialidade.</div>
                    </div>
                    <div class="mb-3">
                        <label for="data_hora" class="form-label">Data e Hora</label>
                        <input type="datetime-local" class="form-control" id="data_hora" name="data_hora" required>
                        <div class="invalid-feedback">Por favor, informe uma data e hora válidas (não pode ser no passado).</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmação para exclusão -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar esta consulta?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Agenda Médica</h2>

    <div class="d-flex justify-content-between mb-4">
        <div class="select-medico w-50">
            <label for="medico" class="form-label text-primary">Filtrar por Médico:</label>
            <select id="medico" class="form-select">
                <option value="">Todos os Médicos</option>
                @foreach($medicos as $medico)
                    <option value="{{ $medico->id }}">{{ $medico->nome_completo }} - {{ $medico->especialidade }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary align-self-end" id="addEventBtn">
            <i class="bi bi-plus-circle"></i> Nova Consulta
        </button>
    </div>

    <div class="agenda-container">
        <div id="calendar"></div>
        
        <div class="event-panel">
            <h5>Agendamentos do Dia</h5>
            <div id="eventos-do-dia"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const medicoSelect = document.getElementById('medico');
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const eventForm = document.getElementById('eventForm');
        const modalTitle = document.getElementById('modalTitle');
        const addEventBtn = document.getElementById('addEventBtn');
        
        let currentEventId = null;
        let deleteCallback = null;
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                const medicoId = medicoSelect.value;
                
                fetch(`/agenda/events?medico_id=${medicoId}&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map(event => ({
                            id: event.id,
                            title: `${event.paciente} - ${event.especialidade}`,
                            start: event.data_hora,
                            extendedProps: {
                                medico: event.medico.nome_completo,
                                medico_id: event.medico_id,
                                especialidade: event.especialidade
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
            },
            eventClick: function(info) {
                openEditModal(info.event);
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
        
        // Botão para adicionar novo evento
        addEventBtn.addEventListener('click', function() {
            openAddModal();
        });
        
        // Formulário de evento
        eventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(eventForm);
            const url = currentEventId ? `/agenda/${currentEventId}` : '/agenda';
            const method = currentEventId ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    calendar.refetchEvents();
                    loadEventosDoDia(calendar.getDate().toISOString().split('T')[0]);
                    eventModal.hide();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Ocorreu um erro ao processar sua solicitação.');
            });
        });
        
        // Confirmação de exclusão
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deleteCallback) {
                deleteCallback();
            }
            confirmModal.hide();
        });
        
        function loadEventosDoDia(dateStr) {
            const medicoId = medicoSelect.value;
            let url = `/agenda/events?date=${dateStr}`;
            
            if(medicoId) {
                url += `&medico_id=${medicoId}`;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const eventosHtml = data.map(event => `
                        <div class="event-item mb-3 p-3 bg-white rounded shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${formatTime(event.data_hora)}</strong> - ${event.paciente}<br>
                                <small>${event.especialidade} com ${event.medico.nome_completo}</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary edit-event" data-id="${event.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-event" data-id="${event.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('eventos-do-dia').innerHTML = eventosHtml || 
                        '<div class="text-muted">Nenhum agendamento para este dia</div>';
                    
                    // Adiciona eventos aos botões de edição/exclusão
                    document.querySelectorAll('.edit-event').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const eventId = this.getAttribute('data-id');
                            fetchEvent(eventId).then(event => {
                                openEditModal(event);
                            });
                        });
                    });
                    
                    document.querySelectorAll('.delete-event').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const eventId = this.getAttribute('data-id');
                            openDeleteModal(eventId);
                        });
                    });
                });
        }
        
        function fetchEvent(eventId) {
            return fetch(`/agenda/${eventId}`)
                .then(response => response.json())
                .then(data => {
                    return {
                        id: data.id,
                        title: `${data.paciente} - ${data.especialidade}`,
                        start: data.data_hora,
                        extendedProps: {
                            paciente: data.paciente,
                            medico_id: data.medico_id,
                            especialidade: data.especialidade
                        }
                    };
                });
        }
        
        function openAddModal() {
            currentEventId = null;
            modalTitle.textContent = 'Adicionar Consulta';
            eventForm.reset();
            
            // Define a data/hora mínima como agora
            const now = new Date();
            const timezoneOffset = now.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);
            document.getElementById('data_hora').min = localISOTime;
            
            eventModal.show();
        }
        
        function openEditModal(event) {
            currentEventId = event.id;
            modalTitle.textContent = 'Editar Consulta';
            
            // Preenche o formulário
            document.getElementById('eventId').value = event.id;
            document.getElementById('paciente').value = event.extendedProps.paciente || event.title.split(' - ')[0];
            document.getElementById('medico_id').value = event.extendedProps.medico_id;
            document.getElementById('especialidade').value = event.extendedProps.especialidade || event.title.split(' - ')[1];
            
            // Formata a data/hora para o input
            const eventDate = new Date(event.start);
            const timezoneOffset = eventDate.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(eventDate - timezoneOffset)).toISOString().slice(0, 16);
            document.getElementById('data_hora').value = localISOTime;
            
            // Define a data/hora mínima como agora
            const now = new Date();
            const nowISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);
            document.getElementById('data_hora').min = nowISOTime;
            
            eventModal.show();
        }
        
        function openDeleteModal(eventId) {
            deleteCallback = function() {
                fetch(`/agenda/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        calendar.refetchEvents();
                        loadEventosDoDia(calendar.getDate().toISOString().split('T')[0]);
                    } else {
                        showAlert('danger', data.message);
                    }
                });
            };
            
            confirmModal.show();
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
        
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '1100';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 3000);
        }
    });
</script>
@endsection 