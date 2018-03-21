<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\ApplicationHelper;
?>
<footer class="dark-bar">
    © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= ApplicationHelper::getVersionName() ?></span> ·
    Open Assessment Technologies S.A.
    · <?= __('All rights reserved.') ?>
</footer>
<?php Template::inc('blocks/careers-js.tpl', 'tao'); ?>
