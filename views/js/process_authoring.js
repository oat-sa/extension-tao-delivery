function checkForm()
{
	// First, we check if the Process name was filled in.
	if ($(':text').eq(0).attr('value') == '')
	{
		return false;
	}
	
	var somethingChecked = false;
	
	$('#authoring_form').find(':radio').each(function() {
		if (this.checked)
			somethingChecked = true;
	});
	
	if (!somethingChecked)
		return false;
		
		
	return true;
}