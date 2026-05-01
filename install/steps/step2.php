        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Installation - Step 2</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Einleitung</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=terms_of_use">Nutzungsbedingungen</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=step1">Step 1 - Voraussetzungen</a></li>
                            <li class="breadcrumb-item active actual">Step 2 - MySQL-Daten</li>
                            <li class="breadcrumb-item active">Step 3 - Grundeinstellungen</li>
                            <li class="breadcrumb-item active">Step 4 - Accounts</li>
                           <li class="breadcrumb-item active">Fertig</li>
              	        </ol>
                    </div>
				</div>
				<?php echo $error; ?>
                <div class="row">
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	MySQL Daten eingeben
                            </div>
                            <div class="card-body">
                                <form action="index.php?p=step2&c=mysql" method="post">
                                    <div class="form-group <?php echo $install->valid_field["host"]; ?>">
                                        <label>MySQL-Server / Host:</label>
                                        <input type="text" class="form-control" maxlength="64" name="mysql_host" value="<?php echo isset($_POST['mysql_host']) ? $_POST['mysql_host'] : str_replace('[host]', 'localhost', $mysql_data['host']); ?>" placeholder="MySQL Server / Host">
                                    </div>
                                    <div class="form-group <?php echo $install->valid_field["user"]; ?>">
                                        <label>MySQL-Benutzer:</label>
                                        <input type="text" class="form-control" maxlength="32" name="mysql_user" value="<?php echo isset($_POST['mysql_user']) ? $_POST['mysql_user'] : str_replace('[user]', '', $mysql_data['user']); ?>" placeholder="Benutzername">
                                    </div>
                                    <div class="form-group <?php echo $install->valid_field["passwd"]; ?>">
                                        <label>MySQL-Passwort:</label>
                                        <input type="text" class="form-control" maxlength="128" name="mysql_passwd" value="<?php echo isset($_POST['mysql_passwd']) ? $_POST['mysql_passwd'] : str_replace('[pass]', '', $mysql_data['passwd']); ?>" placeholder="Passwort">
                                    </div>
                                    <div class="form-group <?php echo $install->valid_field["database"]; ?>">
                                        <label>MySQL-Datenbankname:</label>
                                        <input type="text" class="form-control" maxlength="63" name="mysql_database" value="<?php echo isset($_POST['mysql_database']) ? $_POST['mysql_database'] : str_replace('[dbna]', '', $mysql_data['database']); ?>" placeholder="Datenbankname">
                                    </div>
                                    <div class="form-group <?php echo $install->valid_field["prefix"]; ?>">
                                        <label>MySQL-Pr&auml;fix:</label>
                                        <input type="text" class="form-control" maxlength="16" name="mysql_prefix" value="<?php echo isset($_POST['mysql_prefix']) ? $_POST['mysql_prefix'] : str_replace('[pref]', '', str_replace('_ml', '', $mysql_data['prefix'])); ?>" placeholder="Pr&auml;fix">
                                    </div>
                                    <div class="form-group">
                                        <a class="btn btn-default" href="index.php?p=step1">zur&uuml;ck</a>
                                        <?php if($install->condition){ ?>
                                            <input type="submit" class="btn btn-success float-right" value="Speichern &amp; Weiter">
                                        <?php } ?>
                                    </div>
                                </form>
                       		</div>
                        </div>
                    </div>
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	Hilfe
                            </div>
                            <div class="card-body">
                            	<p><strong>MySQL-Server:</strong><br> Hier musst du die Adresse deines MySQL-Servers eintragen, befindet sich dieser auf dem gleichen Server wie die Webseite, brauchst du diese nicht zu &auml;ndern (Wenn dieser &uml;ber "localhost" erreichbar ist). Diese findest du meist bei deinem Provider unter "MySQL-Datenbanken" und anschließend unter "Info" oder "Bearbeiten".</p>
                       			<p><strong>MySQL-Benutzer:</strong><br> Damit ist der Benutzername gemeint welchen du eingeben musst um dich auf deinem MySQL Server einzuloggen.</p>
                       			<p><strong>MySQL-Passwort:</strong><br> Damit ist das Passwort gemeint welches du eingeben musst um dich auf deinem MySQL Server einzuloggen.</p>
                       			<p><strong>MySQL-Datenbankname:</strong><br> Auf einem MySQL-Server k&ouml;nnen mehrere Datenbanken laufen. Gebe hier an, auf welcher dieser Datenbanken das Script installiert werden soll.</p>
                       			<p><strong>MySQL-Pr&auml;fix:</strong><br> Mit dem Pr&auml;fix definiert man ein Wort/Zeichensatz, welcher ben&ouml;tigt wird, um das System mehrmals auf der gleichen MySQL-Datenbank zu installieren. Es d&uuml;rfen nur Buchstaben, Zahlen und ein _ (Unterstrich) verwendet werden.</p>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </div>