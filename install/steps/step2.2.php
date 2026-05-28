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
                <?php if (!empty($_SESSION['db_version'])): ?>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <div class="alert alert-success">
                            
                            Datenbankverbindung erfolgreich &ndash; Server-Version: <strong><?php echo htmlspecialchars($_SESSION['db_version'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	MySQL Tabellen
                            </div>
                            <div class="card-body">
								<table class="table table-striped">
                                	<thead>
                                    	<tr>
                                            <th>Tabellenname</th>
                                            <th>Existiert</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<?php echo $install->show_tables(); ?>
                                    </tbody>
                                </table>
                                <a class="btn btn-default" href="index.php?p=step2">zur&uuml;ck</a>
                                <a class="btn btn-primary" href="index.php?p=step2.2">Aktualisieren</a>
                                <a class="btn btn-danger" href="index.php?p=step2.2&c=mysql_del">Existierende l&ouml;schen</a>
                                <a class="btn btn-warning" href="index.php?p=step2.2&c=mysql_ignore">Existierende auslassen</a>
                       		</div>
                        </div>
                    </div>
                	<div class="col-sm-6">
                    	<div class="card">
                        	<div class="card-header">
                            	Hinweis
                            </div>
                            <div class="card-body">
                            	<p>Bitte entscheide was du nun tun m&ouml;chtes. Dir stehen folgende M&ouml;glichkeiten zur Auswahl:</p>
                                <ol>
                                	<li>Verwende Tabellen doppelt, ACHTUNG! Wenn sich das System bereits auf deiner MySQL-Datenbank l&auml;uft und auf diese Tabelle ebenfalls zugreift geschehen alle &Auml;nderungen auf beiden Systemen! (nicht empfohlen)<br><br></li>
                                    <li>L&ouml;sche bestehende Tabellen, ACHTUNG! Du k&ouml;nntest damit Tabellen anderer Systeme l&ouml;schen! Vergewissere dich bei dieser Option das die Tabelle von keinem anderem System verwendet wird. (nicht empfohlen)<br><br></li>
                                    <li>Gehe zur&uuml;ck zur Dateneingabe und &auml;ndere den Pr&auml;fix. <em>Tipp: Wenn du dieses System mehrfach nutzt, nutze das k&uuml;rzel der Seite oder nummeriere Sie durch.</em> <strong>(empfohlen)</strong></li>
                                </ol>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </div>
        
        
        
        
        
        
        
        
        
        