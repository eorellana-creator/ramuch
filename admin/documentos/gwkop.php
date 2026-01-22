<?php
// Simple PHP page to embed TradingView charts for Weltrade SyntX synthetic assets.
// Symbols based on Weltrade's documented tickers. Prefix 'WELTRADE:' for broker integration.
// IMPORTANT: To access these broker-specific symbols, connect your Weltrade trading account in TradingView settings (Profile > Trading Panel > Connect Broker).
// If symbols show "not found", ensure broker connection and login to TradingView. Search in TradingView for "Weltrade" symbols.

$symbols = [
    'WELTRADE:FX Vol 20' => 'FX Vol 20',
    'WELTRADE:SFX Vol 20' => 'SFX Vol 20',
    'WELTRADE:GainX 400' => 'GainX 400',
    'WELTRADE:PainX 400' => 'PainX 400',
    'WELTRADE:FlipX 1' => 'FlipX 1'
];

$default_symbol = 'WELTRADE:FX Vol 20';
$timeframes = [
    ['name' => '1 Minuto', 'interval' => '1'],
    ['name' => '5 Minutos', 'interval' => '5'],
    ['name' => '15 Minutos', 'interval' => '15'],
    ['name' => '1 Hora', 'interval' => '60'],
    ['name' => '4 Horas', 'interval' => '240']
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos SyntX Weltrade con TradingView</title>
    <style>
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            font-family: Arial, sans-serif; 
            background-color: black; 
            color: white; 
        }
        h1 { 
            text-align: center; 
            margin: 10px 0; 
        }
        .selector { 
            width: 100%; 
            padding: 10px; 
            background-color: #333333; 
            text-align: center; 
            box-sizing: border-box;
        }
        .selector label, .selector select { color: white; }
        .charts-container { 
            display: flex; 
            height: calc(100vh - 120px); 
            width: 100%; 
        }
        .chart-column { 
            flex: 1; 
            padding: 10px; 
            box-sizing: border-box; 
            display: flex; 
            flex-direction: column;
        }
        .chart-column h3 { 
            text-align: center; 
            margin: 0 0 10px 0; 
            color: white;
            flex-shrink: 0;
        }
        .buttons { 
            text-align: center; 
            margin-bottom: 10px; 
            flex-shrink: 0;
        }
        .buttons button { 
            background-color: #555; 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            margin: 0 5px; 
            border-radius: 3px; 
            cursor: pointer; 
        }
        .buttons button:hover { background-color: #777; }
        .buttons button:disabled { background-color: #333; cursor: not-allowed; }
        .chart-container { 
            flex: 1; 
            background-color: #2a2a2a; /* Fondo gris oscuro para los gráficos */
            border-radius: 5px;
        }
        .chart-column.expanded .chart-container {
            flex: 1;
        }
        @media (max-width: 1200px) { 
            .charts-container { flex-direction: column; }
            .chart-column { height: 20%; }
        }
        @media (max-width: 768px) { 
            .charts-container { height: calc(100vh - 120px); }
            .chart-column { height: auto; }
        }
    </style>
</head>
<body>
    <h1>Gráficos en Tiempo Real - SyntX Weltrade (vía TradingView)</h1>
    <div class="selector">
        <label for="symbolSelect">Seleccionar Activo Sintético:</label><br>
        <select id="symbolSelect">
            <?php foreach ($symbols as $key => $label): ?>
                <option value="<?php echo $key; ?>" <?php echo ($key === $default_symbol) ? 'selected' : ''; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
        <p><small>Los gráficos se actualizan en tiempo real cada segundo.</small></p>
    </div>
    <div class="charts-container">
        <?php foreach ($timeframes as $index => $tf): ?>
            <div class="chart-column" data-index="<?php echo $index; ?>">
                <h3><?php echo $tf['name']; ?></h3>
                <div class="buttons">
                    <button class="expand-btn">Ampliar</button>
                    <button class="restore-btn">Restaurar</button>
                </div>
                <div id="chart_container_<?php echo $index; ?>" class="chart-container"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script>
        var widgets = [];
        var symbolSelect = document.getElementById('symbolSelect');
        var defaultSymbol = '<?php echo $default_symbol; ?>';
        var expandedIndex = null;

        <?php foreach ($timeframes as $index => $tf): ?>
            (function() {
                var widget = new TradingView.widget({
                    "container_id": "chart_container_<?php echo $index; ?>",
                    "width": "100%",
                    "height": "100%",
                    "symbol": defaultSymbol,
                    "interval": "<?php echo $tf['interval']; ?>",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1", // Candlestick
                    "locale": "es",
                    "toolbar_bg": "#2a2a2a",
                    "enable_publishing": false,
                    "hide_top_toolbar": false,
                    "hide_legend": true,
                    "save_image": false,
                    "allow_symbol_change": true,
                    "backgroundColor": "#2a2a2a", // Gris oscuro para fondo del gráfico
                    "overrides": {
                        "paneProperties.background": "#2a2a2a",
                        "paneProperties.vertGridProperties.color": "#333333",
                        "paneProperties.horzGridProperties.color": "#333333",
                        "mainSeriesProperties.candleStyle.upColor": "#00bfff", // Azul para velas positivas
                        "mainSeriesProperties.candleStyle.downColor": "#ff0000", // Rojo para velas negativas
                        "mainSeriesProperties.candleStyle.borderUpColor": "#00bfff",
                        "mainSeriesProperties.candleStyle.borderDownColor": "#ff0000",
                        "mainSeriesProperties.candleStyle.wickUpColor": "#00bfff",
                        "mainSeriesProperties.candleStyle.wickDownColor": "#ff0000"
                    }
                });
                widgets.push(widget);
            })();
        <?php endforeach; ?>

        // Symbol change functionality
        symbolSelect.addEventListener('change', function() {
            var newSymbol = this.value;
            widgets.forEach(function(widget) {
                if (widget && widget.activeChart) {
                    widget.activeChart().setSymbol(newSymbol);
                }
            });
        });

        // Expand/Restore functionality
        function expandChart(index) {
            const columns = document.querySelectorAll('.chart-column');
            columns.forEach((col, i) => {
                if (i === index) {
                    col.classList.add('expanded');
                    col.style.display = 'flex';
                    col.style.flex = 'none';
                    col.style.width = '100%';
                } else {
                    col.style.display = 'none';
                }
            });
            // Update buttons
            updateButtons();
        }

        function restoreChart() {
            const columns = document.querySelectorAll('.chart-column');
            columns.forEach(col => {
                col.classList.remove('expanded');
                col.style.display = 'flex';
                col.style.flex = '1';
                col.style.width = '';
            });
            expandedIndex = null;
            updateButtons();
        }

        function updateButtons() {
            document.querySelectorAll('.expand-btn').forEach(btn => {
                btn.disabled = expandedIndex !== null;
            });
            document.querySelectorAll('.restore-btn').forEach(btn => {
                btn.disabled = expandedIndex === null;
            });
        }

        // Event listeners for buttons
        document.querySelectorAll('.expand-btn').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                if (expandedIndex !== null && expandedIndex !== index) {
                    restoreChart();
                }
                expandChart(index);
                expandedIndex = index;
            });
        });

        document.querySelectorAll('.restore-btn').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                if (expandedIndex === index) {
                    restoreChart();
                }
            });
        });

        // Initial button state
        updateButtons();
    </script>
</body>
</html>