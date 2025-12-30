<?php
// backend/dashboard.php - Dashboard amministrativo
session_start();

// Verifica login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/database.php';

// Ottieni tutte le categorie
$categories = $conn->query("SELECT * FROM expense_categories ORDER BY name")->fetchAll();

// Ottieni statistiche
$current_month = date('Y-m');
$stmt = $conn->prepare("
    SELECT 
        SUM(amount) as total,
        COUNT(*) as count
    FROM expenses 
    WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?
");
$stmt->execute([$current_month]);
$stats = $stmt->fetch();

// Ottieni ultime spese
$expenses = $conn->query("
    SELECT e.*, c.name as category_name, c.color as category_color
    FROM expenses e
    JOIN expense_categories c ON e.category_id = c.id
    ORDER BY e.expense_date DESC, e.id DESC
    LIMIT 50
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Expense Tracker</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>💰 Expense Tracker - Admin</h1>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <div class="stat-label">Totale Mese</div>
                    <div class="stat-value">€<?php echo number_format($stats['total'] ?? 0, 2, ',', '.'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Spese</div>
                    <div class="stat-value"><?php echo $stats['count'] ?? 0; ?></div>
                </div>
            </div>
            
            <!-- Menu Desktop -->
            <div class="header-menu desktop-menu">
                <button onclick="openAddModal()" class="btn btn-success btn-sm">➕ Nuova Spesa</button>
                <a href="../index.php" class="btn btn-primary btn-sm">👁️ Frontend</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Esci</a>
            </div>
            
            <!-- Hamburger Menu Mobile -->
            <button class="hamburger-menu" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        
        <!-- Mobile Menu Dropdown -->
        <div class="mobile-menu" id="mobileMenu">
            <button onclick="openAddModal(); closeMobileMenu();" class="mobile-menu-item">
                <span class="menu-icon">➕</span>
                <span>Aggiungi Nuova Spesa</span>
            </button>
            <a href="../index.php" class="mobile-menu-item">
                <span class="menu-icon">👁️</span>
                <span>Visualizza Frontend</span>
            </a>
            <a href="logout.php" class="mobile-menu-item">
                <span class="menu-icon">🚪</span>
                <span>Esci</span>
            </a>
        </div>
    </header>

    <div class="container">
        <!-- Gestione Categorie -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🏷️ Categorie</h2>
                <button onclick="openCategoryModal()" class="btn btn-primary btn-sm">➕ Nuova Categoria</button>
            </div>
            <div id="categoriesList" style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($categories as $cat): ?>
                <div class="category-item" style="display: inline-flex; align-items: center; background-color: <?php echo $cat['color']; ?>; color: white; padding: 10px 15px; border-radius: 8px;">
                    <span style="font-weight: 600; margin-right: 10px;"><?php echo htmlspecialchars($cat['name']); ?></span>
                    <button onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>', '<?php echo $cat['color']; ?>')" 
                            style="background: rgba(255,255,255,0.3); border: none; color: white; cursor: pointer; padding: 4px 8px; border-radius: 4px; margin-right: 5px; font-size: 12px;">✏️</button>
                    <button onclick="deleteCategory(<?php echo $cat['id']; ?>)" 
                            style="background: rgba(255,255,255,0.3); border: none; color: white; cursor: pointer; padding: 4px 8px; border-radius: 4px; font-size: 12px;">🗑️</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lista Spese -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📝 Tutte le Spese</h2>
            </div>
            
            <div class="expenses-list">
                <?php foreach ($expenses as $expense): ?>
                <div class="expense-item" id="expense-<?php echo $expense['id']; ?>">
                    <div class="expense-item-left">
                        <div class="expense-item-date">
                            <span class="date-day"><?php echo date('d', strtotime($expense['expense_date'])); ?></span>
                            <span class="date-month"><?php echo strtoupper(date('M', strtotime($expense['expense_date']))); ?></span>
                        </div>
                        <div class="expense-item-info">
                            <div class="expense-item-category">
                                <span class="category-badge" style="background-color: <?php echo $expense['category_color']; ?>">
                                    <?php echo htmlspecialchars($expense['category_name']); ?>
                                </span>
                            </div>
                            <div class="expense-item-description">
                                <?php echo $expense['description'] ? htmlspecialchars($expense['description']) : '<span class="text-muted">Nessuna descrizione</span>'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="expense-item-right">
                        <div class="expense-item-amount">
                            €<?php echo number_format($expense['amount'], 2, ',', '.'); ?>
                        </div>
                        <div class="expense-item-actions">
                            <button onclick="editExpense(<?php echo $expense['id']; ?>)" class="action-icon-btn edit-btn" title="Modifica">
                                <span>✏️</span>
                            </button>
                            <button onclick="deleteExpense(<?php echo $expense['id']; ?>)" class="action-icon-btn delete-btn" title="Elimina">
                                <span>🗑️</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal Aggiungi/Modifica Spesa -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Aggiungi Spesa</h3>
                <button class="close-modal" onclick="closeModal('expenseModal')">×</button>
            </div>
            <form id="expenseForm">
                <input type="hidden" id="expense_id" name="expense_id">
                
                <div class="form-group">
                    <label class="form-label">Importo (€) *</label>
                    <input type="number" step="0.01" id="amount" name="amount" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Categoria *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Seleziona...</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Data *</label>
                    <input type="date" id="expense_date" name="expense_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descrizione (facoltativa)</label>
                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">💾 Salva</button>
                <button type="button" class="btn btn-danger" onclick="closeModal('expenseModal')">Annulla</button>
            </form>
        </div>
    </div>

    <!-- Modal Aggiungi/Modifica Categoria -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="categoryModalTitle">Aggiungi Categoria</h3>
                <button class="close-modal" onclick="closeModal('categoryModal')">×</button>
            </div>
            <form id="categoryForm">
                <input type="hidden" id="category_id_edit" name="category_id">
                
                <div class="form-group">
                    <label class="form-label">Nome *</label>
                    <input type="text" id="cat_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Colore *</label>
                    <input type="color" id="cat_color" name="color" class="form-control" value="#3498db">
                </div>
                
                <button type="submit" class="btn btn-success">💾 Salva</button>
                <button type="button" class="btn btn-danger" onclick="closeModal('categoryModal')">Annulla</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/app.js"></script>
</body>
</html>