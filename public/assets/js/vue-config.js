
Vue.http.options.emulateJSON = true;
Vue.directive('init', {
	inserted: function(el, binding, vnode) {
		vnode.context[binding.arg] = binding.value;
	}
});

function copySource (source, obj = undefined) {
  // using native JSON functions removes reactivity
  // so we can clone an object without mutating the original source
  var data = JSON.parse(JSON.stringify(source));
  if(typeof obj === 'object')
  {
  		data['$position'] = obj.indexOf(source);
  }

  return data;
}

function deleteItem( obj, item ){
	//native function remove item
	//...
	var position = obj.indexOf(item);
	obj.splice( position ,1);
}

/**COMPONENTS**/
if(typeof(VuejsPaginate) !== 'undefined' )
	Vue.component('paginate', VuejsPaginate);
if(typeof(vuejsDatepicker) !== 'undefined' )
	Vue.component('datepicker', vuejsDatepicker );
if(typeof(VoerroTagsInput) !=='undefined' )
	Vue.component('tags-input', VoerroTagsInput);

Vue.component('selectdate',{
	props: ['value','readonly', 'format', 'enableDates' ],
	template: '<input type="text" class="form-control" />',
	mounted: function(){

		var vm = this;
		var datePickerOptions = {
			autoclose: true,
			clearBtn: true,
			language: "es",
		};
		
		datePickerOptions.format = (this.format) ? this.format : 'dd/mm/yyyy';
		
		if( this.enableDates)
		{
			datePickerOptions.beforeShowDay = function( date ){
				var sdate = moment(  date, 'dd-mm-yyy' ).format('DD/MM/Y');
	            if($.inArray(sdate, this.enableDates) != -1) {
	                return true;
	            }
	            return false;
			};
		}
		
		$(this.$el).datepicker( datePickerOptions )
		
		$(this.$el).datepicker('setDate', this.value );
		
		$(this.$el).datepicker().on('changeDate', function(e) {
			val = (this.value) ? this.value : '';
			
			vm.$emit('input', val);
			vm.$emit('change', val );
	    });

	    if(this.readonly)
	    	$(this.$el).attr('readonly','readonly');
	    else
	    	$(this.$el).removeAttr('readonly');
	},
	watch: {
		value: function( val ){
			if(!val)
			{
				return false;
			}

			$(this.$el).datepicker('setDate', val );
		},
		readonly: function( val, oldVal ){
			if(val)
			{
				$(this.$el).attr('readonly','readonly');
			}
			else
			{
				$(this.$el).removeAttr('readonly');	
			}
		}
	},
})

Vue.component('select2', {
	data: function () {
    	return {
      		options: {}
    	}
  	},
	props: ['value','url','items','parent'],
	template: '<select style="width:100%;" class="form-control"  v-on:change="changeEvent" @change="changeEvent"   @input="changeEvent" ></select>',
	mounted: function () {
		
		var vm      = this;
		var options = {};

		if(vm.parent)
			options['dropdownParent'] = $(vm.parent);

		if(vm.items)
			options['data'] = vm.items;
		
		if( vm.url )
		{
			options['ajax'] = {
				language: 'es',
				url: vm.url,
				dataType: 'json',
				params:{contentType: "application/json;charset=utf-8"},
				processResults: function (data) {
			      	return {
			      		results: data.records
			      	};
			    }
		  	};

			$(this.$el ).select2( options )
		}
		else
		{
			$(this.$el).select2( options ).val(this.value).trigger('change');
		}

		//set value
		$( this.$el ).on('select2:select', function (e ) {
			
			vm.updateValue( $(this).val() );
			vm.$emit('change', $(this).val() );
		});
  	},
  	methods: {
  		changeEvent:function( $event  ){
  			console.log("CHANGE--", $event)
  			//$emit('change', $event.target.checked )
  		},
        updateValue(val) {
            this.$emit('input', val);
        }
    },watch: {
    	value: function( val ){
 			$(this.$el)
    			.val(val)
    			.trigger('change');
    	},
		items: function (items) {
			
			if(this.url)
			{	
				$(this.$el ).select2({
					data: items,
					ajax: {
						language: 'es',
						url: this.url,
						dataType: 'json',
						params:{contentType: "application/json;charset=utf-8"},
						processResults: function (data) {
					      	return {
				      		results: data.records
					      	};
					    }
				  	}
				}).trigger('change');
			}
			else
			{
				$(this.$el).empty().select2({ data: items }).val(this.value).trigger('change')
			}

		}
	}
});

Vue.component('rangegas',{
	props: ['value','ticks','options'],
	template: '<input style="width:100%;" type="text" v-on:change="updateValue" />',
	mounted: function(){
		var vm = this;
		$(this.$el).bootstrapSlider({
			min: 0.00,
			max: 1.00,
			step: 0.01,
			ticks: [0.00,0.25,0.50,0.75,1.00],
			ticks_labels: ["Vacio","1/4","Medio","3/4","Lleno"],
		});

		$(this.$el).bootstrapSlider('setValue', this.value )
		
		$(this.$el).on('change', function(){
			vm.updateValue(this.value);
			vm.$emit('change', this.value );
		});
	},
	watch:{
		value: function( val ){
			$(this.$el).bootstrapSlider('setValue', val )
		}
	},
	methods:{
		updateValue(val) {
            this.$emit('input', val);
        }
	}
});

/**DIRECTIVES**/
Vue.directive('tooltip', {
	bind:function(el,binding){
		$(el).tooltip().removeAttr('title');
	}
});
Vue.directive('popover', {
	bind:function(el,binding){
		var options ={
			content: binding.value.content,
			title: binding.value.title,
			trigger: 'hover'
		};
		$(el).popover( options );
	},
	update:function( el, binding ){
		
		$(el).data('bs.popover').config.content =   binding.value.content;
		$(el).data('bs.popover').config.title   =   binding.value.title;
		
	}
});

Vue.component('limit-string',{
	data: function () {
		return {
			fullContent: '',
			showButton: false,
			maxLen: 120
		}
	},
	props: ['value','maxlength'],
	template: '<span>{{fullContent}}<a v-on:click="btnReadAll($event)" class="text-info" v-show="showButton" href="#">  ...Read more</a></span>',
	mounted: function(){
		
		if(typeof(this.maxlength) !== 'undefined' )
		{
			this.maxLen = this.maxlength;

		}
		if(typeof(this.value) === 'undefined')
		{
			return this.fullContent = '';
		}
		console.log("max",this.maxLen);
		if( this.value.length > this.maxLen )
		{
			this.fullContent = this.value.substring(0, this.maxLen );
			this.showButton  = true;
		}
		else
		{
			this.fullContent = this.value;
		}
	},
	methods: {
		btnReadAll: function( event ){
			event.preventDefault();
			this.showButton  = false;
			this.fullContent = this.value;
		}
	},
	watch: {
		value: function( val ){
			if( val.length > this.maxLen )
			{
				this.fullContent = val.substring(0, this.maxLen );
				this.showButton  = true;
			}
			else
			{
				this.fullContent = val;
			}
		}
	}
});


Vue.component('display-data-activity',{
	data: function () {
		return {
			dataContent: null,
			dataType: '',
			dataValue: null,
			dataTitle: ''
		}
	},
	props: ['value'],
	template: '<div v-show="dataContent">EXIST DATA</div>',
	mounted: function(){
		
	},
});
