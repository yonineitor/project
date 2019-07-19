<textarea style="display:none;" id="data-products"><?= $_['products'];?></textarea>

<div class="row">
    <div class="col-md-4">
        <div class="card animated fadeInDown faster" >
        	<div class="card-header pb-0 pt-2">
        		<label>Comprar </label>
        	</div>
            <div class="card-body pt-0 pl-0  pr-0 "  v-cloak >
            	 <div class="row p-4">
                	<div class="col-md-12">
                        <div class="form-group mb-0">
                            <label class="col-form-label col-form-label-sm">Producto <span class='text-danger'>*</span></label>
                            <select class="form-control" v-model="selected.product">
                                <option v-for="item in products" :value="item" >{{ item.name }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="col-form-label col-form-label-sm">Color <span class='text-danger'>*</span></label>
                            <select class="form-control" v-model="selected.color">
                                <option v-for="item in getColors()" :value="item" >{{ item.nameColor }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="col-form-label col-form-label-sm">Tamaño <span class='text-danger'>*</span></label>
                            <select class="form-control" v-model="selected.size">
                                <option v-for="item in getSizes()" :value="item" >{{ item.name }}</option>
                            </select>
				        </div>
                        <div class="form-group mb-0">
                            <label class="col-form-label col-form-label-sm">Cantidad <span class='text-danger'>*</span></label>
                            <input type="text" v-model="selected.amount" class="form-control" />
                        </div>
						<div class="text-right mt-4">
							<button type="button" class="btn btn-primary" @click="addCart()" > Agregar al detalle </button>
						</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card animated fadeInDown faster" >
            <div class="card-header pb-0 pt-2">
                <label>Confirmar detalle de compra </label>
            </div>
            <div class="card-body pt-0 pl-0  pr-0 " v-cloak >
                <table class="table table-sm table-hover mt-1">
                    <thead class="">
                        <tr class="text-muted">
                            <th>Producto</th>
                            <th>Color</th>
                            <th>Tamanio</th>
                            <th>Cantidad</th>
                        </tr>
                        <tr v-for="item in confirmCart">
                            <td>{{ item.productName }} - ({{ item.id}})</td>
                            <td>{{ item.color }}</td>
                            <td>{{ item.size }}</td>
                            <td>{{ item.amount }}</td>
                            <td>
                                <button type="button" class="btn-sm btn btn-danger "  @click="removeItem(item)">Eliminar</button>
                            </td>
                        </tr>
                    </thead>
                </table>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary" @click="tryToPay()" > Realizar compra </button>
                </div>
            </div>
        </div>
    </div>
</div>
