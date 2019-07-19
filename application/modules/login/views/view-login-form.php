<div class="container">
  	<div class="row justify-content-md-center">
  		<div class="col col-md-4 text-center pt-5">
  			<h5>Structure</h5>
  			<img src="<?= site_url('assets/img/img3.png')?>"  class="img-thumbnail"  >
  		</div>
    	<div class="col col-md-8">
      		<div class="card mx-auto mt-5" style="background-color: rgba(255,255,255,0.6)" >
			    <div class="card-body ">
			    	<div class="text-center bg-light pt-2">
			    		<img title="Codeigniter 3" src="<?= site_url('assets/img/madeby/codeigniter.svg')?>" width="55" class="mr-2">
			    		<img title="Codeigniter Modular extension" src="<?= site_url('assets/img/madeby/hmvc.png')?>" width="95px" class="mr-2">
			    		<img title="Bootstrap 4" src="<?= site_url('assets/img/madeby/bootstrap.svg')?>" width="55" class="">
			    		<img title="VueJS" src="<?= site_url('assets/img/madeby/vue.png')?>" width="95px" class="mr-">
			    		<img title="fontawesome 4" src="<?= site_url('assets/img/madeby/fontawesome.png')?>" width="55px" class="mr-">
			    		<hr>
			    	</div>
			      	<form autocorrect="off" autocapitalize="none" method="POST" action="<?php echo site_url('/login/authenticate') ?>" autocomplete="off">
			      		<input name="guid" autofocus="true" type="hidden" id="guid"  />
						<div class="form-group">
							<label for="inputEmail"><?= _l('Username') ?></label>
							<input name="username" autocorrect="off" autocapitalize="none" value="<?= $_['username']?>" autofocus="true" type="text" id="inputEmail" class="focus-selected form-control"   autofocus="autofocus" />
						</div>
						<div class="form-group">
							<div class="form-label-group">
								<label for="inputPassword"><?= _l('Password') ?></label>
								<input name="password" autocorrect="off" autocapitalize="none" type="text" id="inputPassword" class="focus-selected form-control input-password"  data-validation="required">
							</div>
						</div>
						<button type="submit" class="btn btn-primary btn-block" ><?= _l('Sign in') ?></button>
			      	</form>
					<div class="text-right mt-3">
						<!--
						<a class="d-block small" href="<?php echo site_url('/public/reset-password') ?>" /><?= _l('Forgot your password?') ?></a>
						-->
					</div>
				</div>
			</div>
   	 	</div>
  	</div>
</div>

<script type="text/javascript">
	/**	RANDOM GUID **/
	APP.postLoad(function(){
		$('#guid').val( APP.guid() );
	})
</script>