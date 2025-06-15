<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }} - {{ $room }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-blue-500 text-white p-4">
                    <h1 class="text-xl font-semibold">Chat: {{ $room }}</h1>
                    <div id="online-users" class="text-sm opacity-75 mt-1">
                        <span>Usuarios conectados: <span id="user-count">0</span></span>
                    </div>
                </div>
                
                <div id="messages-container" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50">
                    @foreach($messages as $message)
                        <div class="message flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr($message->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $message->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-gray-700 mt-1 bg-white p-2 rounded-lg shadow-sm">{{ $message->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form id="message-form" class="border-t p-4 bg-white">
                    <div class="flex space-x-3">
                        <input 
                            type="text" 
                            id="message-input" 
                            placeholder="Escribe tu mensaje..." 
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            maxlength="1000"
                            required
                        >
                        <button 
                            type="submit" 
                            id="send-button"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const room = '{{ $room }}';
        const userId = {{ auth()->id() }};
        const userName = '{{ auth()->user()->name }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iniciando chat...', { room, userId, userName });
            
            // Verificar si Echo está disponible
            if (typeof window.Echo === 'undefined') {
                console.error('Echo no está disponible. Verifica que los assets se hayan compilado correctamente.');
                alert('Error: Echo no está disponible. Refresca la página o contacta al administrador.');
                return;
            }
            
            console.log('Echo disponible:', window.Echo);

            const messagesContainer = document.getElementById('messages-container');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const userCount = document.getElementById('user-count');

            // Unirse al canal del chat
            console.log('Intentando unirse al canal:', `chat.${room}`);
            const channel = window.Echo.join(`chat.${room}`)
                .here((users) => {
                    userCount.textContent = users.length;
                    console.log('Usuarios conectados:', users);
                })
                .joining((user) => {
                    userCount.textContent = parseInt(userCount.textContent) + 1;
                    addSystemMessage(`${user.name} se unió al chat`);
                    console.log('Usuario se unió:', user);
                })
                .leaving((user) => {
                    userCount.textContent = parseInt(userCount.textContent) - 1;
                    addSystemMessage(`${user.name} dejó el chat`);
                    console.log('Usuario se fue:', user);
                })                .listen('.message.sent', (e) => {
                    console.log('🎉 Mensaje recibido via broadcasting:', e);
                    addMessage(e);
                })
                .error((error) => {
                    console.error('Error en el canal:', error);
                });

            // Enviar mensaje
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const content = messageInput.value.trim();
                if (!content) return;

                // Deshabilitar el botón mientras se envía
                sendButton.disabled = true;
                sendButton.textContent = 'Enviando...';

                fetch('/chat/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: content,
                        room: room
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();                })
                .then(data => {
                    console.log('✅ Mensaje enviado exitosamente:', data);
                    if (data.success) {
                        messageInput.value = '';
                        messageInput.focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar el mensaje. Por favor, intenta de nuevo.');
                })
                .finally(() => {
                    // Rehabilitar el botón
                    sendButton.disabled = false;
                    sendButton.textContent = 'Enviar';
                });
            });

            function addMessage(data) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message flex items-start space-x-3';
                
                const time = new Date(data.created_at).toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                messageDiv.innerHTML = `
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            ${data.user.name.charAt(0)}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900">${escapeHtml(data.user.name)}</span>
                            <span class="text-xs text-gray-500">${time}</span>
                        </div>
                        <p class="text-gray-700 mt-1 bg-white p-2 rounded-lg shadow-sm">${escapeHtml(data.content)}</p>
                    </div>
                `;

                messagesContainer.appendChild(messageDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function addSystemMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'system-message text-center text-sm text-gray-500 py-2 italic';
                messageDiv.textContent = message;
                
                messagesContainer.appendChild(messageDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Auto-scroll al final al cargar
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            // Focus en el input al cargar
            messageInput.focus();

            // Enviar mensaje con Enter
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    messageForm.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</x-app-layout>
