<div class="container">
    <!-- Page header/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Rang <small>Verwaltung</small></h1>
    <ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="?">&Uuml;bersicht</a></li>
  		<li class="breadcrumb-item active">Verwaltung</li>
  		<li class="breadcrumb-item"><a href="?p=ranks">Rangverwaltung</a></li>
    </ol>

	<?php echo $error; ?>
	<div class="row mb-4">
        <div class="col-md-4">
        	<?php if($loginsystem->auditRight('rank_new') && empty($f)){ ?>
                <div class="card">
                    <div class="card-header">Rang hinzuf&uuml;gen</div>
                    <div class="card-body">
                        <p>Du kannst nur Rechte vergeben und Seiten freigeben, welche du selbst hast.</p>
                        <form action="?p=ranks&c=new_rank" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" placeholder="Rangbezeichnung" maxlength="32" value="<?php echo $_POST['name'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label class="wp15">Seite/n hinzuf&uuml;gen</label>
                                <select name="sites[]" class="form-control big-multiple" multiple="multiple">
                                    <?php echo $loginsystem->getSiteOptions($_POST['sites'] ?? ''); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="wp15">Regeln hinzuf&uuml;gen</label>
                                <select name="rules[]" class="form-control big-multiple" multiple="multiple">
                                    <?php echo $loginsystem->getRuleOptions($_POST['rules'] ?? ''); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="wp15">Positionieren unter</label>
                                <select name="position" class="form-control">
                                    <?php echo $loginsystem->getRankOptions($_POST['position'] ?? ''); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Hinzufügen" class="btn btn-block btn-md btn-success">
                            </div>
                        </form>
                    </div>
				</div><!-- /.panel -->
			<?php } elseif($loginsystem->auditRight('rank_edit') && $f == 'edit_rank' && !empty($id)){ ?>
				<div class="card">
                    <div class="card-header">Rang bearbeiten <a class="btn btn-xs btn-warning pull-right mt-2px" href="?p=ranks">zur&uuml;ck</a></div>
                    <div class="card-body">
                        <p>Du kannst nur Rechte vergeben und Seiten freigeben, welche du selbst hast.</p>
                        <form action="?p=ranks&c=edit_rank&f=edit_rank&id=<?php echo $id; ?>" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" placeholder="Rangbezeichnung" maxlength="32" value="<?php echo $_POST['name'] ?? $loginsystem->getValue('ranks', 'id', $id, 'title'); ?>">
                            </div>
                            <?php if($id != '1813201541'){ // Webmaster ?>
                            <div class="form-group">
                                <label class="wp15">Seite/n </label>
                                <select name="sites[]" class="form-control big-multiple" multiple="multiple">
                                    <?php echo $loginsystem->getSiteOptions($_POST['sites'] ?? $loginsystem->getValue('ranks', 'id', $id, 'sites')); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="wp15">Regeln</label>
                                <select name="rules[]" class="form-control big-multiple" multiple="multiple">
                                    <?php echo $loginsystem->getRuleOptions($_POST['rules'] ?? $loginsystem->getValue('ranks', 'id', $id, 'rules')); ?>
                                </select>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <input type="text" class="form-control colorpicker-element" id="colorpicker" name="color" maxlength="7" value="#<?php echo $_POST['color'] ?? $loginsystem->getValue('ranks', 'id', $id, 'color'); ?>">
                            </div>
                            <div class="form-group">
                            	<label>Styles:</label>
                                    <div>
                                        <label for="inline_css_checkbox_1"><input type="checkbox" name="colorcheck" <?php if($loginsystem->getValue('ranks', 'id', $id, 'color') != '') echo 'checked';?> value="1" id="inline_css_checkbox_1">
                                        Farbe</label>
                                    </div>
                                    <div>
                                        <label for="inline_css_checkbox_2"><input type="checkbox" name="specials[]" <?php if(strpos($loginsystem->getValue('ranks', 'id', $id, 'special'), 'bold') !== false) echo 'checked';?> value="bold" id="inline_css_checkbox_2">
                                        Fett</label>
                                    </div>
                                    <div>
                                        <label for="inline_css_checkbox_3"><input type="checkbox" name="specials[]" <?php if(strpos($loginsystem->getValue('ranks', 'id', $id, 'special'), 'italic') !== false) echo 'checked';?> value="italic" id="inline_css_checkbox_3">
                                        Krusiv</label>
                                    </div>
                                    <div>
                                        <label for="inline_css_checkbox_4"><input type="checkbox" name="specials[]" <?php if(strpos($loginsystem->getValue('ranks', 'id', $id, 'special'), 'underline') !== false) echo 'checked';?> value="underline" id="inline_css_checkbox_4">
                                        Unterstrichen</label>
                                    </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Speichern" class="btn btn-block btn-md btn-success">
                            </div>
                        </form>
                    </div>
				</div><!-- /.panel -->
			<?php } elseif($loginsystem->auditRight('rank_delete') && $f == 'delete_rank' && !empty($id)){ ?>
				<div class="card">
                    <div class="card-header">Rang l&ouml;schen <a class="btn btn-xs btn-warning pull-right mt-2px" href="?p=ranks">zur&uuml;ck</a></div>
                    <div class="card-body">
                        <form action="?p=ranks&c=delete_rank&f=delete_rank&id=<?php echo $id; ?>" method="post">
                            <div class="form-group">
                                <input type="password" class="form-control" name="passwd" placeholder="Passwort" maxlength="64">
                            </div>
                            <?php if($loginsystem->getAmount('user', 'rank', $id) > 0){ ?>
                            <div class="form-group">
                            	<label>Welchen Rang sollen die Benutzer dieses Rangs erhalten?</label>
                                <select name="rank" class="form-control">
									<?php
										$del = array($id, "1813201541");
										echo $loginsystem->getRankOptions($rank, $del);
									?>
                                </select>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <input type="submit" value="L&ouml;schen" class="btn btn-block btn-md btn-danger">
                            </div>
                        </form>
                    </div>
				</div><!-- /.panel -->
			<?php } elseif($f == 'view_rank' && !empty($id)){ ?>
				<div class="card">
                    <div class="card-header">Rang einsehen <a class="btn btn-xs btn-warning pull-right mt-2px" href="?p=ranks">zur&uuml;ck</a></div>
                    <?php $rankInfo = $loginsystem->getValue('ranks', 'id', $id); ?>
					<table class="table table-striped">
                        <tbody>
                            <tr>
                                <td class="text-bold">Rangtitel:</td>
                                <td><?php echo $rankInfo['title']; ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold">Besonderheit:</td>
                                <td><?php
									if($rankInfo['default'] == '1')
										echo 'Standard Rang';
									else if($rankInfo['guest'] == '1')
										echo 'Gast Rang';
									else
										echo 'Keine';
								?></td>
                            </tr>
                            <tr>
                            	<td>Benutzer (<?php echo $loginsystem->getAmount('user', 'rank', $rankInfo['id']); ?>):</td>
                                <td><?php
									$users = $loginsystem->getValue('user', 'rank', $rankInfo['id'], 'username', true, "Order by username ASC");
									if(!is_array($users))
										echo $users;
									else
										echo implode(', ', $users);
									if(empty($users))
										echo 'Keine';
								?></td>
                            </tr>
                            <tr>
                                <td class="text-bold">Rechte/Regeln:</td>
                                <td><?php
									if($rankInfo['rules'] == 'all')
										echo 'Alle';
									elseif(empty($rankInfo['rules'])){
										echo 'Keine';
									} else {
										$rules = explode(',', str_replace(' ', '', $rankInfo['rules']));
										echo implode("<br>\n", $rules);
									}
								?></td>
                            </tr>
                            <tr>
                                <td class="text-bold">Seiten:</td>
                                <td><?php
									if($rankInfo['sites'] == 'all')
										echo 'Alle';
									elseif(empty($rankInfo['sites'])){
										echo 'Keine';
									} else {
										$sites = explode(',', str_replace(' ', '', $rankInfo['sites']));
										
										for($i = 0; $i < count($sites); $i++){
											if(substr($sites[$i], 0, 1) == 'm')
												$sites[$i] = 'Men&uuml; - '.$loginsystem->getValue('menu', 'id', substr($sites[$i], 1), 'title');
											else
												$sites[$i] = $loginsystem->getValue('sites', 'id', $sites[$i], 'title');
										}
										echo implode("<br>\n", $sites);
									}
								?></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.panel -->
            <?php } else { ?>
				<div class="card">
                    <div class="card-header">Keine Berechtigung!</div>
                    <div class="card-body">
                    	<em>Du hast keine Berechtigung diese Aktion auszuf&uuml;hren!</em>
                    </div>
                </div><!-- /.panel -->
            <?php } ?>
				<div class="card mt-4">
                    <div class="card-header"><i class="fa fa-info-circle"></i> Informationen</div>
                    <div class="card-body">
                    	<strong>Begriffe:</strong><br>
						<p>M => Men&uuml;punkt<br>
						P.-D.-M. => Pull-Down-Men&uuml;punkt<br>
						Ext. Link => Externer Link<br>
						!Nur G&auml;ste => Nur f&uuml;r die Gruppe G&auml;ste sichtbar</p>
                    </div>
                </div><!-- /.panel -->
        </div><!--- /.col-md-4 -->
        <div class="col-md-8">
        	<table class="table table-striped table-sorter table-hover">
            	<thead>
					<tr>
                    	<th class="text-center">#</th>
                        <th class="text-center">Benutzer</th>
                        <th>Rangbezichnung</th>
                        <th class="text-center">Typ</th>
                        <th>Optionen</th>
					</tr>
                </thead>
                <tbody>
					<?php echo $loginsystem->getRankList(); ?>
                </tbody>
			</table>
		</div><!-- /.col-md-8 -->
	</div><!-- /.row -->
</div>



