<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-[600px] relative">
        <button wire:click="$set('modalOpen', false)" class="absolute top-2 right-2">❌</button>
        <h2 class="text-xl font-bold mb-4">{{ $editing ? 'Editar Asignación' : 'Nueva Asignación' }}</h2>

        <form wire:submit.prevent="save" class="space-y-4">

            <div>
                <label>Empleado</label>
                <select wire:model="form.usuarios_num_doc" class="w-full border rounded p-1">
                    <option value="">Seleccione...</option>
                    @foreach ($usuarios as $u)
                        <option value="{{ $u->num_doc }}">{{ $u->usu_nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Turno</label>
                <select wire:model="form.turnos_id" class="w-full border rounded p-1">
                    <option value="">Seleccione...</option>
                    @foreach ($turnos as $t)
                        <option value="{{ $t->id_turnos }}">{{ $t->tur_nombre }} – {{ $t->tur_descripcion }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Nombre de la Tarjeta</label>
                <input type="text" wire:model="form.nombre" class="w-full border rounded p-1">
            </div>

            <div>
                <label>Día</label>
                <select wire:model="form.dia" class="w-full border rounded p-1">
                    @foreach ($columns as $col)
                        <option value="{{ $col }}">{{ $col }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Fecha</label>
                <input type="date" wire:model="form.tur_usu_fecha" class="w-full border rounded p-1">
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded">Guardar</button>
        </form>
    </div>
</div>
