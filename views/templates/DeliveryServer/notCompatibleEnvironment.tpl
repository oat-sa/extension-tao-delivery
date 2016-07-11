<div class="test-listing">
    <h1>
        <?=__('You are prevented from launching an assessment.')?>
    </h1>

    <ul class="entry-point-box plain">
        <li>
            <a class="block entry-point entry-point-all-deliveries" href="<?=_url('index');?>">

                <?=__('The system is unable to start this assessment because you are attempting to launch it using an unsupported browser or operating system.  Please contact your proctor or support for assistance');?>
                <div class="clearfix">
                    <span class="text-link" href="<?=_url('index');?>"><span
                                class="icon-backward"></span> <?= __("Return") ?> </span>
                </div>
            </a>
        </li>
    </ul>

</div>
