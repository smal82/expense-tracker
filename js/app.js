// js/app.js - JavaScript per il backend con SweetAlert2

// Toggle Mobile Menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger-menu');
    
    menu.classList.toggle('active');
    hamburger.classList.toggle('active');
}

function closeMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger-menu');
    
    menu.classList.remove('active');
    hamburger.classList.remove('active');
}

// Chiudi menu quando si clicca fuori
document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger-menu');
    
    if (menu && hamburger && !menu.contains(e.target) && !hamburger.contains(e.target)) {
        menu.classList.remove('active');
        hamburger.classList.remove('active');
    }
});

// Funzioni per i modali
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Aggiungi Spesa';
    document.getElementById('expenseForm').reset();
    document.getElementById('expense_id').value = '';
    document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('expenseModal').classList.add('active');
}

function openCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Aggiungi Categoria';
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id_edit').value = '';
    document.getElementById('cat_color').value = '#3498db';
    document.getElementById('categoryModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Chiudi modal cliccando fuori
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Form Spesa
$('#expenseForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    const expenseId = $('#expense_id').val();
    const action = expenseId ? 'update_expense' : 'add_expense';
    
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: formData + '&action=' + action,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfetto!',
                    text: response.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: response.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Errore di comunicazione',
                text: 'Impossibile contattare il server',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});

// Form Categoria
$('#categoryForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    const categoryId = $('#category_id_edit').val();
    const action = categoryId ? 'update_category' : 'add_category';
    
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: formData + '&action=' + action,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfetto!',
                    text: response.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: response.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Errore di comunicazione',
                text: 'Impossibile contattare il server',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});

// Modifica spesa
function editExpense(id) {
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: { action: 'get_expense', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const expense = response.data;
                document.getElementById('modalTitle').textContent = 'Modifica Spesa';
                document.getElementById('expense_id').value = expense.id;
                document.getElementById('amount').value = expense.amount;
                document.getElementById('category_id').value = expense.category_id;
                document.getElementById('expense_date').value = expense.expense_date;
                document.getElementById('description').value = expense.description || '';
                document.getElementById('expenseModal').classList.add('active');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Impossibile caricare la spesa',
                    confirmButtonColor: '#ef4444'
                });
            }
        }
    });
}

// Elimina spesa
function deleteExpense(id) {
    Swal.fire({
        title: 'Sei sicuro?',
        text: "Vuoi davvero eliminare questa spesa?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sì, elimina!',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: { action: 'delete_expense', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#expense-' + id).fadeOut(300, function() {
                            $(this).remove();
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminata!',
                            text: response.message,
                            confirmButtonColor: '#10b981',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: response.message,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }
            });
        }
    });
}

// Modifica categoria
function editCategory(id, name, color) {
    document.getElementById('categoryModalTitle').textContent = 'Modifica Categoria';
    document.getElementById('category_id_edit').value = id;
    document.getElementById('cat_name').value = name;
    document.getElementById('cat_color').value = color;
    document.getElementById('categoryModal').classList.add('active');
}

// Elimina categoria
function deleteCategory(id) {
    Swal.fire({
        title: 'Sei sicuro?',
        text: "Eliminando questa categoria verranno eliminate anche tutte le spese associate!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sì, elimina tutto!',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: { action: 'delete_category', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminata!',
                            text: response.message,
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: response.message,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }
            });
        }
    });
}