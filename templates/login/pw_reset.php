    <!-- Page Content -->
    <div class="container">
      	<!-- Marketing Icons Section -->
      	<div class="row mt25px">
        	<div class="col-sm-6 col-sm-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading"><h4 class="mt0px mb0px">Passwort zur&uuml;cksetzen</h4></div>
					<div class="panel-body">
						<form action="./?p=pw_reset&c=reset&a=<?php echo $a; ?>" method="POST">
							<p>Bitte achte bei der Wahl eines neuen Passwortes auf die Sicherheit. Verwende keine W&ouml;rter wie: Passwort, Facebook, hallo123, administrator. 
                                                        Wir empfehlen ein Passwort zu nutzen, welches aus Gro&szlig;- wie Kleinbuchstaben, Zahlen und Zeichen besteht. 
                                                        Informationen und einen Passwortgenerator kannst du unter folgender Adresse finden: <a href="https://www.passwort-generator.eu/">https://www.passwort-generator.eu/</a></p>
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
								<img class="captcha-img" src="./?captcha=img" onClick="this.src = './?captcha=img&generate=' + Math.random()" title="Klicke um neuen Code zu erhalten">
								<input type="text" name="pwr_captcha" maxlength="4" class="form-control captcha-field" placeholder="Code">
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
