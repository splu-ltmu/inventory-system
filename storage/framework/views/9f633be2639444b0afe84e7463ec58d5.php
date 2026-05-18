<?php $__env->startSection('content'); ?>
<h2>Edit Stock Item</h2>

<?php if($errors->any()): ?>
    <div style="color: red;">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('stocks.update', $stock->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <label>ID No:</label><br>
    <input type="text" name="id_no" value="<?php echo e(old('id_no', $stock->id_no)); ?>" required><br><br>

    <label>Description:</label><br>
    <input type="text" name="description" value="<?php echo e(old('description', $stock->description)); ?>" required><br><br>

    <label>Unit:</label><br>
    <input type="text" name="unit" value="<?php echo e(old('unit', $stock->unit)); ?>" required><br><br>

    <label>Total:</label><br>
    <input type="number" name="total" value="<?php echo e(old('total', $stock->total)); ?>" min="0" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo e(old('stock', $stock->stock)); ?>" min="0" required><br><br>

    <label>Category:</label><br>
    <select name="category_id" required>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->id); ?>" <?php echo e($stock->category_id == $category->id ? 'selected' : ''); ?>>
                <?php echo e($category->name); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select><br><br>

    <label>Hidden (Admin Only):</label>
    <input type="checkbox" name="hidden" value="1" <?php echo e($stock->hidden ? 'checked' : ''); ?>><br><br>

    <button type="submit" style="padding:10px 16px; border-radius:8px; border:1px solid #2563eb; background:#2563eb; color:#fff; cursor:pointer; font-weight:700; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(37,99,235,.2)';" onmouseout="this.style.transform=''; this.style.boxShadow='';" onclick="this.style.transform='translateY(0)';">Update Stock</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/stocks/edit.blade.php ENDPATH**/ ?>