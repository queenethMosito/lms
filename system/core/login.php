<br/><br/>
<!-- Page Content -->
    <div class="container">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6"></div>
			<div class="col-md-3"></div>
		</div>
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
						<?php if($error): ?>
						<div class="alert alert-danger" role="alert">
							<?php
							if ($result->passwordError) {
								// Incorrect email address or password
								if ($result->user) {
									// Found the account, so the password must be incorrect
							?>
									<strong>Incorrect password entered</strong>
									<p>The password you entered is incorrect. Please try again. Also ensure that your caps lock is off as passwords are case sensitive.</p>
									<p>
										If you have forgotten your password, you can <a style="color: blue" href="<?=WEB_PATH_S?>forgot-password">request a new one</a>.
									</p>
									<p></p>
							<?php
								} else {
									// Could found the account so check email address or register profile
							?>
									<strong>Incorrect username</strong>
									<p>The username you entered does not exists on our database.</p>
									<p>Please ensure that it was typed in correctly.</p>
									<p>If the username was typed in correctly, please contact the system administrator for additional assistance.</p>
									<p></p>
							<?php
								}
							} else {
								// Other error such as inactive accounts

								echo $error;
							}
							?>
						</div>
						<?php endif; ?>
					</div>
			<div class="col-md-3"></div>
		</div>
</div>
