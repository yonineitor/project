
appVue = new Vue({
	el: '#wrapper',
	data: {
		products: []
	},
	created: function(){
		let data      = JSON.parse($("#data-products").val());
		this.products = data.products;
	},
	methods: {
		getCategories: function(){
			
		}
	}
});

