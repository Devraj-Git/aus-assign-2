<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
        <?php echo $_SESSION['message']['content']; ?>
    </div>
    <?php
    // Clear the message after displaying it
    unset($_SESSION['message']);
    ?>
<?php endif; ?>