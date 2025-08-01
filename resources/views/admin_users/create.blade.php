<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Crear Usuario</h2>
    </x-slot>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="t_doc" class="form-label">
                        Tipo Documento<span class="text-danger">*</span>
                    </label>
                    <select name="t_doc" class="form-select" required>
                        @foreach ($tDocumentos as $t_doc)
                            <option value="{{ $t_doc->id }}">{{ $t_doc->tipo_documento }}</option>
                        @endforeach
                    </select>
                    @error('t_doc') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="num_doc" class="form-label">
                        Número Documento<span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="num_doc" value="{{ old('num_doc') }}" required>
                    @error('num_doc') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usu_nombre" class="form-label">
                        Nombre<span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="usu_nombre" value="{{ old('usu_nombre') }}" required>
                    @error('usu_nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usu_apellido" class="form-label">
                        Apellido<span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="usu_apellido" value="{{ old('usu_apellido') }}" required>
                    @error('usu_apellido') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usu_celular" class="form-label">
                        Teléfono<span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="usu_celular" value="{{ old('usu_celular') }}" required>
                    @error('usu_celular') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        Correo<span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usu_cargo" class="form-label">Cargo</label>
                    <select name="usu_cargo" class="form-select" required>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}">{{ $cargo->car_nombre }}</option>
                        @endforeach
                    </select>
                    @error('usu_cargo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usu_punto" class="form-label">Punto de Trabajo</label>
                    <select name="usu_punto" class="form-select">
                        @foreach ($puntos as $punto)
                            <option value="{{ $punto->id }}">{{ $punto->nombre }}</option>
                        @endforeach
                    </select>
                    @error('usu_punto') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="password_confirmation">
                </div>
            </div>

            <button type="submit" class="btn btn-warning">Guardar</button>
        </form>
    </div>
</x-app-layout>
