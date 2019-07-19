
var vueApp = new Vue({
	el: '#wrapper',
	data: {
		disableSubmit : false,
		user: null,
		roles: null,
		activities: [],
		formRoles: {
			is_admin: 0,
			roles: []
		},
		form: {
			name: null,
			middle_name: null,
			last_name: null,
			email: null,
			phone: null,
			marital_status: null,
			gender:null,
			date_of_birth: null,
			employe_status: null,
			employe_date: null,
			medic_type: null,
			medic_npi: null,
			digital_signature: null
		},
		helper: null
	},
	created:function(){
		this.user  = JSON.parse( $('#data-user').val() );
		this.roles = JSON.parse( $('#data-roles').val() );
		this.helper = new PROJECT();
		this.getActivities();
	},
	methods:{
		getRoles:function(){

			let userRoles = this.user.roles.split(',');
			let roles     = this.roles;
			let nameRoles = [];
			if(!userRoles.length)
				return [];
			
			for(let i = 0; i < roles.length; i++)
			{
				if( userRoles.indexOf(roles[i].id) >= 0 )
					nameRoles.push(roles[i].title);
			}
			
			return nameRoles;
		},
		deleteUser: function(){
			let vm = this;

			Swal.fire({
				title: 'Are you sure you want to remove the user?',
				text: 'Delete user '+ vm.user.username,
				type: 'error',
				showCancelButton: true,
				//confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete',
				cancelButtonText: 'Close',
			}).then((result) => {
				if (result.value) {
					
					let url = APP.site_url('user/'+vm.user.id+'/delete');

			  		vm.$http.post(url, vm.form ).then(response => {
			  			if(!response.data.status)
			  			{
			  				toastr.error(response.data.message)
			  			}
			  			else
			  			{
			  				window.location.href = APP.site_url('user');
			  			}
			  			
			  			vm.getActivities();
			  		}, error => {
			  			APP.error( error.statusText );
			  		});
				}
			})
		},
		disableUser:function(){
			let vm = this;

			Swal.fire({
				title: 'Are you sure you want to deactivate the user?',
				text: 'Disable user '+ vm.user.username,
				type: 'warning',
				showCancelButton: true,
				//confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, disabled',
				cancelButtonText: 'Close',
			}).then((result) => {
				if (result.value) {
					
					let url = APP.site_url('user/'+vm.user.id+'/disabled');

			  		vm.$http.post(url, vm.form ).then(response => {
			  			if(!response.data.status)
			  				toastr.error(response.data.message)
			  			else
			  			{
			  				vm.user.status = 0;
			  				toastr.success(response.data.message);
			  			}

			  			vm.getActivities();
			  		}, error => {
			  			APP.error( error.statusText );
			  		});
				}
			})
		},
		enableUser:function(){
			let vm = this;

			Swal.fire({
				title: 'Are you sure you want to enable the user?',
				text: 'Enable user '+ vm.user.username,
				type: 'success',
				showCancelButton: true,
				//confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, enabled',
				cancelButtonText: 'Close',
			}).then((result) => {
				if (result.value) {
					
					let url = APP.site_url('user/'+vm.user.id+'/enabled');
					
			  		vm.$http.post(url, vm.form ).then(response => {
			  			if(!response.data.status)
			  				toastr.error(response.data.message)
			  			else
			  			{
			  				vm.user.status = 1;
			  				toastr.success(response.data.message);
			  			}
			  			
			  			vm.getActivities();
			  		}, error => {
			  			APP.error( error.statusText );
			  		});
				}
			})
		},
		getActivities: function()
		{
			let url = APP.site_url('activity/print/user/'+this.user.id);
			this.$http.get(url).then(response => {
				this.activities = response.data.activities;
			}, error => {
				APP.error(error.statusText )
			});
		},
		modalRoles: function(){
			let roles = (this.user.roles) ? this.user.roles.split(',') : [];

			this.formRoles = {
				is_admin: this.user.is_admin,
				roles: roles 
			}
			$('#modal-user-roles-form').modal();
		},
		updateRoles: function( e ){
			e.preventDefault();
			this.disableSubmit = true;

			let url = APP.site_url('user/'+this.user.id+'/updateRoles');

			this.$http.post(url, this.formRoles ).then(response => {
				if(!response.data.status)
				{
					toastr.error(response.data.message);
				}
				else
				{
					this.user.is_admin = response.data.user.is_admin;
					this.user.roles    = response.data.user.roles;
					this.getActivities();
					toastr.success(response.data.message)
					$('#modal-user-roles-form').modal('hide');
				}

				this.disableSubmit = false;
			}, error => {
				APP.error( error.responseText );
			})
		},
		modalBasic: function(){
			
			for(key in this.user )
				this.form[key] = this.user[key];

			$('#modal-user-basic-form').modal();
		},
		updateUser:function( e, type  ){
			e.preventDefault();
			this.disableSubmit = true;

			let url = APP.site_url('user/'+this.user.id+'/'+type);

			this.$http.post(url, this.user ).then(response => {
				if(!response.data.status)
				{
					toastr.error(response.data.message);
				}
				else
				{
					for(key in response.data.user )
						this.user[key] = response.data.user[key];
					
					this.getActivities();

					toastr.success(response.data.message)
					$('.modal').modal('hide');
				}

				this.disableSubmit = false;
			}, error => {
				APP.error( error.responseText );
			})
		},
		changePassword: function(){
			let vm = this;

			Swal.fire({
				customClass: {
					input: 'input-password'
				},
				title: 'Are you sure you wants to change password?',
				text: "Enter the new password",
				type: 'warning',
				showCancelButton: true,
				//confirmButtonColor: '#3085d6',
				//cancelButtonColor: '#d33',
				confirmButtonText: 'Change password!',
				cancelButtonText: 'Close',
				input: 'text',
  				inputPlaceholder: '',
  				inputValidator: (value) => {
					if(!value)
						return  'The new password field is required!'
					if(value.toString().length<6)
						return 'The password field must be at least 6 characters in length';
			  	}
			}).then((result) => {
				if (result.value) {
					
					let data = { password: result.value }
					let url = APP.site_url('user/'+vm.user.id+'/changePassword');
					
			  		vm.$http.post(url,data).then(response => {
			  			if(!response.data.status)
			  				toastr.error(response.data.message)
			  			else
			  			{
			  				toastr.success(response.data.message);
			  			}
			  			
			  			vm.getActivities();
			  		}, error => {
			  			APP.error( error.statusText );
			  		});
				}
			})
		}
	}
});