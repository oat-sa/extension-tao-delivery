<?php
use oat\tao\helpers\Layout;
$releaseMsgData = Layout::getReleaseMsgData();
?>
<span class="lft">
    <img src="<?= $releaseMsgData['logo'] ?>" alt="<?= $releaseMsgData['branding'] ?> Logo" id="tao-main-logo">
</span>