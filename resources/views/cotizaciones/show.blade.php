<x-app-layout>
	<div class="py-6">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white shadow-sm sm:rounded-lg p-4">
				<div class="d-flex justify-content-between mb-3">
					<h3 class="mb-0">Cotización #{{ $cot->id }}</h3>
					<!-- <a href="{{ route('coti.edit', $cot->id) }}" class="btn btn-secondary btn-sm">Editar</a> -->
					<a href="{{ route('coti.index') }}" class="btn btn-secondary btn-sm">Volver</a>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<h5>Cliente</h5>
						<p class="mb-1">{{ $cot->cliente->nombre ?? '-' }}</p>
						<p class="text-muted mb-0">{{ $cot->cliente->celular ?? '' }} {{ $cot->cliente->correo ? '· '.$cot->cliente->correo : '' }}</p>
					</div>
					<div class="col-md-6">
						<h5>Evento</h5>
						<p class="mb-1">Motivo: {{ $cot->motivo }}</p>
						<p class="mb-1">Sede: {{ $cot->punto->nombre ?? ($cot->sede ?? '-') }}</p>
						<p class="mb-0">Fecha: {{ optional($cot->fecha)->format('Y-m-d') }} {!! $cot->hora ? ' · '.$cot->hora : '' !!}</p>
					</div>
				</div>

				<div class="table-responsive mb-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Producto</th>
								<th style="width:120px">Cantidad</th>
								<th style="width:140px">Precio</th>
								<th style="width:160px">Total</th>
							</tr>
						</thead>
						<tbody>
							@foreach($cot->items as $item)
								<tr>
									<td>{{ $item->producto->proNombre ?? 'Producto #' . ($item->producto_id ?? '-') }}</td>
									<td>{{ $item->cantidad }}</td>
									<td>{{ number_format($item->producto_precio ?? 0, 0, ',', '.') }}</td>
									<td>{{ number_format($item->total_item ?? 0, 0, ',', '.') }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="row">
					<div class="col-md-6">
						<ul class="list-group">
							<li class="list-group-item d-flex justify-content-between">
								<strong>Subtotal</strong>
								<span>{{ number_format($cot->subtotal ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Descuento ({{ $cot->descuento_pct ?? 0 }}%)</strong>
								<span>{{ number_format($cot->descuento_monto ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Ipo consumo</strong>
								<span>{{ number_format($cot->ipoconsumo ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Reteica</strong>
								<span>{{ number_format($cot->reteica ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Retefuente</strong>
								<span>{{ number_format($cot->retefuente ?? 0, 0, ',', '.') }}</span>
							</li>
						</ul>
					</div>
					<div class="col-md-6">
						<ul class="list-group">
							<li class="list-group-item d-flex justify-content-between">
								<strong>Propina</strong>
								<span>{{ number_format($cot->propina ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Anticipo</strong>
								<span>{{ number_format($cot->anticipo ?? 0, 2, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Total final</strong>
								<span>{{ number_format($cot->total_final ?? 0, 0, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Saldo pendiente</strong>
								<span>{{ number_format($cot->saldo_pendiente ?? 0, 2, ',', '.') }}</span>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<strong>Creada</strong>
								<span>{{ optional($cot->created_at)->format('Y-m-d H:i') }}</span>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
</x-app-layout>