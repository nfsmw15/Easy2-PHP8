<div class="content-wrapper">
<div class="container">
    <!-- Page header/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Men&uuml; <small>verwalten</small></h1>
    <ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="?">&Uuml;bersicht</a></li>
  		<li class="breadcrumb-item">Verwaltung</li>
  		<li class="breadcrumb-item active">Men&uuml; verwalten</li>
    </ol>
	<?php echo $error; ?>
	<div class="row mb-4">
		<div class="col-sm-4">
			<?php if(empty($f)){ ?>
				<div class="card mb-2">
					<div class="card-header"><i class="fa fa-plus"></i> Men&uuml;punkt hinzuf&uuml;gen</div>
					<div class="card-body">
						<form action="?p=menu&c=add_menu" method="post">
							<div class="form-group">
								<label>Name des Links:</label>
								<input type="text" name="title" placeholder="Name des Links" maxlength="32" value="<?php echo htmlspecialchar(isset($_POST['title']) ? $_POST['title'] : ''); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Icon des Links:</label>
								<input type="text" name="icon" placeholder="z.B. fa-home" maxlength="32" value="<?php echo htmlspecialchar(isset($_POST['icon']) ? $_POST['icon'] : ''); ?>" class="form-control">
							</div>
							
							<!--
							<div class="form-group">
								<label>Men&uuml;:</label>
								<select name="menu" class="form-control">
									<?php 
										$getMenu = isset($_POST['menu']) ? $_POST['menu'] : NULL;
										echo $menu->getMenuGroupOptions($getMenu); 
									?>
								</select>
							</div>-->
							<div class="form-group">
								<label>Verlinkte Datei:</label>
								<select name="file" class="form-control">
									<option value="0">keine (Externer Link)</option>
									<?php 
										$getSite = isset($_POST['file']) ? $_POST['file'] : NULL;
										echo $menu->getSiteOptions($getSite); 
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Einordnen unter: <span class="wp11">(Bei auswahl wird der Link zum Pull-Down)</span></label>
								<select name="under" class="form-control">
									<option value="0">Kein &uuml;bergeordneter Link</option>
									<?php 
										$getUnder = isset($_POST['under']) ? $_POST['under'] : NULL;
										echo $menu->menuOptionsUnder($getUnder); 
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Externe URL: <span class="wp11">(Nur wenn keine Datei ausgew&auml;hlt ist)</span></label>
								<input type="text" name="url" placeholder="Externe URL" maxlength="1024" value="<?php echo htmlspecialchar(isset($_POST['url']) ? $_POST['url'] : ''); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Position:</label>
								<input type="number" name="pos" placeholder="Externe URL" maxlength="3" min="0" max="999" value="<?php echo htmlspecialchar(isset($_POST['pos']) ? $_POST['pos'] : '0'); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Link Target:</label>
								<select name="target" class="form-control">
									<?php $target = isset($_POST['target']) ? $_POST['target'] : ''; ?>
									<option <?php echo checker($target, '_self'); ?> value="_self">im selben Browser-Tab</option>
									<option <?php echo checker($target, '_blank'); ?> value="_blank">im neuen Brower-Tab</option>
								</select>
							</div>
							<div class="form-group">
								<label>
									<?php $link_type = isset($_POST['link_type']) ? $_POST['link_type'] : ''; ?>
									<input <?php echo checker($link_type, '1', 1); ?> type="checkbox" name="link_type" value="1"> Nur f&uuml;r G&auml;ste sichtbar? <span class="wp11">(Und <strong>abh&auml;ngig</strong> von der Rechteverwaltung!!!)</span>
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Hinzuf&uuml;gen</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'edit' && !empty($id)){ ?>
				<div class="card mb-2">
					<div class="card-header"><i class="fa fa-pencil"></i> Men&uuml;punkt bearbeiten <a class="float-right btn btn-sm btn-warning" href="?p=menu">Abbrechen</a></div>
					<div class="card-body">
						<form action="?p=menu&c=edit&f=edit&id=<?php echo $id; ?>" method="post">
							<div class="form-group">
								<label>Name des Links:</label>
								<input type="text" name="title" placeholder="Name des Links" maxlength="32" value="<?php echo htmlspecialchar(isset($_POST['title']) ? $_POST['title'] : $menu->getValue('menu', 'id', $id, 'title')); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Icon des Links:</label>
								<input type="text" name="icon" placeholder="z.B. fa-home" maxlength="32" value="<?php echo htmlspecialchar(isset($_POST['icon']) ? $_POST['icon'] : $menu->getValue('menu', 'id', $id, 'icon')); ?>" class="form-control">
							</div>
							
							<!--
							<div class="form-group">
								<label>Men&uuml;:</label>
								<select name="menu" class="form-control">
									<?php 
										$getMenu = isset($_POST['menu']) ? $_POST['menu'] : $menu->getValue('menu', 'id', $id, 'menu');
										echo $menu->getMenuGroupOptions($getMenu); 
									?>
								</select>
							</div>-->
							<div class="form-group">
								<label>Verlinkte Datei:</label>
								<select name="file" class="form-control">
									<option value="0">keine (Externer Link)</option>
									<?php 
										$getSite = isset($_POST['file']) ? $_POST['file'] : $menu->getValue('menu', 'id', $id, 'sid');
										echo $menu->getSiteOptions($getSite); 
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Einordnen unter: <span class="wp11">(Bei auswahl wird der Link zum Pull-Down)</span></label>
								<select name="under" class="form-control">
									<option value="0">Kein &uuml;bergeordneter Link</option>
									<?php 
										$getUnder = isset($_POST['under']) ? $_POST['under'] : $menu->getValue('menu', 'id', $id, 'under');
										echo $menu->menuOptionsUnder($getUnder); 
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Externe URL: <span class="wp11">(Nur wenn keine Datei ausgew&auml;hlt ist)</span></label>
								<input type="text" name="url" placeholder="Externe URL" maxlength="1024" value="<?php echo htmlspecialchar(isset($_POST['url']) ? $_POST['url'] : $menu->getValue('menu', 'id', $id, 'url')); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Position:</label>
								<input type="number" name="pos" maxlength="3" min="0" max="999" value="<?php echo htmlspecialchar(isset($_POST['pos']) ? $_POST['pos'] : $menu->getValue('menu', 'id', $id, 'pos')); ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Link Target:</label>
								<select name="target" class="form-control">
									<?php $target = isset($_POST['target']) ? $_POST['target'] : $menu->getValue('menu', 'id', $id, 'target'); ?>
									<option <?php echo checker($target, '_self'); ?> value="_self">im selben Browser-Tab</option>
									<option <?php echo checker($target, '_blank'); ?> value="_blank">im neuen Brower-Tab</option>
								</select>
							</div>
							<div class="form-group">
								<label>
									<?php $link_type = isset($_POST['link_type']) ? $_POST['link_type'] : $menu->getValue('menu', 'id', $id, 'link_type'); ?>
									<input <?php echo checker($link_type, '1', 1); ?> type="checkbox" name="link_type" value="1"> Nur f&uuml;r G&auml;ste sichtbar? <span class="wp11">(Und <strong>abh&auml;ngig</strong> von der Rechteverwaltung!!!)</span>
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-success"><i class="fa fa-check"></i> Speichern</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'remove' && !empty($id)){ ?>
				<div class="card mb-2">
					<div class="card-header"><i class="fa fa-trash"></i> Men&uuml;punkt l&ouml;schen <a class="float-right btn btn-sm btn-warning" href="?p=menu">Abbrechen</a></div>
					<div class="card-body">
						<form action="?p=menu&c=remove&f=remove&id=<?php echo $id; ?>" method="post">
							<div class="form-group">
								<h5>Soll der Men&uuml;punkt "<?php echo $menu->getValue('menu', 'id', $id, 'title'); ?>" wirklich gel&ouml;scht werden?</h5>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-danger"><i class="fa fa-trash"></i> Entfernen</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'reset_positions'){ ?>
				<div class="card mb-2">
					<div class="card-header"><i class="fa fa-eraser"></i> Men&uuml;positionen resetten <a class="float-right btn btn-sm btn-warning" href="?p=menu">Abbrechen</a></div>
					<div class="card-body">
						<form action="?p=menu&c=reset_positions&f=reset_positions" method="post">
							<div class="form-group">
								<h5>Sollen wirklich alle Positionen der Men&uuml;punkte zur&uuml;ckgesetzt werden?</h5>
								<p>Es wird nach Alphabet sortiert.</p>
							</div>
							<div class="form-group">
								<label>Bereich:</label>
								<select name="resetOption" class="form-control">
									<option value="all">Alle</option>
									<?php 
										$resetOption = isset($_POST['resetOption']) ? $_POST['resetOption'] : '';
										echo $menu->resetOptions($resetOption);
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Passwort:</label>
								<input type="password" name="passwd" maxlength="64" class="form-control" placeholder="Passwort zur Best&auml;tigung">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-danger"><i class="fa fa-eraser"></i> Resetten</button>
							</div>
						</form>
					</div>
				</div><!-- /.card -->
			<?php } elseif($f == 'view' && !empty($id)){ ?>
				<div class="card mb-2">
					<div class="card-header"><i class="fa fa-info-circle"></i> Men&uuml; Informationen <a class="float-right btn btn-sm btn-warning" href="?p=menu">zur&uuml;ck</a></div>
					<div class="card-body">
						<table class="table table-bordered">
							<tbody>
								<tr>
									<td>Name:</td>
									<td><?php echo $menu->getValue('menu', 'id', $id, 'title'); ?></td>
								</tr>
								<tr>
									<td>URL/Datei:</td>
									<td>
										<?php 
											$getURL = $menu->getValue('menu', 'id', $id, 'url');
											$getFILE = $menu->getValue('menu', 'id', $id, 'sid');
											if(!empty($getURL)){
												echo '<a href="'.$getURL.'" target="_blank">'.$getURL.'</a>';
											} elseif(!empty($getFILE)) {
												echo '<a href="?p='.$menu->getSite('url', $getFILE).'" target="_blank">?p='.$menu->getSite('url', $getFILE).'</a>';
											} else {
												echo 'Pull-Down';
											}
										?>
									</td>
								</tr>
								<tr>
									<td>Target:</td>
									<td><?php echo $menu->getValue('menu', 'id', $id, 'target'); ?></td>
								</tr>
								<tr>
									<td>Icon:</td>
									<td>
										<?php 
											$icon = $menu->getValue('menu', 'id', $id, 'icon');
											if(!empty($icon)){
												echo '<i class="fa '.$icon.'"></i> ('.$icon.')';
											} else {
												echo 'kein Icon';
											}
										?>
									</td>
								</tr>
								<tr>
									<td>Untergeordnet:</td>
									<td>
										<?php 
											$under = $menu->getValue('menu', 'id', $id, 'under');
											if(empty($under)){
												echo 'nein';
											} else {
												echo 'ja, '.$menu->getValue('menu', 'id', $under, 'title');
											}
										?>
									</td>
								</tr>
								<tr>
									<td>Nur f&uuml;r G&auml;ste:</td>
									<td>
										<?php 
											$onlyGuest = $menu->getValue('menu', 'id', $id, 'link_type'); 
											if($onlyGuest == 0){
												echo 'Nein';
											} else {
												echo 'Ja';
											}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div><!-- /.card -->
			<?php } ?>
			
			<?php if($loginsystem->auditRight('menu_reset_pos') && $loginsystem->auditRight('menu_fill_gaps')){ ?>
				<div class="card mt-4">
					<div class="card-header"><i class="fa fa-bullseye"></i> Weitere Aktionen</div>
					<div class="card-body">
						<?php if($loginsystem->auditRight('menu_reset_pos')){ ?>
							<a class="btn btn-block btn-danger" href="?p=menu&f=reset_positions"><i class="fa fa-eraser"></i> Positionen zur&uuml;cksetzen</a>
						<?php } ?>
						<?php if($loginsystem->auditRight('menu_fill_gaps')){ ?>
							<a class="btn btn-block btn-warning" href="?p=menu&c=fill_gaps"><i class="fa fa-arrows-v"></i> Positionen pr&uuml;fen und beheben</a>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="card mt-4">
				<div class="card-header"><i class="fa fa-info-circle"></i> Infos</div>
				<div class="card-body">
					<strong>Wie erstelle ich was?</strong><br>
					<p>Um einen Link zu einer Datei zu erstellen, muss mindestens der Name ausgef&uuml;llt und die Datei ausgew&auml;hlt werden.<br>
					Es kann aber auch ein Link zu einer externen Seite oder einem anderen Verzeichnis erstellt werden.
					Dazu muss nun auch wieder ein Name angegeben werden und eine URL wohin der Benutzer verlinkt werden soll.<br>
					Wenn du aber ein Pull-Down-Men&uuml; erstellen m&ouml;chtest musst du nur den Namen ausf&uuml;llen.
					Die Eintr&auml;ge erstellst du wie oben beschrieben, nur das du noch bei "Einordnen unter" deinen erstellten Pull-Down-Link ausw&auml;hlen musst.
					Es k&ouml;nnen nur einfache Pull-Down-Men&uuml;s erstellt werden.</p><br>
					<strong>Was ist Target?</strong><br>
					<p>Target beschreibt wie sich der Link &ouml;ffnen soll.
					Es gibt die M&ouml;glichkeit das der Link sich im selben Browser-Tab &ouml;ffnet (Standard).
					Dieser Link kann aber auch in einem neuen Browser-Tab ge&ouml;ffnet werden (z.B. f&uuml;r externe Links zu empfehlen).</p>
					<br>
					<strong>Welche Variablen kann ich nutzen?</strong><br>
					<p>Du kannst die CSRF-Variable mit " [csrf] " verwenden, um deine Links sicherer zu machen. Zum Beispiel f&uuml;r eigene Funktionen oder &auml;hnlichem.
					<a href="https://de.wikipedia.org/wiki/Cross-Site-Request-Forgery" target="_blank">Was ist CSRF?</a></p>
					<strong>Positionen zur&uuml;cksetzen</strong><br>
					<p>Diese Funktion setzt alle Positionsnummern zur&uuml;ck. 
						Sie sollte verwendet werden, wenn durch Sriptfehler ein Nummernchaos entstanden ist, 
						welches mit der "Positionen pr&uuml;fen und beheben" Funktion nicht reparabel ist.
					Um diese Funktion auszuf&uuml;hren muss der Benutzer das Recht "menu_reset_pos" besitzen und sein Passwort zur Best&auml;tigung eingeben.</p>
					<strong>Positionen pr&uuml;fen und beheben</strong><br>
					<p>Diese Funktion pr&uuml;ft deine Men&uuml;s auf Fehler mit den Positionsnummern.<br>
					Diese erkennt doppelte Positionsnummern, L&uuml;cken und negative Positionsnummern und r&uuml;ckt diese wieder in die richtige Form.
					Bei doppelten Eintr&auml;gen sollte &uuml;berpr&uuml;ft werden ob die Men&uuml;punkte noch in der gew&uuml;nschten Reihenfolge sind.<br>
					Falls du also ein kleines Nummernchaos verursacht haben solltest (oder das Script), 
						kannst du diese Funktion verwenden. Sollte diese nicht helfen verwende bitte die Funktion "Positionen zur&uuml;cksetzen".</p>
				</div>
			</div>
		</div><!-- /.col-sm-4 -->
		<div class="col-sm-8">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Name</th>
							<th>Pfad</th>
							<th>Aktionen</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $menu->listMenu(); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div><!-- /.row -->
</div>
</div><!-- /.content-wrapper -->
