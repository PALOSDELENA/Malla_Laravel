<div class="grid grid-cols-7 gap-2">
    @foreach ($columns as $day)
        {{-- Contenedor de la columna (día) --}}
        <div class="border rounded shadow p-2 bg-gray-100 min-h-[300px] kanban-column" data-day="{{ $day }}">
            <h3 class="text-center font-bold text-lg">{{ $day }}</h3>

            {{-- Botón de agregar --}}
            <button wire:click="openModal()" class="text-sm bg-blue-600 text-white px-2 py-1 rounded my-2">+ Añadir</button>

            {{-- Lista de tarjetas arrastrables para ese día --}}
            <div class="kanban-list" id="day-{{ $day }}">
                @foreach ($asignaciones[$day] ?? [] as $asig)
                    {{-- Tarjeta arrastrable individual --}}
                    <div
                        class="kanban-card bg-white p-2 mb-2 rounded shadow cursor-move hover:bg-blue-50"
                        data-id="{{ $asig['id'] }}"
                    >
                        <div class="font-semibold">{{ $asig['nombre'] }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $asig['usuario']['usu_nombre'] ?? '—' }}<br>
                            <small>{{ $asig['turno']['tur_nombre'] ?? '—' }}</small>
                        </div>
                        <button
                            wire:click="openModal({{ $asig['id'] }})"
                            class="text-xs mt-1 bg-blue-500 text-black px-2 py-0.5 rounded hover:bg-blue-600"
                        >
                            Editar
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($modalOpen)
        @include('livewire.partials.modal-turno')
    @endif
</div>
