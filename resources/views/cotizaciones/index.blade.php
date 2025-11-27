<x-app-layout>
	<style>
		/* Limitar ancho de la columna Items */
		.table td:nth-child(4),
		.table th:nth-child(4) {
			max-width: 200px;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	</style>

	<div class="py-6">
		<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white shadow-sm sm:rounded-lg p-4">
				@if (session('success'))
					<script>
						document.addEventListener('DOMContentLoaded', function () {
							Swal.fire({
								icon: 'success',
								title: '¡Éxito!',
								text: '{{ session('success') }}',
								confirmButtonColor: '#3085d6',
								confirmButtonText: 'OK'
							});
						});
					</script>
				@endif

				@if (session('error') || $errors->any())
					<script>
						document.addEventListener('DOMContentLoaded', function () {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: '{{ session('error') ?? $errors->first() }}',
								confirmButtonColor: '#d33',
								confirmButtonText: 'OK'
							});
						});
					</script>
				@endif
				
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h3 class="mb-0">Cotizaciones</h3>
					<a href="{{ route('coti.create') }}" class="btn btn-warning btn-sm">Nueva Cotización</a>
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Cliente</th>
								<th>Sede</th>
								<th>Items</th>
								<!-- <th>Subtotal</th>
								<th>Propina</th> -->
								<!-- <th>Anticipo</th> -->
								<th>Total final</th>
								<!-- <th>Saldo pendiente</th> -->
								<!-- <th>Creada</th> -->
								<th>Fecha</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@forelse($cotizaciones as $cot)
								<tr>
									<td>{{ $cot->id }}</td>
									<td>{{ $cot->cliente->nombre ?? '-' }}</td>
									<td>{{ $cot->punto->nombre ?? ($cot->sede ?? '-') }}</td>
									<td>
										{{ $cot->items->count() }}
										@if($cot->items->count())
											<br>
											<small class="text-muted">
												{{ $cot->items->pluck('producto_id')->map(function($id){ return optional(App\Models\Productos::find($id))->proNombre; })->filter()->take(3)->join(', ') }}
												@if($cot->items->count() > 3) ... @endif
											</small>
										@endif
									</td>
									<!-- <td>{{ number_format($cot->subtotal ?? 0, 0, ',', '.') }}</td>
									<td>{{ number_format($cot->propina ?? 0, 0, ',', '.') }}</td> -->
									<!-- <td>{{ number_format($cot->anticipo ?? 0, 2, ',', '.') }}</td> -->
									<td>${{ number_format($cot->total_final ?? 0, 0, ',', '.') }}</td>
									<!-- <td>{{ number_format($cot->saldo_pendiente ?? 0, 2, ',', '.') }}</td> -->
									<!-- <td>{{ optional($cot->created_at)->format('Y-m-d H:i') }}</td> -->
									<td>
										{{ $cot->fecha ? (is_object($cot->fecha) ? $cot->fecha->format('d-m-Y') : \Carbon\Carbon::parse($cot->fecha)->format('d-m-Y')) : '-' }}
										@if($cot->fecha)
											<br>
											<small class="text-muted">
												{{ is_object($cot->fecha) ? $cot->fecha->translatedFormat('l') : \Carbon\Carbon::parse($cot->fecha)->translatedFormat('l') }}
											</small>
										@endif
									</td>
									<td class="text-end">
										<div class="d-flex justify-content-end gap-1">
											<a href="{{route('coti.show', $cot->id )}}" class="btn btn-sm btn-outline-secondary">Ver</a>
											<a href="{{route('coti.export', $cot->id )}}" class="btn btn-sm btn-outline-secondary">Excel</a>
											<a href="{{route('coti.export.pdf', $cot->id )}}" class="btn btn-sm btn-outline-secondary">PDF</a>
											<button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#uploadFacturaModal{{ $cot->id }}" title="Cargar comprobante">
												<i class="fa-solid fa-file-invoice"></i>
											</button>
											<form action="{{ route('coti.destroy', $cot->id) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="btn btn-sm btn-outline-danger delete-btn"><i class="fa-solid fa-trash"></i></button>
											</form>
										</div>
									</td>
								</tr>

								<!-- Modal para cargar factura -->
								<div class="modal fade" id="uploadFacturaModal{{ $cot->id }}" tabindex="-1" aria-labelledby="uploadFacturaModalLabel{{ $cot->id }}" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="uploadFacturaModalLabel{{ $cot->id }}">Cargar Comprobante de Pago - Cotización #{{ $cot->id }}</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<form action="{{ route('coti.uploadFactura', $cot->id) }}" method="POST" enctype="multipart/form-data">
												@csrf
												<div class="modal-body">
													@if($cot->img_factura)
														<div class="mb-3">
															<label class="form-label"><strong>Comprobante actual:</strong></label>
															<div class="text-center">
																<img src="{{ asset('storage/cotizaciones_facturas/' . $cot->img_factura) }}" 
																	class="img-thumbnail mb-2" 
																	style="max-width: 100%; max-height: 300px;"
																	alt="Comprobante actual">
																<div>
																	<a href="{{ asset('storage/cotizaciones_facturas/' . $cot->img_factura) }}" 
																	target="_blank" 
																	class="btn btn-sm btn-outline-primary">
																		<i class="fa-solid fa-external-link-alt"></i> Ver en tamaño completo
																	</a>
																</div>
															</div>
															<hr>
														</div>
													@endif
													<div class="mb-3">
														<label for="img_factura{{ $cot->id }}" class="form-label">Seleccionar imagen del comprobante</label>
														<input type="file" class="form-control" id="img_factura{{ $cot->id }}" name="img_factura" accept="image/*" required>
														<div class="form-text">Formatos permitidos: JPG, PNG, PDF. Tamaño máximo: 5MB</div>
													</div>
													<div id="preview{{ $cot->id }}" class="mt-3"></div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
													<button type="submit" class="btn btn-primary">Guardar Comprobante</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@empty
								<tr>
									<td colspan="11" class="text-center">No hay cotizaciones.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="d-flex justify-content-end">
					{{ $cotizaciones->links() }}
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Handle delete button clicks
			document.querySelectorAll('.delete-btn').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const form = this.closest('.delete-form');
					
					Swal.fire({
						title: '¿Estás seguro?',
						text: "Esta acción no se puede deshacer",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#d33',
						cancelButtonColor: '#3085d6',
						confirmButtonText: 'Sí, eliminar',
						cancelButtonText: 'Cancelar'
					}).then((result) => {
						if (result.isConfirmed) {
							form.submit();
						}
					});
				});
			});

		// Handle image preview for factura uploads
		document.querySelectorAll('input[type="file"][name="img_factura"]').forEach(input => {
			input.addEventListener('change', function(e) {
				const file = e.target.files[0];
				const cotId = this.id.replace('img_factura', '');
				const previewDiv = document.getElementById('preview' + cotId);
				
				if (file && file.type.startsWith('image/')) {
					const reader = new FileReader();
					reader.onload = function(e) {
						previewDiv.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 300px;">';
					};
					reader.readAsDataURL(file);
				} else {
					previewDiv.innerHTML = '';
				}
			});
		});
	});
	</script>
</x-app-layout>