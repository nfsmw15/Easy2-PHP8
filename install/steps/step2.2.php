        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Installation - Step 2</h1>
                        <ol class="breadcrumb float-right">
                            <li><a href="index.php">Einleitung</a></li>
                            <li><a href="index.php?p=terms_of_use">Nutzungsbedingungen</a></li>
                            <li><a href="index.php?p=step1">Step 1 - Voraussetzungen</a></li>
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
                    	<div class="panel panel-primary">
                        	<div class="panel-heading">
                            	MySQL Tabellen
                            </div>
                            <div class="panel-body">
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
                                <a class="btn btn-default" href="index.php?p=step2"><i class="fa fa-arrow-left"></i> zur&uuml;ck</a>
                                <a class="btn btn-primary" href="index.php?p=step2.2"><i class="fa fa-refresh"></i> Aktualisieren</a>
                                <a class="btn btn-danger" href="index.php?p=step2.2&c=mysql_del"><i class="fa fa-trash"></i> Existierende l&ouml;schen</a>
                                <a class="btn btn-warning" href="index.php?p=step2.2&c=mysql_ignore"><i class="fa fa-times"></i> Existierende auslassen</a>
                       		</div>
                        </div>
                    </div>
                	<div class="col-sm-6">
                    	<div class="panel panel-primary">
                        	<div class="panel-heading">
                            	Hinweis
                            </div>
                            <div class="panel-body">
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
        
        
        
        
        
        
        
        
        
        