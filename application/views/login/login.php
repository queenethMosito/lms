<br/><br/>
<!-- Page Content -->
    <div class="container-fluid">
		
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<form role="form" method="POST" action="<?=WEB_PATH_S?>login/">
					<fieldset>
						<legend>Log in</legend>
						<input type="hidden" name="login" value="1" />
						<div class="form-group">
							<input name="email" id="login_email" type="email" class="form-control" placeholder="USERNAME">
						</div>
						<div class="form-group">
							<input name="password" id="login_password" type="password" class="form-control" placeholder="Password">
						</div>

						<div class="form-group">
							<button class="btn btn-lg btn-info btn-block" type="submit">Sign in</button>
						</div>

					</fieldset>
				</form>
					</div>
			<div class="col-md-3"></div>
		</div>
</div>
