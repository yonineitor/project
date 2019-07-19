
appVue = new Vue({
	el: '#wrapper',
	data: {
		roles: null,
		helper: APP
	},
	created:function(){
		this.roles = JSON.parse($('#data-roles').val());
	},
	methods: {
		deleteRole: function( item , ev){
			
			if(ev)
				ev.preventDefault();	

			let vm = this;
			Swal.fire({
				title: "You are sure you want to continue with the delete?",
				text: "Role name "+ item.title,
				type: 'warning',
				showCancelButton: true,
				//confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Confirm!',
				cancelButtonText: 'Close',
			}).then((result) => {
				if (result.value) {
					
					let url = APP.site_url('roles/'+item.id+'/delete');
					
					vm.$http.post(url).then(response => {

						if( !response.data.status )
						{
							toastr.error(response.data.message)
						}
						else
						{
							toastr.success(response.data.message)
							let positon = vm.roles.indexOf(item);
							vm.roles.splice(positon, 1 );
						}
					}, error => {
						APP.error(error.statusText)
					});
					
				}
			});
		}
	}
});