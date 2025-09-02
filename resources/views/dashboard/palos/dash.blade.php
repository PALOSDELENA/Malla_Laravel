<x-app-layout>
    <style>
        .iframe-container {
            position: relative;
            width: 100%;
            height: calc(100vh - 50px);
            margin-bottom: 2em;
        }

        .iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <div class="iframe-container">
        <div class="loading" id="loading"></div>
        <iframe id="dashboardFrame" src="https://app.powerbi.com/reportEmbed?reportId=f43790f6-de68-4e3e-9a4f-3abcb250696c&autoAuth=true&ctid=f6c12f04-b932-4ae1-96f3-22710dca4704" allowFullScreen="true"></iframe>
    </div>

    <script>
        function cambiarDashboard(dashboard) {
            var iframe = document.getElementById('dashboardFrame');
            var loader = document.getElementById('loading');

            loader.style.display = "block";
            iframe.style.opacity = 0;

            setTimeout(() => {
                iframe.src = dashboard === 'palos' 
                    ? "https://app.powerbi.com/reportEmbed?reportId=f43790f6-de68-4e3e-9a4f-3abcb250696c&autoAuth=true&ctid=f6c12f04-b932-4ae1-96f3-22710dca4704"
                    : "https://app.powerbi.com/reportEmbed?reportId=83cf90e3-f81f-4491-b688-afd888407729&autoAuth=true&ctid=f6c12f04-b932-4ae1-96f3-22710dca4704";
            }, 500);

            iframe.onload = () => {
                loader.style.display = "none";
                iframe.style.opacity = 1;
            };
        }
    </script>
</x-app-layout>