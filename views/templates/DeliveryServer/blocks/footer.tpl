<?php
use oat\tao\helpers\Layout;
?>
</div>

<?php if (get_data('showControls')) :?>
<!-- /content wrap -->
<footer class="dark-bar">
    <?php
    if (!$val = Layout::getCopyrightNotice()):
        ?>
        © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= TAO_VERSION_NAME ?></span> ·
        <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
        · <?= __('All rights reserved.') ?>
    <?php else: ?>
        <?= $val ?>
    <?php endif; ?>
</footer>
<?php endif ?>
<div class="loading-bar"></div>
</body>
</html>