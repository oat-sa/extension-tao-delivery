<ul>
<?foreach(get_data('tests') as $test):?>
	<li><?=(string)$test->getLabel()?></li>
<?endforeach?>
</ul>