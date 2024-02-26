<main class="test-listing">
    <ul class="entry-point-box plain">
        <li>
            <div class="block entry-point entry-point-all-deliveries" data-launch_url="<?= get_data('delivery-execution-url') ?>" tabindex="-1">
                <h3><?= _dh(get_data('block-title')) ?></h3>
                <div class="clearfix">
                    <span class="action" tabindex="0" role="button" aria-label="<?= __('Start this test') ?>">
                        <span class="icon-play"></span> <?= __('Start') ?>
                    </span>
                </div>
            </div>
        </li>
    </ul>
</main>
