<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kop - Weltrade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a3a 0%, #0d1b2a 100%);
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
            padding: 0;
        }
        
        .container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 20px;
            overflow: hidden;
        }
        
        header {
            text-align: center;
            padding: 15px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #2a3a4a;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 8px;
            color: #4cc9f0;
            text-shadow: 0 0 10px rgba(76, 201, 240, 0.3);
        }
        
        .subtitle {
            color: #a0aec0;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .controls {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .control-btn {
            background: #2d3748;
            border: none;
            color: #e2e8f0;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .control-btn:hover {
            background: #4cc9f0;
            color: #0d1b2a;
        }
        
        .control-btn:disabled {
            background: #4a5568;
            color: #718096;
            cursor: not-allowed;
        }
        
        .charts-container {
            flex: 1;
            overflow: hidden;
            width: 100%;
        }
        
        .charts-grid {
            display: flex;
            gap: 15px;
            height: 100%;
            width: 100%;
        }
        
        .chart-container {
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            flex: 1;
        }
        
        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
        }
        
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(200, 50, 50, 0.7);
            border: none;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .close-btn:hover {
            background: rgba(220, 70, 70, 0.9);
            transform: scale(1.1);
        }
        
        .chart-wrapper {
            height: 100%;
        }
        
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        footer {
            text-align: center;
            padding: 15px;
            color: #718096;
            font-size: 0.85rem;
            border-top: 1px solid #2a3a4a;
            margin-top: 20px;
            flex-shrink: 0;
        }
        
        .empty-slot {
            background: rgba(30, 41, 59, 0.5);
            border: 2px dashed #4a5568;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            height: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .empty-slot:hover {
            background: rgba(30, 41, 59, 0.7);
            border-color: #4cc9f0;
        }
        
        .add-icon {
            font-size: 2rem;
            color: #4a5568;
        }
        
        .empty-slot:hover .add-icon {
            color: #4cc9f0;
        }
        
        /* Media queries para responsividad */
        @media (max-width: 1200px) {
            .charts-grid {
                flex-wrap: wrap;
            }
            
            .chart-container, .empty-slot {
                min-width: calc(50% - 10px);
                flex: 1;
            }
        }
        
        @media (max-width: 768px) {
            .charts-grid {
                flex-direction: column;
            }
            
            .chart-container, .empty-slot {
                min-width: 100%;
                height: 300px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .controls {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Panel Kop Flexibles</h1>
            <p class="subtitle">Controla la visualización de tus gráficos en tiempo real</p>
            
            <div class="controls">
                <!-- 
                <button id="reset-charts" class="control-btn">
                    <i class="fas fa-sync"></i> Restablecer Todos
                </button>
                -->
            </div>
        </header>
        
        <div class="charts-container">
            <div class="charts-grid" id="charts-grid">
                <!-- Los gráficos se agregarán dinámicamente aquí -->
            </div>
        </div>
        
        <footer>
            <p>Panel Kop - Weltrade © 2025</p>
        </footer>
    </div>

    <script>
        // Datos de los gráficos disponibles
        const availableCharts = [
            { id: 1, symbol: "PainX-999", visible: true },
            { id: 2, symbol: "VolX-500", visible: true },
            { id: 3, symbol: "RangeX-750", visible: true },
            { id: 4, symbol: "JumpX-250", visible: true },
            { id: 5, symbol: "TrendX-100", visible: false }
        ];

        // Inicializar el estado de los gráficos
        function initializeCharts() {
            const chartsGrid = document.getElementById('charts-grid');
            chartsGrid.innerHTML = '';
            
            // Agregar gráficos visibles
            availableCharts.forEach(chart => {
                if (chart.visible) {
                    addChartToGrid(chart);
                }
            });
            
            // Agregar slots vacíos si hay menos de 4 gráficos visibles (cambiar de 5 a 4)
            const visibleCount = availableCharts.filter(chart => chart.visible).length;
            for (let i = visibleCount; i < 4; i++) {
                addEmptySlot();
            }
            
            updateButtonStates();
        }

        // Añadir un gráfico al grid
        function addChartToGrid(chart) {
            const chartsGrid = document.getElementById('charts-grid');
            
            const chartContainer = document.createElement('div');
            chartContainer.className = 'chart-container';
            chartContainer.setAttribute('data-chart-id', chart.id);
            
            chartContainer.innerHTML = `
                <button class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
                <div class="chart-wrapper">
                    <iframe src="https://weltradecharts.com/chart?lang=es&symbol=${chart.symbol}&interval=15&theme=dark&background=%232d3748"></iframe>
                </div>
            `;
            
            chartsGrid.appendChild(chartContainer);
            
            // Añadir evento al botón de cerrar
            const closeBtn = chartContainer.querySelector('.close-btn');
            closeBtn.addEventListener('click', () => {
                closeChart(chart.id);
            });
        }

        // Añadir un slot vacío
        function addEmptySlot() {
            const chartsGrid = document.getElementById('charts-grid');
            
            const emptySlot = document.createElement('div');
            emptySlot.className = 'empty-slot';
            emptySlot.innerHTML = `
                <div class="add-icon">
                    <i class="fas fa-plus"></i>
                </div>
            `;
            
            emptySlot.addEventListener('click', addChart);
            
            chartsGrid.appendChild(emptySlot);
        }

        // Cerrar un gráfico
        function closeChart(chartId) {
            const visibleCharts = availableCharts.filter(chart => chart.visible);
            
            // No permitir cerrar el último gráfico visible
            if (visibleCharts.length <= 1) {
                alert('Debe haber al menos un gráfico visible.');
                return;
            }
            
            // Marcar el gráfico como no visible
            const chartIndex = availableCharts.findIndex(chart => chart.id === chartId);
            if (chartIndex !== -1) {
                availableCharts[chartIndex].visible = false;
            }
            
            // Reconstruir la cuadrícula
            initializeCharts();
        }

        // Agregar un gráfico
        function addChart() {
            // Verificar si ya tenemos 4 gráficos visibles (cambiar de 5 a 4)
            const visibleCount = availableCharts.filter(chart => chart.visible).length;
            if (visibleCount >= 4) {
                alert('Solo se permiten 4 gráficos como máximo.');
                return;
            }
            
            // Encontrar el primer gráfico no visible
            const hiddenChart = availableCharts.find(chart => !chart.visible);
            
            if (hiddenChart) {
                hiddenChart.visible = true;
                initializeCharts();
            }
        }

        // Restablecer todos los gráficos
        function resetCharts() {
            availableCharts.forEach(chart => {
                chart.visible = chart.id <= 4; // Mostrar solo los primeros 4 por defecto
            });
            initializeCharts();
        }

        // Actualizar el estado de los botones
        function updateButtonStates() {
            // Puedes agregar lógica adicional aquí si es necesario
        }

        // Inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            
            // Añadir eventos a los botones de control
            document.getElementById('reset-charts').addEventListener('click', resetCharts);
        });
    </script>
</body>
</html>