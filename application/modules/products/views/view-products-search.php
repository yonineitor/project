<textarea style="display:none;" id="data-products"><?= $_['products'];?></textarea>

<div class="card animated fadeInDown faster">
	<div class="card-header pb-0 pt-2">
		<div class="float-right">
    		<a href="<?= site_url('products/cart')?>" > <i class="fas fa-shopping-cart"></i> Comprar</a>
    	</div>
    	<label>Products</label>
	</div>
	<div class="card-body p-2" v-cloak >
		<table class="table table-sm table-hover mt-1 " >
			<thead class="text-muted " >
				<tr>
					<th > ID</th>
					<th >Nombre</th>
					<th >Disponibilidad</th>
					<th >Colores/ Tamaño y Cantidad</th>
				</tr>
			</thead>
			<tbody  >
				<tr v-for="item in products">
					<td>{{ item.id }}</td>
					<td>{{ item.name }}</td>
					<td>{{ item.available }}</td>
					<td>
						<div class="row" v-for="colors in item.colors">
							<div class="col-md-3">{{ colors.nameColor }}</div>
							<div class="col-md-9">
								<button  v-for="sizes in colors.size" type="button" class="btn-sm btn btn-secondary mr-2 mb-2" >
								  {{ sizes.name }}  <span class="badge badge-light">{{ sizes.quantity }}</span>
								</button>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>