<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">{{ __("¡Bienvenido!") }}</h3>
                    <p class="mb-6">{{ __("You're logged in!") }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Salas de Chat Disponibles:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <a href="{{ route('chat.index', 'general') }}" 
                                   class="block p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                    <h5 class="font-medium text-blue-900">💬 General</h5>
                                    <p class="text-sm text-blue-700">Sala principal de conversación</p>
                                </a>
                                
                                <a href="{{ route('chat.index', 'desarrollo') }}" 
                                   class="block p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                    <h5 class="font-medium text-green-900">🚀 Desarrollo</h5>
                                    <p class="text-sm text-green-700">Charlas sobre programación</p>
                                </a>
                                
                                <a href="{{ route('chat.index', 'random') }}" 
                                   class="block p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                                    <h5 class="font-medium text-purple-900">🎲 Random</h5>
                                    <p class="text-sm text-purple-700">Conversaciones casuales</p>
                                </a>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t">
                            <p class="text-sm text-gray-600">
                                💡 <strong>Tip:</strong> Puedes crear nuevas salas simplemente cambiando la URL: 
                                <code class="bg-gray-100 px-2 py-1 rounded">/chat/tu-sala-personalizada</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
