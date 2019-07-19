
appVue = new Vue({
	el: '#wrapper',
	data: {
		products: [],
		selected: {
			product: null,
			color: null,
			size: null,
			amount: null
		},
		confirmCart: []
	},
	created: function(){
		let data      = JSON.parse($("#data-products").val());
		this.products = data.products;
	},
	methods: {
		getColors: function()
		{
			if( this.selected.product  === null )
				return [];

			return this.selected.product.colors;
		},
		getSizes: function()
		{
			if( this.selected.color  === null )
				return [];
			
			return this.selected.color.size;
		},
		addCart: function()
		{
			
			if( this.selected.product  === null ){
				toastr.error("Favor de seleccionar el producto");
				return false;
			}

			if( this.selected.color  === null ){
				toastr.error("Favor de seleccionar el color");
				return false;
			}

			if( this.selected.size  === null ){
				toastr.error("Favor de seleccionar el tamanio");
				return false;
			}

			if( this.selected.amount  === null || this.selected.amount == "" || isNaN(this.selected.amount) ){
				toastr.error("Favor de indicar la cantidad");
				return false;
			}

			this.confirmCart.push({
				id: this.selected.product.id,
				productName: this.selected.product.name,
				color: this.selected.color.nameColor,
				size: this.selected.size.name,
				amount: this.selected.amount
			});
		},
		removeItem: function( item ){
			let positon = this.confirmCart.indexOf( item );
			this.confirmCart.splice( positon , 1 );
		},
		tryToPay: function(){
			if(!this.confirmCart.length)
			{
				toastr.error("Favor de agregar al menos un producto");
				return false;
			}

			let data = {
				products: this.confirmCart
			}
			
			let url = APP.site_url('products/tryToPay');
			
			this.$http.post(url, data ).then( response => {
				
				console.log(response.data);

				if(!response.data.status)
				{
					toastr.error(response.data.message);
				}
				else
				{
					toastr.success(response.data.message);
				}
				
			}, error => {
				toast.error( error.statusText );
			});
		}
	}
});