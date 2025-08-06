<x-app-layout>
    <div class="container py-4">
        <canvas id="stockChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('stockChart').getContext('2d');

        const stockChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Cantidad en Stock',
                    data: @json($cantidades),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // hace la gráfica horizontal
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
