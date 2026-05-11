<?php
  $brand = 'Inventory System';
  $pageTitle = 'Create Outbound';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('client.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div style="margin-top:16px;">
        <!-- Header -->
        <div style="margin-bottom:20px;">
            <h2 style="color:#1e40af; font-size:24px; font-weight:700; margin:0;">Create Outbound</h2>
            <p style="color:#64748b; font-size:14px; margin:4px 0 0 0;">Distribute items directly from your inventory</p>
        </div>

        <form method="POST" action="<?php echo e(route('client.outbounds.store')); ?>" style="margin-top:20px;">
            <?php echo csrf_field(); ?>

            <!-- Item Selection -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; color:#374151; font-size:14px; margin-bottom:6px;">Select Item *</label>
                <select name="stock_request_item_id" id="stock_request_item_id" required
                        style="width:100%; padding:10px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; background:#ffffff; transition:all 0.3s ease;"
                        onchange="updateAvailableQuantity()">
                    <option value="">Choose an item from your inventory...</option>
                    <?php $__currentLoopData = $availableInventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>" 
                                data-available="<?php echo e($item->available_qty); ?>"
                                data-stock-id="<?php echo e($item->stock->id_no); ?>"
                                data-stock-name="<?php echo e($item->stock->description ?? $item->stock->name ?? 'Item'); ?>"
                                data-price="<?php echo e($item->stock->price ?? 0); ?>">
                            <?php echo e($item->stock->id_no); ?> - <?php echo e($item->stock->description ?? $item->stock->name ?? 'Item'); ?> (Available: <?php echo e($item->available_qty); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['stock_request_item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#dc2626; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Available Quantity Display -->
            <div id="availableInfo" style="display:none; margin-bottom:20px; padding:12px 16px; background:#f0fdf4; border:1px solid #86efac; border-radius:8px;">
                <div style="font-weight:600; color:#166534; font-size:14px;">Available Quantity: <span id="availableQty">0</span></div>
                <div style="color:#64748b; font-size:12px; margin-top:2px;">Item: <span id="selectedItem">-</span></div>
            </div>

            <!-- Quantity -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; color:#374151; font-size:14px; margin-bottom:6px;">Quantity *</label>
                <input type="number" name="total" id="total" min="1" required
                       style="width:100%; padding:10px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; background:#ffffff; transition:all 0.3s ease;"
                       placeholder="Enter quantity to distribute">
                <?php $__errorArgs = ['total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#dc2626; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Recipient Selection -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; color:#374151; font-size:14px; margin-bottom:6px;">Recipient (Optional)</label>
                <select name="member_id" id="member_id"
                        style="width:100%; padding:10px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; background:#ffffff; transition:all 0.3s ease;">
                    <option value="">Direct distribution (no specific member)</option>
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($member->id); ?>"><?php echo e($member->name); ?> (<?php echo e($member->email); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div style="color:#64748b; font-size:12px; margin-top:4px;">Leave empty for direct distribution to your office</div>
                <?php $__errorArgs = ['member_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#dc2626; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Reason -->
            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; color:#374151; font-size:14px; margin-bottom:6px;">Reason (Optional)</label>
                <textarea name="reason" rows="3" maxlength="1000"
                          style="width:100%; padding:10px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; background:#ffffff; transition:all 0.3s ease; resize:vertical;"
                          placeholder="Enter reason for this distribution..."></textarea>
                <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#dc2626; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Form Actions -->
            <div style="display:flex; gap:12px; align-items:center;">
                <button type="submit" 
                        style="padding:12px 24px; background:linear-gradient(135deg, #10b981, #059669); color:#fff; border:none; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; transition:all 0.3s ease;"
                        onmouseover="this.style.background='linear-gradient(135deg, #059669, #047857)'" 
                        onmouseout="this.style.background='linear-gradient(135deg, #10b981, #059669)'">
                    Create Outbound
                </button>
                <a href="<?php echo e(route('client.outbounds.index')); ?>" 
                   style="padding:12px 24px; background:#f8fafc; color:#64748b; text-decoration:none; border:2px solid #e2e8f0; border-radius:8px; font-weight:600; font-size:14px; display:inline-block; transition:all 0.3s ease;"
                   onmouseover="this.style.background='#e2e8f0'" 
                   onmouseout="this.style.background='#f8fafc'">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function updateAvailableQuantity() {
            const select = document.getElementById('stock_request_item_id');
            const availableInfo = document.getElementById('availableInfo');
            const availableQty = document.getElementById('availableQty');
            const selectedItem = document.getElementById('selectedItem');
            const totalInput = document.getElementById('total');

            if (select.value) {
                const option = select.options[select.selectedIndex];
                const available = option.dataset.available;
                const stockId = option.dataset.stockId;
                const stockName = option.dataset.stockName;

                availableQty.textContent = available;
                selectedItem.textContent = `${stockId} - ${stockName}`;
                availableInfo.style.display = 'block';

                // Set max quantity
                totalInput.max = available;
                
                // Clear any previous value if it exceeds available
                if (totalInput.value && parseInt(totalInput.value) > parseInt(available)) {
                    totalInput.value = '';
                }
            } else {
                availableInfo.style.display = 'none';
                totalInput.max = '';
            }
        }

        // Validate quantity on input
        document.getElementById('total').addEventListener('input', function() {
            const select = document.getElementById('stock_request_item_id');
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const available = parseInt(option.dataset.available);
                const value = parseInt(this.value);

                if (value > available) {
                    this.setCustomValidity(`Maximum available quantity is ${available}`);
                } else {
                    this.setCustomValidity('');
                }
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateAvailableQuantity();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/outbounds/create.blade.php ENDPATH**/ ?>