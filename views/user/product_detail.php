<?php require dirname(__DIR__) . '/product_detail.php'; ?>
<script>document.querySelectorAll('form[action*="review-store"],form[action*="review-update"]').forEach(function(f){var i=document.createElement('input');i.type='hidden';i.name='csrf_token';i.value='<?= e(csrf_token()) ?>';f.appendChild(i);});</script>
