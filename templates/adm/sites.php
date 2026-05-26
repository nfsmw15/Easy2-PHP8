<div class="container">
    <!-- Page Heading/Breadcrumbs -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <i class="fa fa-file"></i> Seiten
                <small>verwalten</small>
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="./">&Uuml;bersicht</a>
                </li>
                <li>Verwaltung</li>
                <li class="active">Seiten verwalten</li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
	<?php echo $error; ?>
	<div class="row">
		<div class="col-sm-4">
			<?php if(empty($f)){ ?>
				<div class="panel panel-primary">
					<div class="panel-heading"><i class="fa fa-plus"></i> Seite hinzuf&uuml;gen</div>
					<div class="panel-body">
						<form action="./?p=sites&c=add_site" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label>Seitentitel:</label>
								<input type="text" name="title" placeholder="Seitentitel" maxlength="32" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : ''; ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Dateiname:</label>
								<input type="text" name="filename" placeholder="Dateiname" maxlength="64" value="<?php echo isset($_POST['filename']) ? htmlspecialchars($_POST['filename'], ENT_QUOTES, 'UTF-8') : ''; ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Datei hochladen:</label>
								<input type="file" name="file">
							</div>
							<div class="form-group">
								<label>Verzeichnis:</label>
								<input type="text" name="dir" placeholder="Verzeichnis" maxlength="128" value="<?php echo isset($_POST['dir']) ? htmlspecialchars($_POST['dir'], ENT_QUOTES, 'UTF-8') : ''; ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" name="start_site" value="1" <?php echo checker(isset($_POST['start_site']) ? $_POST['start_site'] : 0, 1, 1); ?>> Startseite
								</label><br>
								<label>
									<input type="checkbox" name="start_site_login" value="1" <?php echo checker(isset($_POST['start_site_login']) ? $_POST['start_site_login'] : 0, 1, 1); ?>> Startseite nach dem Login
								</label><br>
								<label>
									<input type="checkbox" name="logout_site" value="1" <?php echo checker(isset($_POST['logout_site']) ? $_POST['logout_site'] : 0, 1, 1); ?>> Logoutseite
								</label><br>
								<label>
									<input type="checkbox" name="error_site" value="1" <?php echo checker(isset($_POST['error_site']) ? $_POST['error_site'] : 0, 1, 1); ?>> Fehlerseite
								</label>
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" name="file_check" value="1"> Pr&uuml;fe ob Datei und Verzeichnis existiert?
								</label><br>
								<label>
									<input type="checkbox" name="create_site" value="1"> Seite mit Datei und Verzeichnis anlegen (wenn nicht vorhanden)?
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Hinzuf&uuml;gen</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'edit' && !empty($id)){ ?>
				<div class="panel panel-primary">
					<div class="panel-heading"><i class="fa fa-pencil"></i> Seite bearbeiten <a class="pull-right btn btn-xs btn-warning" href="./?p=sites">Abbrechen</a></div>
					<div class="panel-body">
						<form action="./?p=sites&c=edit&f=edit&id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label>Seitentitel:</label>
								<input type="text" name="title" placeholder="Seitentitel" maxlength="32" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : $sites->getValue('sites', 'id', $id, 'title'); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Dateiname:</label>
								<input type="text" name="filename" placeholder="Dateiname" maxlength="64" value="<?php echo isset($_POST['filename']) ? htmlspecialchars($_POST['filename'], ENT_QUOTES, 'UTF-8') : $sites->getSite('complete_filename', $id); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Datei hochladen:</label>
								<input type="file" name="file">
							</div>
							<div class="form-group">
								<label>Verzeichnis:</label>
								<input type="text" name="dir" placeholder="Verzeichnis" maxlength="128" value="<?php echo isset($_POST['dir']) ? htmlspecialchars($_POST['dir'], ENT_QUOTES, 'UTF-8') : $sites->getValue('sites', 'id', $id, 'dir'); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" name="start_site" value="1" <?php echo checker(isset($_POST['start_site']) ? $_POST['start_site'] : $sites->getValue('sites', 'id', $id, 'start_site'), 1, 1); ?>> Startseite
								</label><br>
								<label>
									<input type="checkbox" name="start_site_login" value="1" <?php echo checker(isset($_POST['start_site_login']) ? $_POST['start_site_login'] : $sites->getValue('sites', 'id', $id, 'start_site_login'), 1, 1); ?>> Startseite nach dem Login
								</label><br>
								<label>
									<input type="checkbox" name="logout_site" value="1" <?php echo checker(isset($_POST['logout_site']) ? $_POST['logout_site'] : $sites->getValue('sites', 'id', $id, 'logout_site'), 1, 1); ?>> Logoutseite
								</label><br>
								<label>
									<input type="checkbox" name="error_site" value="1" <?php echo checker(isset($_POST['error_site']) ? $_POST['error_site'] : $sites->getValue('sites', 'id', $id, 'errorsite'), 1, 1); ?>> Fehlerseite
								</label>
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" name="file_check" value="1"> Pr&uuml;fe ob Datei und Verzeichnis existiert?
								</label>
								<label>
									<input type="checkbox" name="create_site" value="1"> Seite mit Datei und Verzeichnis anlegen (wenn nicht vorhanden)?
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-success"><i class="fa fa-check"></i> Speichern</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'remove' && !empty($id)){ ?>
				<div class="panel panel-primary">
					<div class="panel-heading"><i class="fa fa-pencil"></i> Seite entfernen <a class="pull-right btn btn-xs btn-warning" href="./?p=sites">Abbrechen</a></div>
					<div class="panel-body">
						<form action="./?p=sites&c=remove&f=remove&id=<?php echo $id; ?>" method="post">
							<div class="form-group">
								<h5>Soll die Seite "<?php echo $sites->getSite('complete_filename', $id);?>" wirklich entfernt werden?</h5>
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" checked name="delete_file" value="1"> Soll die Datei mit entfernt werden?
								</label>
								<label>
									<input type="checkbox" name="delete_dir" value="1"> Soll das Verzeichnis mit entfernt werden (Nur bei Unterverzeichnissen m&ouml;glich)?
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-danger"><i class="fa fa-trash"></i> Entfernen</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'check' && !empty($id)){ ?>
				<div class="panel panel-primary">
					<div class="panel-heading"><i class="fa fa-info-circle"></i> Seiten Informationen <a class="pull-right btn btn-xs btn-warning" href="./?p=sites">zur&uuml;ck</a></div>
					<div class="panel-body">
						<?php 
							$filename = $sites->getSite('complete_filename', $id);
							$dir	  = $sites->getValue('sites', 'id', $id, 'dir');
							$specials = array();
							if($sites->getValue('sites', 'id', $id, 'start_site') == 1) 
								$specials[] = '<i class="fa fa-home" title="Startseite"></i>';
							if($sites->getValue('sites', 'id', $id, 'start_site_login') == 1) 
								$specials[] = '<i class="fa fa-lock" title="Startseite nach dem Login"></i>';
							if($sites->getValue('sites', 'id', $id, 'errorsite') == 1) 
								$specials[] = '<i class="fa fa-flash" title="Fehlerseite"></i>';
							if($sites->getValue('sites', 'id', $id, 'logout_site') == 1) 
								$specials[] = '<i class="fa fa-power-off" title="Logoutseite"></i>';
							
						?>
						<table class="table table-bordered">
							<tr>
								<td><strong>Datei:</strong></td>
								<td><?php echo $filename; ?></td>
							</tr>
							<tr>
								<td>Exsistiert:</td>
								<td><?php echo file_exists('./templates/'.$dir.$filename) ? '<span class="text-success">ja</span>' : '<span class="text-danger">nein</span>';?></td>
							</tr>
							<tr>
								<td>Lesbar:</td>
								<td><?php echo is_readable('./templates/'.$dir.$filename) ? '<span class="text-success">ja</span>' : '<span class="text-danger">nein</span>';?></td>
							</tr>
							<tr>
								<td>Rechte:</td>
								<td><?php echo file_exists('./templates/'.$dir.$filename) ? getFilePermission('./templates/'.$dir.$filename) : '-'; ?></td>
							</tr>
							<tr>
								<td>Gr&ouml;&szlig;e:</td>
								<td><?php echo file_exists('./templates/'.$dir.$filename) ? count_size(filesize('./templates/'.$dir.$filename)) : '-'; ?></td>
							</tr>
							<tr>
								<td>Specials:</td>
								<td><?php echo implode(' | ', $specials); ?></td>
							</tr>
						</table>
						<hr>
						<table class="table table-bordered">
							<tr>
								<td><strong>Verzeichnis:</strong></td>
								<td>./<?php echo $dir; ?></td>
							</tr>
							<tr>
								<td>Exsistiert:</td>
								<td><?php echo file_exists('./templates/'.$dir) ? '<span class="text-success">ja</span>' : '<span class="text-danger">nein</span>';?></td>
							</tr>
							<tr>
								<td>Lesbar:</td>
								<td><?php echo is_readable('./templates/'.$dir) ? '<span class="text-success">ja</span>' : '<span class="text-danger">nein</span>';?></td>
							</tr>
							<tr>
								<td>Rechte:</td>
								<td><?php echo file_exists('./templates/'.$dir) ? getFilePermission('./templates/'.$dir) : '-'; ?></td>
							</tr>
						</table>
					</div>
				</div><!-- /.card -->
			<?php } ?>
			<div class="panel panel-primary mt15px">
				<div class="panel-heading"><i class="fa fa-info-circle"></i> Infos</div>
				<div class="panel-body">
					<strong>Wie f&uuml;ge ich eine Seite hinzu?</strong><br>
					<p>Um eine Seite hinzuzuf&uuml;gen, musst du diese zuerst mit einem Editor erstellen.
						Anschließend f&uuml;gst du diese in das Verzeichnis "./templates/" ein.
						Du kannst die Datei auch in Unterverezichnisse von "./templates/" legen.
						Nun f&uuml;lle das obere Formular wie folgt aus:<br>
					<ol>
						<li>Vergebe einen Seitennamen "Titel", also wie deine Seite hei&szlig;en soll.</li>
						<li>Gebe den Dateinamen ein, wie z.B.: "beispiel.php", optional kannst du eine bereits erstelle Datei mit hochladen</li>
						<li>Gebe das Unterverzeichnis ein, in welcher deine Datei liegt, z.B.: "test/" entspricht "./templates/test/meineDatei.php". (Liegt die Datei im Verzeichnis "./templates/", Feld frei lassen)</li>
						<li>Lege fest ob dies die Startseite und/oder die Startseite nach dem Login werden soll.</li>
						<li>Dr&uuml;cke auf hinzuf&uuml;gen</li>
						<li>F&uuml;ge nun die Seite den entsprechenden Rechtegruppen zu (auch der Gruppe "Gast"). (Sonst kann nur der Webadministrator die Seite sehen!!!)</li>
					</ol></p>
				</div>
			</div>
		</div><!-- /.col-sm-4 -->
		<div class="col-sm-8">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Datei</th>
							<th>Specials</th>
							<th>Aktionen</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $sites->listSites(); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div><!-- /.row -->
</div>



