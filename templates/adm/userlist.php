<div class="container full-container">
    <!-- Page header/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Benutzer <small>Verwaltung</small></h1>
    <div class="bg-body-tertiary rounded-2 px-3 py-2 mb-3">
<ol class="breadcrumb mb-0">
    	<li class="breadcrumb-item"><a href="?">&Uuml;bersicht</a></li>
  		<li class="breadcrumb-item">Verwaltung</li>
  		<li class="breadcrumb-item active">Benutzer Verwaltung</li>
    </ol>
</div>
    <?php echo $error; ?>
	<div class="row mb-4">
    	<div class="col-md-6 col-lg-8">
        	<div class="card">
            	<div class="card-header">
                	<i class="fa fa-users"></i> Benutzer
                </div>
                <div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="data-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Benutzername</th>
									<th>Name</th>
									<th>Rang</th>
									<th>E-Mail Adresse</th>
									<th>Aktionen</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $loginsystem->listAllUsers(); ?>
							</tbody>
						</table>
					</div>
                </div>
            </div>
        </div>
    	<div class="col-md-6 col-lg-4">
        	<?php if(empty($f)){ ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-user-plus"></i> Benutzer hinzuf&uuml;gen
                    </div>
                    <div class="card-body card-form">
						<?php if($loginsystem->auditRight('user_add')){ ?>
                            <form action="?p=userlist&c=new" method="post" class="form-horizontal form-bordered">
                            <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <label>Benutzername:</label>
                                    <input type="text" class="form-control" value="<?php echo isset($_POST['new-username']) ? $_POST['new-username'] : ''; ?>" name="new-username" maxlength="64" placeholder="Benutzername" required>
                                </div>
                                <div class="form-group">
                                    <label>E-Mail Adresse:</label>
                                    <input type="email" class="form-control" value="<?php echo isset($_POST['new-email']) ? $_POST['new-email'] : ''; ?>" name="new-email" maxlength="64" placeholder="E-Mail Adresse" required>
                                </div>
                                <div class="form-group">
                                    <label>Vor-/Nachname:</label>
                                    <input type="text" class="form-control" value="<?php echo isset($_POST['new-fullname']) ? $_POST['new-fullname'] : ''; ?>" name="new-fullname" maxlength="64" placeholder="Vor-/Namename" required>
                                </div>
                                <?php if(!empty($webmaster_confirm)){ ?>
                                <input type="hidden" name="new-password" value="<?php echo htmlspecialchars($_POST['new-password'] ?? ''); ?>">
                                <input type="hidden" name="new-password-confirm" value="<?php echo htmlspecialchars($_POST['new-password-confirm'] ?? ''); ?>">
                                <div class="form-group">
                                    <label>Passwort:</label>
                                    <input type="password" class="form-control" placeholder="(bereits eingegeben)" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Passwort best&auml;tigen:</label>
                                    <input type="password" class="form-control" placeholder="(bereits eingegeben)" disabled>
                                </div>
                                <?php } else { ?>
                                <div class="form-group">
                                    <label>Passwort:</label>
                                    <input type="password" class="form-control" name="new-password" maxlength="64" placeholder="Passwort" required>
                                </div>
                                <div class="form-group">
                                    <label>Passwort best&auml;tigen:</label>
                                    <input type="password" class="form-control" name="new-password-confirm" maxlength="64" placeholder="Passwort best&auml;tigen" required>
                                </div>
                                <?php } ?>
								<?php if($loginsystem->auditRight('user_rank')){ ?>
                                    <div class="form-group">
                                        <label>Rang:</label>
                                            <?php 
                                                $rank = isset($_POST['new-rank']) ? $_POST['new-rank'] : ''; 
                                            ?>
                                            <select class="form-control" name="new-rank">
                                                <?php echo $loginsystem->getRankOptions($rank); ?>
                                            </select>
                                    </div>
                                <?php } ?>
								<?php 
									echo $additional_fields->showFields(0, 'new-');
								?>
                                <div class="form-group">
                                        <input type="checkbox" id="send-mail" name="new-send-email" value="1" <?php echo !empty($_POST['new-send-email']) ? 'checked' : ''; ?>>
                                        <label for="send-mail" class="cursor">
                                            Anmeldedaten versenden
                                        </label>
                                </div>
                                <?php if(!empty($webmaster_confirm)){ ?>
                                <input type="hidden" name="confirm_webmaster" value="1">
                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-warning"></i> Achtung: Mehrere Webmaster-Accounts!</strong><br>
                                    Es existiert bereits ein Webmaster-Account. Beachte:
                                    <ul class="mb-2 mt-1">
                                        <li>Webmaster k&ouml;nnen sich gegenseitig l&ouml;schen und aussperren</li>
                                        <li>Jeder Webmaster kann weitere Webmaster-Accounts anlegen</li>
                                        <li>Alle Webmaster haben vollen Zugriff auf das gesamte System</li>
                                    </ul>
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <?php if(!empty($webmaster_confirm)){ ?>
                                        <a class="btn btn-secondary float-start" href="?p=userlist"><i class="fa fa-times"></i> Abbrechen</a>
                                        <button class="btn btn-warning float-end" type="submit"><i class="fa fa-check"></i> Ich habe verstanden &mdash; Trotzdem anlegen</button>
                                    <?php } else { ?>
                                        <button class="btn btn-success float-end" type="submit"><i class="fa fa-user-plus"></i> Anlegen</button>
                                    <?php } ?>
                                </div>
                            </form>
                        <?php } else echo '<em>Keine Berechtigung Benutzer hinzuzuf&uuml;gen durchzuf&uuml;hren!</em>';	?>
                    </div>
                </div><!-- /.card -->
        	<?php } elseif($f == 'show' && !empty($id) && $loginsystem->auditRight('user_show')){ ?>
                <div class="card">
                    <div class="card-header">
						<div class="btn-group float-end">
							<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">Aktionen <span class="caret"></span></button>
							<ul class="dropdown-menu" role="menu">
                            	<?php if($loginsystem->auditRight('user_edit') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
									<li><a href="?p=userlist&f=edit&id=<?php echo $id; ?>"><i class="fa fa-pencil fa-fw"></i> Bearbeiten</a></li>
                                <?php } ?>
                                <?php if($loginsystem->getUser('active', $id) == 0){ ?>
                                	<?php if($loginsystem->auditRight('user_enable') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
										<li><a href="?p=userlist&c=activate&id=<?php echo $id; ?>&csrf=<?php echo $loginsystem->getData('csrfToken'); ?>"><i class="fa fa-check fa-fw"></i> Aktivieren</a></li>
                                    <?php } ?>
                                <?php } else { ?>
                                	<?php if($loginsystem->auditRight('user_disable') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
										<li><a href="?p=userlist&c=deactivate&id=<?php echo $id; ?>&csrf=<?php echo $loginsystem->getData('csrfToken'); ?>"><i class="fa fa-ban fa-fw"></i> Deaktivieren</a></li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if($loginsystem->auditRight('user_pwreset') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
									<li><a href="?p=userlist&f=pwreset&id=<?php echo $id; ?>"><i class="fa fa-refresh fa-fw"></i> Passwort zur&uuml;cksetzen</a></li>
                               	<?php } ?>
                                <?php if($loginsystem->auditRight('user_rm_avatar') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
									<li><a href="?p=userlist&f=rm_avatar&id=<?php echo $id; ?>"><i class="fa fa-eraser fa-fw"></i> Profilbild entfernen</a></li>
                               	<?php } ?>
                                <?php if($loginsystem->auditRight('user_delete') && $loginsystem->checkRank($loginsystem->getUser('rank', $id)) && $loginsystem->getUser('id') != $id){ ?>
                                	<li class="divider"></li>
									<li><a href="?p=userlist&f=delete&id=<?php echo $id; ?>"><i class="fa fa-trash fa-fw"></i> L&ouml;schen</a></li>
                                <?php } ?>
                                <?php if($loginsystem->auditRight('user_add')){ ?>
                                	<li class="divider"></li>
									<li><a href="?p=userlist"><i class="fa fa-user-plus fa-fw"></i> Benutzer hinzuf&uuml;gen</a></li>
                                <?php } ?>
							</ul>
						</div>
						<i class="fa fa-info-circle"></i> Benutzer Informationen
                    </div>
                    	<table class="table table-striped">
                        	<tbody>
                            	<tr>
                                	<td class="text-bold">Profilbild:</td>
                                    <td><img src="<?php echo $loginsystem->getUserAvatar($id); ?>" alt="avatar" class="img-responsive box-center" style="max-height:200px; max-width:200px;"></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">Benutzername:</td>
                                    <td><?php echo $loginsystem->getUser('username', $id); ?></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">Name:</td>
                                    <td><?php echo $loginsystem->getUser('first_name', $id); ?> <?php echo $loginsystem->getUser('last_name', $id); ?></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">E-Mail Adresse:</td>
                                    <td><?php echo $loginsystem->getUser('email', $id); ?></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">Rang:</td>
                                    <td><?php echo $loginsystem->getRank($id, true); ?></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">Aktiv:</td>
                                    <td><?php echo ($loginsystem->getUser('active', $id) == 0) ? '<span class="text-danger">Nein</span>' : '<span class="text-success">Ja</span>'; ?></td>
                                </tr>
                            	<tr>
                                	<td class="text-bold">Dabei seit:</td>
                                    <td><?php echo date('d.m.Y H:i', $loginsystem->getUser('regdate', $id)); ?></td>
                                </tr>
 								<?php 
									echo $additional_fields->getFieldValues($id);
								?>
                           </tbody>
                        </table>
				</div><!-- /.card -->
        	<?php } elseif($f == 'edit' && !empty($id) && $loginsystem->auditRight('user_edit') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-pencil"></i> Benutzer bearbeiten
                    </div>
                    <div class="card-body card-form">
                        <form action="?p=userlist&c=edit&f=edit&id=<?php echo $id; ?>" method="post" class="form-horizontal form-bordered">
                        <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Benutzername:</label>
                                <input type="text" class="form-control" value="<?php echo isset($_POST['edit-username']) ? $_POST['edit-username'] : $loginsystem->getUser('username', $id); ?>" name="edit-username" maxlength="64" placeholder="Benutzername" required>
                            </div>
                            <div class="form-group">
                                <label>E-Mail Adresse:</label>
                                <input type="email" class="form-control" value="<?php echo isset($_POST['edit-email']) ? $_POST['edit-email'] : $loginsystem->getUser('email', $id); ?>" name="edit-email" maxlength="64" placeholder="E-Mail Adresse" required>
                            </div>
                            <div class="form-group">
                                <label>Vor-/Nachname:</label>
                                <input type="text" class="form-control" value="<?php echo isset($_POST['edit-fullname']) ? $_POST['edit-fullname'] : $loginsystem->getUser('first_name', $id).' '.$loginsystem->getUser('last_name', $id); ?>" name="edit-fullname" maxlength="64" placeholder="Vor-/Namename" required>
                            </div>
                            <?php if($loginsystem->auditRight('user_rank') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
                                <div class="form-group">
                                    <label>Rang:</label>
                                        <?php 
                                            $rank = isset($_POST['edit-rank']) ? $_POST['edit-rank'] : $loginsystem->getUser('rank', $id); 
                                        ?>
                                        <select class="form-control" name="edit-rank">
                                        	<?php echo $loginsystem->getRankOptions($rank); ?>
                                        </select>
                                </div>
                            <?php } ?>
 							<?php 
								echo $additional_fields->showFields(0, 'edit-', $id);
							?>
                            <?php if(!empty($webmaster_confirm)){ ?>
                            <input type="hidden" name="confirm_webmaster" value="1">
                            <div class="alert alert-warning">
                                <strong><i class="fa fa-warning"></i> Achtung: Mehrere Webmaster-Accounts!</strong><br>
                                Es existiert bereits ein Webmaster-Account. Beachte:
                                <ul class="mb-2 mt-1">
                                    <li>Webmaster k&ouml;nnen sich gegenseitig l&ouml;schen und aussperren</li>
                                    <li>Jeder Webmaster kann weitere Webmaster-Accounts anlegen</li>
                                    <li>Alle Webmaster haben vollen Zugriff auf das gesamte System</li>
                                </ul>
                            </div>
                            <?php } ?>
                           <div class="form-group">
                                <?php if(!empty($webmaster_confirm)){ ?>
                                    <a class="btn btn-secondary float-start" href="?p=userlist"><i class="fa fa-times"></i> Abbrechen</a>
                                    <button class="btn btn-warning float-end" type="submit"><i class="fa fa-check"></i> Ich habe verstanden &mdash; Trotzdem speichern</button>
                                <?php } else { ?>
                                    <a class="btn btn-warning float-start" href="#" data-history-back><i class="fa fa-times"></i> Abbrechen</a>
                                    <button class="btn btn-success float-end" type="submit"><i class="fa fa-floppy-o"></i> Speichern</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card -->
        	<?php } elseif($f == 'delete' && !empty($id) && $loginsystem->auditRight('user_delete') && $loginsystem->checkRank($loginsystem->getUser('rank', $id)) && $loginsystem->getUser('id') != $id){ ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-trash"></i> Benutzer l&ouml;schen
                    </div>
                    <div class="card-body card-form">
                        <form action="?p=userlist&c=delete&f=delete&id=<?php echo $id; ?>" method="post" class="form-horizontal form-bordered">
                        <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <div class="col-sm-12 text-center f-s-16">
                                	M&ouml;chtest du den Benutzer "<?php echo $loginsystem->getUser('username', $id); ?>" wirklich l&ouml;schen?
                                </div>
                                <div class="col-sm-12 text-center f-s-12">
                                	Um den Vorgang abzuschlie&szlig;en, gebe bitte dein Passwort zur Best&auml;tigung ein.
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Passwort:</label>
                                <input type="password" class="form-control" name="delete-password" maxlength="64" placeholder="Passwort" required>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-success float-start" href="#" data-history-back><i class="fa fa-times"></i> Abbrechen</a>
                                <button class="btn btn-danger float-end" type="submit"><i class="fa fa-trash"></i> L&ouml;schen</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card -->
        	<?php } elseif($f == 'pwreset' && !empty($id) && $loginsystem->auditRight('user_pwreset') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-refresh"></i> Benutzerpasswort zur&uuml;cksetzen
                    </div>
                    <div class="card-body card-form">
                        <form action="?p=userlist&c=pwreset&f=pwreset&id=<?php echo $id; ?>" method="post" class="form-horizontal form-bordered">
                        <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <div class="col-sm-12 text-center f-s-14">
                                	M&ouml;chtest du das Passwort von "<?php echo $loginsystem->getUser('username', $id); ?>" wirklich zur&uuml;cksetzen?
                                </div>                                
                                <div class="col-sm-12 text-center f-s-12">
                                	Um den Vorgang abzuschlie&szlig;en, gebe bitte dein Passwort zur Best&auml;tigung ein.
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Passwort:</label>
                                <input type="password" class="form-control" name="reset-password" maxlength="64" placeholder="Passwort" required>
                            </div>
                            <div class="form-group">
                               	<a class="btn btn-success float-start" href="#" data-history-back><i class="fa fa-times"></i> Abbrechen</a>
                                <button class="btn btn-danger float-end" type="submit"><i class="fa fa-refresh"></i> Zur&uuml;cksetzen</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card -->
        	<?php } elseif($f == 'rm_avatar' && !empty($id) && $loginsystem->auditRight('user_rm_avatar') && $loginsystem->checkRank($loginsystem->getUser('rank', $id))){ ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-refresh"></i> Profilbild entfernen
                    </div>
                    <div class="card-body card-form">
                        <form action="?p=userlist&c=rm_avatar&f=rm_avatar&id=<?php echo $id; ?>" method="post" class="form-horizontal form-bordered">
                        <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <div class="col-sm-12 text-center f-s-14">
                                	M&ouml;chtest du das Profilbild von "<?php echo $loginsystem->getUser('username', $id); ?>" wirklich entfernen?
                                </div>                                
                                <div class="col-sm-12 text-center f-s-12">
                                	Um den Vorgang abzuschlie&szlig;en, gebe bitte dein Passwort zur Best&auml;tigung ein.
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Passwort:</label>
                                <input type="password" class="form-control" name="reset-password" maxlength="64" placeholder="Passwort" required>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-success float-start" href="#" data-history-back><i class="fa fa-times"></i> Abbrechen</a>
                                <button class="btn btn-danger float-end" type="submit"><i class="fa fa-eraser"></i> Entfernen</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.card -->
            <?php } ?>
        </div><!-- /.col-lg-4 -->
    </div>
</div>