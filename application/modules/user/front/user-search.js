const _userCtrl = function($vue){
	var vm = this;
	vm.per_page = 10;
	vm.filter 	= {
		id: ''
	};
	//#######SERVER#######//
	vm.items       = [];
	vm.total_count = 0;
	vm.pages       = 0;
	//######EVENTS######//
	vm.search = function( pageNumber){
		
		let dataInputGet = {
			format: 'json',
			per_page: vm.per_page,
			page: pageNumber,
			filters: vm.filter,
			sort: {
				name: 'id',
				type: 'DESC'
			}
		};
		
		let url = APP.site_url('user/search/?'+ $.param(dataInputGet) );
		
		$vue.$http.get( url ).then( function( response ) {
			vm.items       = response.body.paginate.result_data;
			vm.total_count = response.body.paginate.total_count;
			vm.pages       = response.body.paginate.pages;
		});
	}
};


appVue = new Vue({
	el: '#wrapper',
	data: {
		project: null,
		userCtrl: null,
	},
	created: function(){
		this.userCtrl = new _userCtrl( this ); 
		this.userCtrl.search(1);
		this.project   = new PROJECT();
	},
});

