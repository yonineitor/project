appVue = new Vue({
	el: '#wrapper',
	data: {
		form: null,
		disableSubmit: false,
		formPassword: {
			current_password: null,
			new_password: null,
			confirm_password: null 
		}
	},
	created:function(){
		this.form = JSON.parse($('#data-user').val());;
	},
	methods:{
		submit: function( event ){
			
			event.preventDefault();
			this.disableSubmit = true;
			let url = APP.site_url('profile/update' );

			this.$http.post( url, this.form ).then(response => {
				
				if(!response.data.status)
					toastr.error( response.data.message );
				else
					toastr.success( response.data.message );

				this.disableSubmit = false;
			}, error => {
				APP.error( error.statusText );
			});
		},
		changePassword: function( event ){
			event.preventDefault();
			this.disableSubmit = true; 
			let url = APP.site_url('profile/updatePassword' );

			this.$http.post( url, this.formPassword ).then(response => {

				if(!response.data.status)
					toastr.error( response.data.message );
				else
				{
					this.formPassword.current_password = '';
					this.formPassword.new_password     = '';
					this.formPassword.confirm_password = '';
					
					toastr.success( response.data.message );
					
					$('#modal-profile-changepassword').modal('hide');
				}

				this.disableSubmit = false;
			}, error => {
				APP.error( error.statusText );
			});
		}
	}
});