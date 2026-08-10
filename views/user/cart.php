<?php require dirname(__DIR__) . '/cart.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutButton = document.querySelector('.summary-card button.btn-primary');
    if (!checkoutButton) return;
    const checkoutLink = document.createElement('a');
    checkoutLink.className = checkoutButton.className;
    checkoutLink.href = '<?= BASE_URL ?>?action=checkout';
    checkoutLink.textContent = checkoutButton.textContent;
    checkoutButton.replaceWith(checkoutLink);
});
</script>
