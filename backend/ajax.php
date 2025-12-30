<?php
// backend/ajax.php - API per operazioni CRUD
session_start();

// Verifica login
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_expense':
            $amount = floatval($_POST['amount']);
            $category_id = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $expense_date = $_POST['expense_date'];
            
            if ($amount <= 0 || !$category_id || !$expense_date) {
                throw new Exception('Compila tutti i campi obbligatori');
            }
            
            $stmt = $conn->prepare("
                INSERT INTO expenses (amount, category_id, description, expense_date)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$amount, $category_id, $description ?: null, $expense_date]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Spesa aggiunta con successo! 💰'
            ]);
            break;
            
        case 'update_expense':
            $id = intval($_POST['expense_id']);
            $amount = floatval($_POST['amount']);
            $category_id = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $expense_date = $_POST['expense_date'];
            
            if (!$id || $amount <= 0 || !$category_id || !$expense_date) {
                throw new Exception('Compila tutti i campi obbligatori');
            }
            
            $stmt = $conn->prepare("
                UPDATE expenses 
                SET amount = ?, category_id = ?, description = ?, expense_date = ?
                WHERE id = ?
            ");
            $stmt->execute([$amount, $category_id, $description ?: null, $expense_date, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Spesa aggiornata con successo! ✅'
            ]);
            break;
            
        case 'delete_expense':
            $id = intval($_POST['id']);
            
            if (!$id) {
                throw new Exception('ID non valido');
            }
            
            $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Spesa eliminata! 🗑️'
            ]);
            break;
            
        case 'get_expense':
            $id = intval($_POST['id']);
            
            if (!$id) {
                throw new Exception('ID non valido');
            }
            
            $stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
            $stmt->execute([$id]);
            $expense = $stmt->fetch();
            
            if (!$expense) {
                throw new Exception('Spesa non trovata');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $expense
            ]);
            break;
            
        case 'add_category':
            $name = trim($_POST['name']);
            $color = $_POST['color'];
            
            if (!$name || !$color) {
                throw new Exception('Compila tutti i campi');
            }
            
            // Verifica che il nome non esista già
            $stmt = $conn->prepare("SELECT id FROM expense_categories WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) {
                throw new Exception('Una categoria con questo nome esiste già');
            }
            
            $stmt = $conn->prepare("
                INSERT INTO expense_categories (name, color)
                VALUES (?, ?)
            ");
            $stmt->execute([$name, $color]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoria aggiunta con successo! 🏷️'
            ]);
            break;
            
        case 'update_category':
            $id = intval($_POST['category_id']);
            $name = trim($_POST['name']);
            $color = $_POST['color'];
            
            if (!$id || !$name || !$color) {
                throw new Exception('Compila tutti i campi');
            }
            
            // Verifica che il nome non esista già (escludendo la categoria corrente)
            $stmt = $conn->prepare("SELECT id FROM expense_categories WHERE name = ? AND id != ?");
            $stmt->execute([$name, $id]);
            if ($stmt->fetch()) {
                throw new Exception('Una categoria con questo nome esiste già');
            }
            
            $stmt = $conn->prepare("
                UPDATE expense_categories 
                SET name = ?, color = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $color, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoria aggiornata con successo! ✅'
            ]);
            break;
            
        case 'delete_category':
            $id = intval($_POST['id']);
            
            if (!$id) {
                throw new Exception('ID non valido');
            }
            
            // Verifica che non sia l'ultima categoria
            $count = $conn->query("SELECT COUNT(*) FROM expense_categories")->fetchColumn();
            if ($count <= 1) {
                throw new Exception('Non puoi eliminare l\'ultima categoria! 🚫');
            }
            
            $stmt = $conn->prepare("DELETE FROM expense_categories WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoria e spese associate eliminate! 🗑️'
            ]);
            break;
            
        default:
            throw new Exception('Azione non valida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>