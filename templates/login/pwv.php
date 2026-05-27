    <!-- Page Content -->
    <div class="container">
      	<!-- Marketing Icons Section -->
      	<div class="row mt25px">
        	<div class="col-sm-6 col-sm-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading"><h4 class="mt0px mb0px">Passwort vergessen?</h4></div>
					<div class="panel-body">
						<form action="./?p=pwv&c=send" method="POST">
							<?php echo csrf_field(); ?>
							<p>Gebe deine E-Mail Adresse deines Account hier ein. Anschließend wird dir ein Link zugesendet mit dem du dein Passwort zur&uuml;cksetzen kannst.</p>
							<?php echo $error; ?>
							<div class="form-group">
								<label>E-Mail Adresse:</label>
								<input type="text" class="form-control" name="email" placeholder="E-Mail Adresse" required value="<?php echo htmlspecialchar(isset($_POST["email"]) ? $_POST["email"] : ''); ?>" />
							</div>
							<div><label>Sicherheitscode:</label></div>
							<div class="form-group">
								<img class="captcha-img" src="./?captcha=img" title="Klicke um neuen Code zu erhalten">
								<input type="text" name="captcha" maxlength="4" class="form-control captcha-field" placeholder="Code">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-success btn-block">Passwort zur&uuml;cksetzen</button>
							</div>
						</form>
					</div>
					<div class="panel-footer">
						<a href="./?p=login" class="btn btn-primary">zum Login</a>
					</div>
				</div>
        	</div>
      	</div><!-- /.row -->
    </div><!-- /.container -->
