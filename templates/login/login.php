    <!-- Page Content -->
    <div class="container row-fullheight clearfix">
      	<!-- Marketing Icons Section -->
      	<div class="row justify-content-center align-items-center mt-5 row-100">
        	<div class="col-lg-6 mb-5">
				<div class="card h-100">
					<h4 class="card-header">Anmelden</h4>
					<div class="card-body">
						<form action="?c=login<?php echo isset($_GET['p']) ? '&p='.$_GET['p'] : NULL;?>" method="POST" class="margin-bottom-0">
							<?php echo $error; ?>
							<div class="form-group">
								<label>Benutzername:</label>
								<input type="text" class="form-control" name="login-email" maxlength="64" placeholder="Benutzername oder E-Mail Adresse" required value="<?php echo isset($_POST["login-email"]) ? $_POST["login-email"] : ''; ?>" />
							</div>
							<div class="form-group">
								<label>Passwort:</label>
								<input type="password" class="form-control" name="login-passwd" maxlength="64" placeholder="Passwort" required />
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="login-remember" <?php echo checker(isset($_POST["login-remember"]) ? $_POST["login-remember"] : 0, 1, 1); ?> value="1"/> Eingeloggt bleiben
								</label>
							</div>
							<div class="login-buttons">
								<button type="submit" class="btn btn-success btn-block">Anmelden</button>
							</div>
						</form>
					</div>
					<div class="card-footer">
					  <a href="?p=pwv" class="btn btn-primary">Passwort vergessen?</a>
					</div>
				</div>
        	</div>
      	</div><!-- /.row -->
    </div><!-- /.container -->
