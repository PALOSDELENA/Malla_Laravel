<x-app-layout>
	<div class="py-6">
		<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white shadow-sm sm:rounded-lg p-4">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h3 class="mb-0">Cotizaciones</h3>
					<a href="{{ route('coti.create') }}" class="btn btn-primary btn-sm">Nueva Cotización</a>
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Cliente</th>
								<th>Sede</th>
								<th>Items</th>
								<th>Subtotal</th>
								<th>Propina</th>
								<th>Anticipo</th>
								<th>Total final</th>
								<th>Saldo pendiente</th>
								<th>Creada</th>
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
									<td>{{ number_format($cot->subtotal ?? 0, 0, ',', '.') }}</td>
									<td>{{ number_format($cot->propina ?? 0, 0, ',', '.') }}</td>
									<td>{{ number_format($cot->anticipo ?? 0, 2, ',', '.') }}</td>
									<td>{{ number_format($cot->total_final ?? 0, 0, ',', '.') }}</td>
									<td>{{ number_format($cot->saldo_pendiente ?? 0, 2, ',', '.') }}</td>
									<td>{{ optional($cot->created_at)->format('Y-m-d H:i') }}</td>
									<td class="text-end">
										<div class="d-flex justify-content-end gap-1">
											<a href="{{route('coti.show', $cot->id )}}" class="btn btn-sm btn-outline-secondary">Ver</a>
											<a href="{{route('coti.export', $cot->id )}}" class="btn btn-sm btn-outline-secondary">Excel</a>
											<a href="{{route('coti.export.pdf', $cot->id )}}" class="btn btn-sm btn-outline-secondary">PDF</a>
										</div>
									</td>
								</tr>
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
</x-app-layout>