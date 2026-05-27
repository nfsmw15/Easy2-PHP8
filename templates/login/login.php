    <!-- Page Content -->
    <div class="container">
      	<!-- Marketing Icons Section -->
      	<div class="row mt25px">
        	<div class="col-sm-6 col-sm-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading"><h4 class="mt0px mb0px">Anmelden</h4></div>
					<div class="panel-body">
						<form action="./?c=login<?php echo isset($_GET['p']) ? '&p='.$_GET['p'] : NULL;?>" method="POST" class="margin-bottom-0">
							<?php echo $error; ?>
							<div class="form-group">
								<label>Benutzername:</label>
								<input type="text" class="form-control" name="login-email" maxlength="64" placeholder="Benutzername oder E-Mail Adresse" required value="<?php echo isset($_POST["login-email"]) ? e($_POST["login-email"]) : ''; ?>" />
							</div>
							<div class="form-group">
								<label>Passwort:</label>
								<input type="password" class="form-control" name="login-passwd" maxlength="64" placeholder="Passwort" required />
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="login-remember" <?php echo checker(isset($_POST["login-remember"]) ? $_POST["login-remember"] : 0, 1, 1); ?> value="1"/> Eingeloggt bleiben								<small class="text-muted" style="display: block; margin-top: 5px;">
									<i class="fa fa-info-circle"></i> Dies speichert Cookies für 14 Tage auf diesem Ger&auml;t.
								</small>								</label>
							</div>
							<div class="login-buttons">
								<button type="submit" class="btn btn-success btn-block">Anmelden</button>
							</div>
						</form>
					</div>
					<div class="panel-footer">
					  <a href="./?p=pwv" class="btn btn-primary">Passwort vergessen?</a>
					</div>
				</div>
        	</div>
      	</div><!-- /.row -->
    </div><!-- /.container -->
