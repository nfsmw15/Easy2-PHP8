    <!-- Page Content -->
    <div class="container row-fullheight clearfix">
      	<!-- Marketing Icons Section -->
      	<div class="row justify-content-center align-items-center mt-5 row-100">
        	<div class="col-lg-6 mb-5">
				<div class="card h-100">
					<h4 class="card-header">Passwort zur&uuml;cksetzen</h4>
					<div class="card-body">
						<?php if($pwr_token_valid): ?>
						<form action="?p=pw_reset&c=reset&a=<?php echo e($a); ?>" method="POST">
							<?php echo csrf_field(); ?>
							<p>Bitte achte bei der Wahl eines neuen Passwortes auf die Sicherheit. Verwende keine W&ouml;rter wie: Passwort, Facebook, hallo123, administrator.
							Wir empfehlen ein Passwort zu nutzen, welches aus Gro&szlig;- wie Kleinbuchstaben, Zahlen und Zeichen besteht.</p>
							<?php echo $error; ?>
							<div class="form-group">
								<label>Neues Passwort:</label>
								<input type="password" class="form-control" name="pwr_passwd" placeholder="Neues Passwort" required />
							</div>
							<div class="form-group">
								<label>Neues Passwort wiederholen:</label>
								<input type="password" class="form-control" name="pwr_passwd_confirm" placeholder="Neues Passwort wiederholen" required />
							</div>
							<div><label>Sicherheitscode:</label></div>
							<div class="form-group">
								<img class="captcha-img" src="?captcha=img" title="Klicke um neuen Code zu erhalten">
								<input type="text" name="pwr_captcha" maxlength="4" class="form-control captcha-field" placeholder="Code">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-success w-100">Passwort zur&uuml;cksetzen</button>
							</div>
						</form>
						<?php else: ?>
						<div class="alert alert-danger"><?php echo $error; ?></div>
						<a href="?p=pwv" class="btn btn-primary btn-sm mt-2">Neuen Reset-Link anfordern</a>
						<?php endif; ?>
					</div>
					<div class="card-footer">
						<a href="?p=login" class="btn btn-primary">zum Login</a>
					</div>
				</div>
        	</div>
      	</div><!-- /.row -->
    </div><!-- /.container -->
