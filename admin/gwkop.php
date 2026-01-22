<?php
// Simple PHP page to embed Weltrade charts for SyntX synthetic assets using Weltrade's charting service.
// Symbols based on working format from provided code. Use hyphenated names as in the example.
// Note: These are broker-specific; ensure you're logged in or have access via Weltrade account.

$symbols = [
    'PainX-999' => 'PainX 999',
    'VolX-500' => 'VolX 500',
    'RangeX-750' => 'RangeX 750',
    'JumpX-250' => 'JumpX 250',
    'TrendX-100' => 'TrendX 100'
];

$default_symbol = 'PainX-999';
$timeframes = [
    ['name' => '1 Minuto', 'interval' => '1'],
    ['name' => '5 Minutos', 'interval' => '5'],
    ['name' => '15 Minutos', 'interval' => '15'],
    ['name' => '1 Hora', 'interval' => '60'],
    ['name' => '4 Horas', 'interval' => '240']
];
$intervals = array_column($timeframes, 'interval');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos SyntX Weltrade</title>
    <style>
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            font-family: Arial, sans-serif; 
            background-color: black; 
            color: white; 
        }
        .charts-container { 
            display: flex; 
            height: 100vh; 
            width: 100%; 
        }
        .chart-column { 
            flex: 1; 
            padding: 10px; 
            box-sizing: border-box; 
            display: flex; 
            flex-direction: column;
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
        .buttons button:disabled { background-color: #333; cursor: not-allowed; opacity: 0.5; }
        .chart-container { 
            flex: 1; 
            background-color: #2a2a2a;
            border-radius: 5px;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .chart-column.expanded {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 10;
            background-color: black;
        }
        .chart-column.expanded .chart-container {
            height: 100%;
            border-radius: 0;
        }
        .chart-column.hidden {
            display: none !important;
        }
        @media (max-width: 1200px) { 
            .charts-container { flex-direction: column; }
            .chart-column { height: auto; }
            .chart-column.expanded { position: relative; top: 0; height: auto; }
        }
        @media (max-width: 768px) { 
            .charts-container { height: 100vh; }
            .chart-column { height: auto; }
        }
    </style>
</head>
<body>
    <div class="charts-container" id="chartsContainer">
        <?php foreach ($timeframes as $index => $tf): ?>
            <div class="chart-column" data-index="<?php echo $index; ?>">
                <div class="buttons">
                    <button class="expand-btn">Ampliar</button>
                    <button class="restore-btn" style="display: none;">Restaurar</button>
                </div>
                <div class="chart-container">
                    <iframe id="chart_iframe_<?php echo $index; ?>"
                            src="https://weltradecharts.com/chart?lang=es&symbol=<?php echo urlencode($default_symbol); ?>&interval=<?php echo $tf['interval']; ?>&theme=dark&background=%232a2a2a"></iframe>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        var expandedIndex = null;
        var chartsContainer = document.getElementById('chartsContainer');

        // Expand functionality
        function expandChart(index) {
            if (expandedIndex !== null) {
                restoreChart();
            }
            expandedIndex = index;
            var columns = document.querySelectorAll('.chart-column');
            columns.forEach(function(col, i) {
                if (i === index) {
                    col.classList.add('expanded');
                    col.classList.remove('hidden');
                } else {
                    col.classList.add('hidden');
                }
            });
            // Show restore button for expanded
            document.querySelectorAll('.restore-btn').forEach(function(btn, i) {
                btn.style.display = (i === index) ? 'inline-block' : 'none';
            });
            // Disable all expand buttons
            document.querySelectorAll('.expand-btn').forEach(function(btn) {
                btn.disabled = true;
            });
        }

        function restoreChart() {
            if (expandedIndex === null) return;
            var columns = document.querySelectorAll('.chart-column');
            columns.forEach(function(col) {
                col.classList.remove('expanded', 'hidden');
            });
            // Hide all restore buttons
            document.querySelectorAll('.restore-btn').forEach(function(btn) {
                btn.style.display = 'none';
            });
            // Enable all expand buttons
            document.querySelectorAll('.expand-btn').forEach(function(btn) {
                btn.disabled = false;
            });
            expandedIndex = null;
        }

        // Event listeners for buttons
        document.querySelectorAll('.expand-btn').forEach(function(btn, index) {
            btn.addEventListener('click', function() {
                expandChart(index);
            });
        });

        document.querySelectorAll('.restore-btn').forEach(function(btn, index) {
            btn.addEventListener('click', function() {
                restoreChart();
            });
        });

        // Initial state: hide restore buttons
        document.querySelectorAll('.restore-btn').forEach(function(btn) {
            btn.style.display = 'none';
        });
    </script>
</body>
</html>