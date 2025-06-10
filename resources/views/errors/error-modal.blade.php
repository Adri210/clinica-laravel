<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="errorTitle">Erro</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="errorMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeErrorModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showErrorModal(message, title = 'Erro') {
    const modal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    const errorTitle = document.getElementById('errorTitle');
    
    errorMessage.textContent = message;
    errorTitle.textContent = title;
    modal.classList.remove('hidden');
    
    // Fechar modal quando clicar no botão
    document.getElementById('closeErrorModal').onclick = function() {
        modal.classList.add('hidden');
    }
    
    // Fechar modal quando clicar fora dele
    modal.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }
    
    // Fechar modal com a tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            modal.classList.add('hidden');
        }
    });
}

// Função para mostrar erros do Laravel
function showLaravelErrors(errors) {
    if (typeof errors === 'string') {
        showErrorModal(errors);
    } else if (typeof errors === 'object') {
        let errorMessage = '';
        for (let key in errors) {
            if (errors.hasOwnProperty(key)) {
                errorMessage += errors[key].join('<br>') + '<br>';
            }
        }
        showErrorModal(errorMessage);
    }
}
</script>