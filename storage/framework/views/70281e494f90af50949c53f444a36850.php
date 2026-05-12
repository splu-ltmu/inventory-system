<?php
  $brand = 'Inventory System';
  $pageTitle = 'Categories';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:7px 10px;
        border-radius:8px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
        font-size:13px;
        transition: all 0.3s ease;
    }
    .btn-link:hover{ 
        background: rgba(37,99,235,.18);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.15);
    }
    .btn-link:active{
        transform: translateY(0);
    }

    .actions .btn{ padding:6px 8px; font-size:12px; }
    .actions .btn-danger{ padding:6px 8px; font-size:12px; background: #f8d7da; color: #991b1b; border-color: #991b1b; }
    .actions .btn-danger:hover{ background: #fca5a5; color: #7f1d1d; }

    .table-wrap{ overflow:auto; border:1px solid var(--line); border-radius:14px; }
    table{ width:100%; border-collapse:collapse; min-width: 720px; background:#fff; }
    th,td{ border:1px solid var(--line); padding:10px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; }
    td{ color: var(--text); }

    .actions{ display:flex; gap:8px; justify-content:flex-start; }
    .btn{
        padding:8px 10px; border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        cursor:pointer; font-weight:700;
        text-decoration:none;
        display:inline-block;
        transition: all 0.3s ease;
    }
    .btn:hover{ 
        background: rgba(37,99,235,.18);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.15);
    }
    .btn:active{
        transform: translateY(0);
    }

    .btn-danger{
        border-color: var(--orange);
        background: var(--orange-soft);
        color: var(--orange);
        transition: all 0.3s ease;
    }
    .btn-danger:hover{ 
        background: rgba(249,115,22,.22);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(249,115,22,.15);
    }
    .btn-danger:active{
        transform: translateY(0);
    }

    form{ display:inline; }

    /* Modal styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        border-radius: 14px;
        padding: 24px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        position: relative;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line);
    }

    .modal-header h3 {
        margin: 0;
        color: var(--text);
        font-size: 20px;
        font-weight: 700;
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--muted);
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--line);
        color: var(--text);
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 16px;
        border-top: 1px solid var(--line);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text);
        font-weight: 700;
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.2s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }

    .btn-primary {
        background: var(--blue);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: rgba(37,99,235,0.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,0.2);
    }

    .btn-secondary {
        background: transparent;
        color: var(--text);
        border: 1px solid var(--line);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: var(--line);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    }

    .error-message {
        color: var(--red);
        margin-bottom: 16px;
        padding: 12px;
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.3);
        border-radius: 8px;
        font-size: 14px;
    }

    .success-message {
        color: #166534;
        margin-bottom: 16px;
        padding: 12px;
        background: rgba(34,197,94,0.1);
        border: 1px solid rgba(34,197,94,0.3);
        border-radius: 8px;
        font-size: 14px;
    }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Categories</h2>
    <a class="btn-link" href="#" onclick="openCategoryModal()">Add New Category</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:120px;">ID</th>
                <th>Name</th>
                <th style="width:220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($cat->id); ?></td>
                    <td><?php echo e($cat->name); ?></td>
                    <td>
                        <div class="actions">
                            <a class="btn" href="<?php echo e(route('categories.edit', $cat->id)); ?>">Edit</a>

                            <form action="<?php echo e(route('categories.destroy', $cat->id)); ?>" method="POST"
                                  onsubmit="return confirm('Delete this category?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" style="color:var(--muted);">No categories found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close" onclick="closeCategoryModal()">&times;</button>
        
        <div class="modal-header">
            <h3>Add New Category</h3>
        </div>
        
        <div class="modal-body">
            <div id="modalMessages"></div>
            
            <form id="categoryForm" action="<?php echo e(route('categories.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="modalName">Name:</label>
                    <input type="text" name="name" id="modalName" placeholder="e.g., Computer Supplies" required>
                </div>
                
                <div class="form-group">
                    <label for="modalCode">Code:</label>
                    <input type="text" name="code" id="modalCode" placeholder="e.g., CS" maxlength="2" style="text-transform: uppercase;" required>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeCategoryModal()">Cancel</button>
            <button type="submit" form="categoryForm" class="btn-primary">Add Category</button>
        </div>
    </div>
</div>

<script>
function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'flex';
    document.getElementById('modalName').focus();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
    document.getElementById('categoryForm').reset();
    document.getElementById('modalMessages').innerHTML = '';
}

// Handle form submission
 document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messagesDiv = document.getElementById('modalMessages');
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messagesDiv.innerHTML = '<div class="success-message">' + data.success + '</div>';
            setTimeout(() => {
                closeCategoryModal();
                location.reload(); // Reload to show the new category
            }, 1500);
        } else if (data.errors) {
            let errorHtml = '<div class="error-message"><ul>';
            Object.values(data.errors).flat().forEach(error => {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul></div>';
            messagesDiv.innerHTML = errorHtml;
        }
    })
    .catch(error => {
        messagesDiv.innerHTML = '<div class="error-message">An error occurred. Please try again.</div>';
    });
});

// Auto-uppercase code input
document.getElementById('modalCode').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

// Close modal when clicking overlay
document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('categoryModal').style.display === 'flex') {
        closeCategoryModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>