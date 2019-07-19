
var vueApp = new Vue({
	el: '#wrapper',
	data: {
		disableSubmit : false,
		form: {
			username: null,
			email: null,
			name: null,
			middle_name: null,
			last_name: null,
			password: null,
			is_admin: "0",
			date_of_birth: null,
			gender: null,
			marital_status: null,
			phone: null,
			medical_information: null,
			roles: []
		}
	},
	methods:{
		onSubmit: function( event ){
			this.disableSubmit = true;
			event.preventDefault();
			
			this.$http.post( APP.site_url('user/insert') , this.form ).then( response => {
				if(!response.data.status)
				{
					toastr.error(response.data.message);
					this.disableSubmit = false;
				}
				else
				{
					window.location.href = APP.site_url("user/" + response.data.user_id  );
				}	
			}, error => {
				APP.error(error.statusText);
			});
		}
	}
});