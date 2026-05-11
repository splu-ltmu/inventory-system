

<?php
  $brand = 'Inventory System';
  $pageTitle = 'Stocks';
  $pageSubtitle = 'Add new stock item.';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
    }
    .btn-link:hover{ background: rgba(37,99,235,.18); }

    .form-container{ max-width:600px; width:100%; margin:0 auto; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; box-shadow:0 4px 20px rgba(15,23,42,.06); }
    .form-group{ margin-bottom:20px; }
    .form-group label{ display:block; margin-bottom:8px; color: var(--text); font-weight:700; }
    .form-group select,
    .form-group input[type="text"],
    .form-group input[type="number"]{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; box-sizing:border-box; }
    .form-group select:focus,
    .form-group input[type="text"]:focus,
    .form-group input[type="number"]:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    
    .form-checkbox{ display:flex; align-items:center; gap:8px; margin-bottom:20px; }
    .form-checkbox input[type="checkbox"]{ width:18px; height:18px; cursor:pointer; }
    .form-checkbox label{ margin:0; font-weight:500; cursor:pointer; }

    .form-actions{ display:flex; gap:12px; margin-top:24px; }
    .btn-submit{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:none;
        background: var(--blue);
        color: white;
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    .btn-submit:hover{ 
        background: rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .btn-submit:active{
        transform: translateY(0);
    }
    .btn-cancel{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:1px solid var(--line);
        background: transparent;
        color: var(--text);
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover{ 
        background: var(--line);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.08);
    }
    .btn-cancel:active{
        transform: translateY(0);
    }

    .error-message{ color: var(--red); margin-bottom:16px; padding:12px; background: rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:8px; }
    .error-message ul{ margin:0; padding-left:20px; }
    .error-message li{ margin:4px 0; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Add New Stock</h2>
    <a class="btn-link" href="<?php echo e(route('stocks.index')); ?>">Back to Stocks</a>
</div>

<div class="form-container">
    <?php if($errors->any()): ?>
        <div class="error-message">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('stocks.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select name="category_id" id="category_id" required>
                <option value="">-- Choose a category --</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" data-code="<?php echo e($category->code ?? ''); ?>"><?php echo e($category->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_no">ID No:</label>
            <input type="text" name="id_no" id="id_no" value="<?php echo e(old('id_no')); ?>" required readonly style="background-color: #f5f5f5; cursor: not-allowed;">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" value="<?php echo e(old('description')); ?>" required>
        </div>

        <div class="form-group">
            <label for="unit">Unit:</label>
            <input type="text" name="unit" id="unit" value="<?php echo e(old('unit')); ?>" required>
        </div>

        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" name="stock" id="stock" value="<?php echo e(old('stock', 0)); ?>" min="0" required>
        </div>

        <div class="form-checkbox">
            <input type="checkbox" name="hidden" id="hidden" value="1" <?php echo e(old('hidden') ? 'checked' : ''); ?>>
            <label for="hidden">Hidden (Admin Only)</label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Stock</button>
            <a href="<?php echo e(route('stocks.index')); ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const idNoInput = document.getElementById('id_no');

    categorySelect.addEventListener('change', async function() {
        const categoryId = this.value;
        
        if (!categoryId) {
            idNoInput.value = '';
            return;
        }

        try {
            const response = await fetch(`/admin/stocks/generate-id/${categoryId}`);
            const data = await response.json();
            
            if (response.ok && data.id_no) {
                idNoInput.value = data.id_no;
            } else {
                idNoInput.value = '';
                alert(data.error || 'Error generating ID');
            }
        } catch (error) {
            console.error('Error generating ID:', error);
            idNoInput.value = '';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/stocks/create.blade.php ENDPATH**/ ?>