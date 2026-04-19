        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Installation - Step 4</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Einleitung</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=terms_of_use">Nutzungsbedingungen</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=step1">Step 1 - Voraussetzungen</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=step2">Step 2 - MySQL-Daten</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=step3">Step 3 - Grundeinstellungen</a></li>
                            <li class="breadcrumb-item active actual">Step 4 - Accounts</li>
                            <li class="breadcrumb-item active">Fertig</li>
              	        </ol>
                    </div>
				</div>
				<?php echo $error; ?>
                <div class="row">
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	<i class="fa fa-user-plus"></i> Account anlegen
                            </div>
                            <div class="card-body">
                            	<?php if(!$install->is_user()){ ?>
                                    <form action="index.php?p=step4&c=create_user" method="post">
										 <div class="form-group <?php echo $install->valid_field["username"]; ?>">
											<label>Benutzername:</label>
											<input type="text" class="form-control input-special" value="<?php echo isset($_POST['new-username']) ? $_POST['new-username'] : ''; ?>" name="new-username" maxlength="64" placeholder="Benutzername" required>
										</div>
										<div class="form-group <?php echo $install->valid_field["username"]; ?>">
											<label>E-Mail Adresse:</label>
											<input type="email" class="form-control input-special" value="<?php echo isset($_POST['new-email']) ? $_POST['new-email'] : ''; ?>" name="new-email" maxlength="64" placeholder="E-Mail Adresse" required>
										</div>
										<div class="form-group <?php echo $install->valid_field["fullname"]; ?>">
											<label>Vor-/Nachname:</label>
											<input type="text" class="form-control input-special" value="<?php echo isset($_POST['new-fullname']) ? $_POST['new-fullname'] : ''; ?>" name="new-fullname" maxlength="64" placeholder="Vor-/Namename">
										</div>
										<div class="form-group <?php echo $install->valid_field["passwd"]; ?>">
											<label>Passwort:</label>
											<input type="password" class="form-control input-special" name="new-password" maxlength="64" placeholder="Passwort" required>
										</div>
										<div class="form-group <?php echo $install->valid_field["pw_co"]; ?>">
											<label>Passwort best&auml;tigen:</label>
											<input type="password" class="form-control input-special" name="new-password-confirm" maxlength="64" placeholder="Passwort best&auml;tigen" required>
										</div>
                                        <div class="form-group">
                                            <a class="btn btn-default" href="index.php?p=step3">zur&uuml;ck</a>
                                            <input type="submit" class="btn btn-success float-right" value="Anlegen &amp; Weiter">
                                        </div>
                                    </form>
                                <?php } else { ?>
                                	<p>Es existiert bereits ein Account. Bitte fahre mit dem n&auml;chsten Schritt fort.</p>
									<a class="btn btn-default" href="index.php?p=step3">zur&uuml;ck</a>
									<a class="btn btn-success float-right" href="index.php?p=step5">Weiter</a>
                                <?php } ?>
                       		</div>
                        </div>
                    </div>
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	Hilfe
                            </div>
                            <div class="card-body">
                       			<p><strong>Benutzername:</strong><br> W&auml;hle einen Benutzernamen aus, welchen du dir gut merken kannst. Ung&uuml;nstige Benutzernamen sind: Admin, Adm, Administrator, Root<br><br></p>
                       			<p><strong>E-Mail:</strong><br> Gebe hier bitte eine E-Mail Adresse f&uuml;r deinen Account an. Diese wird ben6ouml;tigt dich &uuml;ber Sachen zu informieren und zum zur&uuml;ksetzen des Passwortes.<br><br></p>
                       			<p><strong>Passwort:</strong><br> W&auml;hle dein Passwort weise, es ist der Schl&uuml;ssel zu deinem System! Vermeide allt&auml;gliche Begriffe, Zahlenreihen, Geburtstage, Telefonnummern und leichte Passw&ouml;rter wie: admin, root, 123456, administrator, &lt;Name der Seite&gt;.<br><br></p>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </div>