<?php
$flash = flash_get('message');
if ($flash):
?>
  <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
    <?php echo e($flash); ?>
  </div>
<?php endif; ?>
