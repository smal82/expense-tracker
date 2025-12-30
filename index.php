<?php
// index.php - Frontend pubblico - Focus sulla quotidianità
require_once 'config/database.php';

// Ottieni data o range di date
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-30 days'));
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Query per le statistiche del periodo
$stmt = $conn->prepare("
    SELECT 
        SUM(amount) as total,
        COUNT(*) as count
    FROM expenses 
    WHERE expense_date BETWEEN ? AND ?
");
$stmt->execute([$date_from, $date_to]);
$stats = $stmt->fetch();

// Query per le spese con filtri
$sql = "
    SELECT e.*, c.name as category_name, c.color as category_color
    FROM expenses e
    JOIN expense_categories c ON e.category_id = c.id
    WHERE e.expense_date BETWEEN ? AND ?
";
$params = [$date_from, $date_to];

if ($category_filter) {
    $sql .= " AND e.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY e.expense_date DESC, e.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Ottieni tutte le categorie per il filtro
$categories = $conn->query("SELECT * FROM expense_categories ORDER BY name")->fetchAll();

// Dati per il grafico a torta (somma per categoria nel periodo)
$stmt = $conn->prepare("
    SELECT c.id, c.name, c.color, SUM(e.amount) as total
    FROM expenses e
    JOIN expense_categories c ON e.category_id = c.id
    WHERE e.expense_date BETWEEN ? AND ?
    GROUP BY c.id, c.name, c.color
    ORDER BY total DESC
");
$stmt->execute([$date_from, $date_to]);
$chart_data = $stmt->fetchAll();

// Dati per il grafico a linea (ultimi 30 giorni - ULTIMA SPESA per categoria)
// Ottieni tutte le spese degli ultimi 30 giorni ordinate per data
$stmt = $conn->prepare("
    SELECT 
        e.expense_date,
        c.id as category_id,
        c.name as category_name,
        c.color as category_color,
        SUM(e.amount) as daily_total
    FROM expenses e
    JOIN expense_categories c ON e.category_id = c.id
    WHERE e.expense_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 29 DAY) AND CURDATE()
    GROUP BY e.expense_date, c.id, c.name, c.color
    ORDER BY e.expense_date ASC
");
$stmt->execute();
$daily_expenses = $stmt->fetchAll();

// Crea array di tutti gli ultimi 30 giorni
$line_dates = [];
for ($i = 29; $i >= 0; $i--) {
    $line_dates[] = date('Y-m-d', strtotime("-$i days"));
}

// Organizza i dati per categoria con logica "ultima spesa" (con somma giornaliera)
$line_categories = [];
$category_last_values = []; // Tiene traccia dell'ultimo valore per ogni categoria

// Inizializza le categorie
foreach ($daily_expenses as $expense) {
    $cat_id = $expense['category_id'];
    
    if (!isset($line_categories[$cat_id])) {
        $line_categories[$cat_id] = [
            'name' => $expense['category_name'],
            'color' => $expense['category_color'],
            'data' => []
        ];
        $category_last_values[$cat_id] = 0;
    }
}

// Per ogni giorno, riempie con l'ultima spesa disponibile
foreach ($line_dates as $date) {
    foreach ($line_categories as $cat_id => &$cat_data) {
        // Cerca se c'è una spesa (già sommata) in questo giorno per questa categoria
        $found_expense = null;
        foreach ($daily_expenses as $expense) {
            if ($expense['category_id'] == $cat_id && $expense['expense_date'] == $date) {
                $found_expense = $expense;
                break;
            }
        }
        
        if ($found_expense) {
            // C'è una nuova spesa oggi (già sommata dalla query) - aggiorna il valore
            $category_last_values[$cat_id] = floatval($found_expense['daily_total']);
        }
        
        // Usa l'ultimo valore conosciuto (o 0 se non ci sono ancora state spese)
        $cat_data['data'][] = $category_last_values[$cat_id];
    }
    unset($cat_data);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Spese Quotidiane</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>💰 Expense Tracker</h1>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <div class="stat-label">Totale Periodo</div>
                    <div class="stat-value">€<?php echo number_format($stats['total'] ?? 0, 2, ',', '.'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Numero Spese</div>
                    <div class="stat-value"><?php echo $stats['count'] ?? 0; ?></div>
                </div>
                <div class="stat-item">
                    <a href="backend/index.php" class="btn btn-primary btn-sm">🔐 Admin</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Lista Spese con Filtri Integrati -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📝 Elenco Spese Quotidiane</h2>
            </div>
            
            <!-- Filtri Inline -->
            <form method="GET" action="">
                <div class="filters">
                    <div class="form-group">
                        <label class="form-label">Dal</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" onchange="this.form.submit()">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Al</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" onchange="this.form.submit()">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="category" class="form-control" onchange="this.form.submit()">
                            <option value="">Tutte</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <a href="index.php" class="btn btn-primary" style="display: block; text-align: center;">🔄 Reset</a>
                    </div>
                </div>
            </form>
            
            <?php if (count($expenses) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrizione</th>
                            <th style="text-align: right;">Importo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                            <td>
                                <span class="category-badge" style="background-color: <?php echo $expense['category_color']; ?>">
                                    <?php echo htmlspecialchars($expense['category_name']); ?>
                                </span>
                            </td>
                            <td><?php echo $expense['description'] ? htmlspecialchars($expense['description']) : '<span class="text-muted">-</span>'; ?></td>
                            <td style="text-align: right;">
                                <span class="amount">€<?php echo number_format($expense['amount'], 2, ',', '.'); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-center text-muted">Nessuna spesa registrata per questo periodo.</p>
            <?php endif; ?>
        </div>

        <!-- Grafici -->
        <?php if (count($chart_data) > 0): ?>
        <div class="charts-grid">
            <!-- Grafico a Torta -->
            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title">📊 Spese per Categoria</h2>
                </div>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Grafico a Linea - Ultimi 30 Giorni -->
            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title">📈 Ultima Spesa per Categoria (30gg)</h2>
                </div>
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Dati per il grafico a torta
        const chartData = <?php echo json_encode($chart_data); ?>;
        
        if (chartData.length > 0) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.map(item => item.name),
                    datasets: [{
                        data: chartData.map(item => item.total),
                        backgroundColor: chartData.map(item => item.color),
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: { size: 11 },
                                usePointStyle: true,
                                boxWidth: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': €' + context.parsed.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Dati per il grafico a linea (ultimi 30 giorni - ultima spesa)
        const lineDates = <?php echo json_encode($line_dates); ?>;
        const lineCategories = <?php echo json_encode(array_values($line_categories)); ?>;
        
        if (lineCategories.length > 0 && lineDates.length > 0) {
            const datasets = lineCategories.map(cat => ({
                label: cat.name,
                data: cat.data,
                borderColor: cat.color,
                backgroundColor: cat.color + '20',
                tension: 0.1,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 0,
                borderWidth: 2
            }));
            
            const ctx2 = document.getElementById('lineChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: lineDates.map(date => {
                        const d = new Date(date + 'T00:00:00');
                        return d.getDate() + '/' + (d.getMonth() + 1);
                    }),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: { size: 10 },
                                usePointStyle: true,
                                boxWidth: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (context.parsed.y === 0) {
                                        return label + ': Nessuna spesa';
                                    }
                                    return label + ': €' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                font: { size: 9 },
                                autoSkip: true,
                                maxTicksLimit: 15
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 10 },
                                callback: function(value) {
                                    return '€' + value.toFixed(0);
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>