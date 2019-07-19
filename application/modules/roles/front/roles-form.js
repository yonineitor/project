
$('.fill-group').on('click', function(){
	let checked = $(this).is(':checked');
	let subgroup = $(this).data('subgroup');
	$('.'+subgroup).prop('checked', checked);
});

$('#rolesForm').on('submit', function( event ){
	let roleName = $('[name="role_name"]').val();
	if(!roleName)
	{
		event.preventDefault();
		toastr.error('The role name field is required');
	}
});