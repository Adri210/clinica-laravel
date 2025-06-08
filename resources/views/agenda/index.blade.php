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

    .medico-filter {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
</style>

<div class="container mt-5">
    <div class="medico-filter">
        <label for="view-all" class="form-check-label">Visualizar todos os médicos:</label>
        <input type="checkbox" id="view-all" class="form-check-input">

        <div id="medico-container">
            <label for="medico" class="form-label text-primary ms-3">Selecionar Médico:</label>
            <select id="medico" class="form-select form-select-sm w-100">
                @foreach($medicos as $medico)
                    <option value="{{ $medico->id }}">{{ $medico->nome }} {{ $medico->sobrenome }} - {{ $medico->especialidade }}</option>
                @endforeach
            </select>
        </div>
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
                        <input type="text" class="form-control" id="paciente" name="paciente" required maxlength="100">
                        <small class="form-text text-muted">
                            <span id="pacienteCharCount">0</span>/100 caracteres
                        </small>
                    </div>
                    <div class="mb-3" id="modal-medico-selection">
                        <label for="modal_medico_id" class="form-label">Médico*</label>
                        <select id="modal_medico_id" name="medico_id" class="form-select" required>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}">{{ $medico->nome }} {{ $medico->sobrenome }} - {{ $medico->especialidade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="especialidade" name="especialidade">
                    <div class="mb-3">
                        <label for="data_hora" class="form-label">Data e Hora*</label>
                        <input type="datetime-local" class="form-control" id="data_hora" name="data_hora" required min="{{ date('Y-m-d\TH:i') }}">
                        <div class="invalid-feedback" id="time-validation-feedback" style="display: none;">
                            Agendamentos só podem ser feitos entre 07:00 e 22:00.
                        </div>
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
    const viewAllCheckbox = document.getElementById('view-all');
    const medicoContainer = document.getElementById('medico-container');
    const medicoSelect = document.getElementById('medico');
    const calendarEl = document.getElementById('calendar');
    const modal = new bootstrap.Modal(document.getElementById('agendaModal'));
    const form = document.getElementById('agendaForm');
    const eventList = document.getElementById('event-list');
    const deleteButton = document.getElementById('deleteEventButton');
    const especialidadeInput = document.getElementById('especialidade');
    const dataHoraInput = document.getElementById('data_hora');
    const pacienteInput = document.getElementById('paciente'); 
    const pacienteCharCount = document.getElementById('pacienteCharCount');
    const modalMedicoIdSelect = document.getElementById('modal_medico_id');
    const timeValidationFeedback = document.getElementById('time-validation-feedback'); 

    let calendar;
    let currentEventId = null;

    const localeSettings = {
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        dayNames: ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'],
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
        today: 'Hoje',
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        allDayText: 'Dia todo',
        noEventsText: 'Nenhum evento para mostrar',
        moreLinkText: 'mais',
    };

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
            firstDay: 0,
            buttonText: localeSettings.buttonText,
            monthNames: localeSettings.monthNames,
            monthNamesShort: localeSettings.monthNamesShort,
            dayNames: localeSettings.dayNames,
            dayNamesShort: localeSettings.dayNamesShort,
            allDayText: localeSettings.allDayText,
            noEventsText: localeSettings.noEventsText,
            moreLinkText: localeSettings.moreLinkText,
            events: function(fetchInfo, successCallback, failureCallback) {
                eventList.innerHTML = '<p class="text-muted">Carregando eventos...</p>';

                const url = new URL('/agenda/events', window.location.origin);
                url.searchParams.append('start', fetchInfo.start.toISOString());
                url.searchParams.append('end', fetchInfo.end.toISOString());

                if (!viewAllCheckbox.checked && medicoSelect.value) {
                    url.searchParams.append('medico_id', medicoSelect.value);
                }

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erro HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        successCallback(data);
                        updateDayEvents();
                    })
                    .catch(error => {
                        console.error('Erro ao buscar eventos:', error);
                        eventList.innerHTML = '<p class="text-danger">Erro ao carregar eventos.</p>';
                        failureCallback(error);
                    });
            },
            dateClick: function(info) {
                const clickedDate = info.date;
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (clickedDate < today) {
                    alert('Não é possível agendar consultas para datas passadas.');
                    return;
                }

                form.reset();
                document.getElementById('agenda_id').value = '';
                deleteButton.style.display = 'none';
                currentEventId = null;

                pacienteInput.value = ''; 
                pacienteCharCount.textContent = '0'; 
                pacienteCharCount.classList.remove('text-danger'); 
                pacienteInput.readOnly = false;
                dataHoraInput.readOnly = false;
                document.querySelector('#agendaForm button[type="submit"]').disabled = false;
                timeValidationFeedback.style.display = 'none'; 
                const year = clickedDate.getFullYear();
                const month = String(clickedDate.getMonth() + 1).padStart(2, '0');
                const day = String(clickedDate.getDate()).padStart(2, '0');
                const hours = String(clickedDate.getHours()).padStart(2, '0');
                const minutes = String(clickedDate.getMinutes()).padStart(2, '0');

                dataHoraInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;

               
                if (viewAllCheckbox.checked) {
                    document.getElementById('modal-medico-selection').style.display = 'block';
                    modalMedicoIdSelect.value = ''; 
                    especialidadeInput.value = ''; 
                } else {
                    document.getElementById('modal-medico-selection').style.display = 'none';
                    modalMedicoIdSelect.value = medicoSelect.value; 

                    const selectedMedicoOption = medicoSelect.options[medicoSelect.selectedIndex];
                    const medicoSpecialtyText = selectedMedicoOption.text;
                    const specialtyMatch = medicoSpecialtyText.match(/ - (.*)$/);

                    if (specialtyMatch && specialtyMatch[1]) {
                        especialidadeInput.value = specialtyMatch[1];
                    } else {
                        especialidadeInput.value = '';
                    }
                }
                
                modal.show();
            },
            eventClick: function(info) {
                const event = info.event;
                const eventDate = new Date(event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                currentEventId = event.id;
                document.getElementById('agenda_id').value = event.id;
                pacienteInput.value = event.extendedProps.paciente;
                especialidadeInput.value = event.extendedProps.especialidade;

                modalMedicoIdSelect.value = event.extendedProps.medico_id;
                document.getElementById('modal-medico-selection').style.display = 'block';

                pacienteCharCount.textContent = pacienteInput.value.length;
                if (pacienteInput.value.length >= 100) {
                    pacienteCharCount.classList.add('text-danger');
                } else {
                    pacienteCharCount.classList.remove('text-danger');
                }

                const year = eventDate.getFullYear();
                const month = String(eventDate.getMonth() + 1).padStart(2, '0');
                const day = String(eventDate.getDate()).padStart(2, '0');
                const hours = String(eventDate.getHours()).padStart(2, '0');
                const minutes = String(eventDate.getMinutes()).padStart(2, '0');

                dataHoraInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                timeValidationFeedback.style.display = 'none'; 
                if (eventDate < today) {
                    pacienteInput.readOnly = true;
                    dataHoraInput.readOnly = true;
                    document.querySelector('#agendaForm button[type="submit"]').disabled = true;
                    deleteButton.style.display = 'none';
                    modalMedicoIdSelect.disabled = true;
                } else {
                    pacienteInput.readOnly = false;
                    dataHoraInput.readOnly = false;
                    document.querySelector('#agendaForm button[type="submit"]').disabled = false;
                    deleteButton.style.display = 'inline-block';
                    modalMedicoIdSelect.disabled = false;
                }

                modal.show();
            },
            eventDidMount: function(info) {
                info.el.setAttribute('title',
                    `Paciente: ${info.event.extendedProps.paciente}\n` +
                    `Médico: ${info.event.extendedProps.medico_nome || 'N/A'}\n` +
                    `Especialidade: ${info.event.extendedProps.especialidade}\n` +
                    `Data: ${info.event.start.toLocaleString('pt-BR')}`
                );
            },
            eventDrop: function(info) {
                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (eventDate < today) {
                    alert('Não é possível mover consultas para datas passadas.');
                    info.revert(); 
                    return;
                }

                
                const eventHour = eventDate.getHours();
                const eventMinute = eventDate.getMinutes();
                if (eventHour < 7 || eventHour > 22 || (eventHour === 22 && eventMinute > 0)) {
                    alert('Não é possível agendar consultas fora do horário permitido (07:00 às 22:00).');
                    info.revert();
                    return;
                }

                const year = eventDate.getFullYear();
                const month = String(eventDate.getMonth() + 1).padStart(2, '0');
                const day = String(eventDate.getDate()).padStart(2, '0');
                const hours = String(eventDate.getHours()).padStart(2, '0');
                const minutes = String(eventDate.getMinutes()).padStart(2, '0');

                const dateStr = `${year}-${month}-${day}T${hours}:${minutes}`;

                updateEvent({
                    id: info.event.id,
                    paciente: info.event.extendedProps.paciente,
                    especialidade: info.event.extendedProps.especialidade,
                    medico_id: info.event.extendedProps.medico_id,
                    data_hora: dateStr
                });
            }
        });

        calendar.render();
        updateDayEvents();
    }

    function saveEvent(eventData) {
        const url = eventData.id ? `/agenda/${eventData.id}` : '/agenda';
        const method = eventData.id ? 'PUT' : 'POST';

        const medicoIdToSave = document.getElementById('modal-medico-selection').style.display !== 'none' 
                               ? modalMedicoIdSelect.value 
                               : medicoSelect.value;

        if (!medicoIdToSave) {
            alert('Por favor, selecione um médico.');
            return Promise.reject(new Error('Médico não selecionado.'));
        }

        const selectedMedicoOption = Array.from(modalMedicoIdSelect.options).find(
            option => option.value === medicoIdToSave
        );
        let selectedSpecialty = '';
        if (selectedMedicoOption) {
            const medicoSpecialtyText = selectedMedicoOption.text;
            const specialtyMatch = medicoSpecialtyText.match(/ - (.*)$/);
            if (specialtyMatch && specialtyMatch[1]) {
                selectedSpecialty = specialtyMatch[1];
            }
        }
        
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ...eventData,
                medico_id: medicoIdToSave,
                especialidade: selectedSpecialty
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
                    calendar.refetchEvents(); 
                    throw new Error(data.message || 'Erro ao atualizar evento');
                }
                return data;
            })
            .catch(error => {
                console.error('Erro ao atualizar evento:', error);
                alert('Erro ao atualizar consulta: ' + error.message);
                calendar.refetchEvents(); 
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

        const url = new URL('/agenda/events', window.location.origin);
        url.searchParams.append('start', start.toISOString());
        url.searchParams.append('end', end.toISOString());

        if (!viewAllCheckbox.checked && medicoSelect.value) {
            url.searchParams.append('medico_id', medicoSelect.value);
        }

        eventList.innerHTML = '<p class="text-muted">Carregando agendamentos...</p>';

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(events => {
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

  
    dataHoraInput.addEventListener('change', function() {
        const selectedDateTime = new Date(this.value);
        const selectedHour = selectedDateTime.getHours();
        const selectedMinute = selectedDateTime.getMinutes();

        if (selectedHour < 7 || selectedHour > 22 || (selectedHour === 22 && selectedMinute > 0)) {
            timeValidationFeedback.style.display = 'block';
            this.classList.add('is-invalid');
            document.querySelector('#agendaForm button[type="submit"]').disabled = true;
        } else {
            timeValidationFeedback.style.display = 'none';
            this.classList.remove('is-invalid');
            document.querySelector('#agendaForm button[type="submit"]').disabled = false;
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const eventDate = new Date(dataHoraInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (eventDate < today) {
            alert('Não é possível agendar consultas para datas passadas.');
            return;
        }

   
        const selectedHour = eventDate.getHours();
        const selectedMinute = eventDate.getMinutes();
        if (selectedHour < 7 || selectedHour > 22 || (selectedHour === 22 && selectedMinute > 0)) {
            alert('Agendamentos só podem ser feitos entre 07:00 e 22:00.');
            return;
        }

        const eventData = {
            paciente: pacienteInput.value,
            data_hora: dataHoraInput.value
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

    viewAllCheckbox.addEventListener('change', function() {
        medicoContainer.style.display = this.checked ? 'none' : 'block';
        if (this.checked) {
            medicoSelect.value = ''; 
        } else {
            if (!medicoSelect.value) {
                medicoSelect.value = medicoSelect.options[0] ? medicoSelect.options[0].value : '';
            }
        }
        calendar.refetchEvents();
        updateDayEvents();
    });

    medicoSelect.addEventListener('change', function() {
        if (!viewAllCheckbox.checked) {
            updateDayEvents();
        }
        calendar.refetchEvents();
    });

    if (pacienteInput.value) {
        pacienteCharCount.textContent = pacienteInput.value.length;
    }
    pacienteInput.addEventListener('input', function() {
        const currentLength = this.value.length;
        pacienteCharCount.textContent = currentLength;

        if (currentLength >= 100) {
            pacienteCharCount.classList.add('text-danger');
        } else {
            pacienteCharCount.classList.remove('text-danger');
        }
    });

    initCalendar();
});
</script>
@endsection