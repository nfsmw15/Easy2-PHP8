<div class="content-wrapper">
<div class="container">

	<!-- Page Heading/Breadcrumbs -->
	<h1 class="mt-4 mb-3">Profil
		<small><?php echo $loginsystem->getUser('username'); ?></small>
	</h1>

	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="?">Home</a></li>
		<li class="breadcrumb-item active">Profil</li>
	</ol>
	
        <div class="row">
            <div class="col-sm-3">
				<img src="<?php echo $loginsystem->getUserAvatar(); ?>" alt="avatar" class="img-responsive box-center">
				<br>
				<?php if(!$loginsystem->isTopRank()){ ?>
				<a class="btn btn-block btn-danger" href="?p=profil&f=remove_self">Konto l&ouml;schen</a>
				<?php } ?>
            </div><!-- /.col-sm-3 -->
            <div class="col-sm-9 mb-2">
				<div class="card">
					<div class="card-header"><i class="fa fa-user"></i> Informationen</div>
					<div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td class="text-bold">Benutzername:</td>
                                        <td><?php echo $loginsystem->getUser('username'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold">Vor-/Nachname:</td>
                                        <td><?php echo $loginsystem->getUser('first_name'); ?> <?php echo $loginsystem->getUser('last_name'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold">E-Mail Adresse:</td>
                                        <td><?php echo $loginsystem->getUser('email'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold">Rang: </td>
                                        <td><?php echo $loginsystem->getRank(NULL, true); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold">Dabei seit: </td>
                                        <td><?php echo date('d.m.Y', $loginsystem->getUser('regdate')); ?></td>
                                    </tr>
									<?php 
										echo $additional_fields->getFieldValues($loginsystem->getUser('id'));
									?>
                               </tbody>
                            </table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
            </div><!-- /.col-sm-9 -->
		</div>
        <?php echo $error; ?>
		<?php if(empty($f)){ ?>
			<div class="row mb-2">
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							Passwort &auml;ndern
						</div>
						<div class="card-body">
							<form action="?p=profil&c=passwd_change" method="post">
<?php echo csrf_field(); ?>
								<div class="form-group">
									<label>Neues Passwort: </label>
									<input type="password" name="password" class="form-control" placeholder="Neues Passwort" required="">
								</div>
								<div class="form-group">
									<label>Neues Passwort best&auml;tigen: </label>
									<input type="password" name="password-confirm" class="form-control" placeholder="Neues Passwort best&auml;tigen" required="">
								</div>
								<div class="form-group">
									<label>Aktuelles Passwort: </label>
									<input type="password" name="password-actual" class="form-control" placeholder="Aktuelles Passwort" required="">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success">Passwort &auml;ndern</button>
								</div>
							</form>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							E-Mail &auml;ndern
						</div>
						<div class="card-body">
							<form action="?p=profil&c=email_change" method="post">
<?php echo csrf_field(); ?>
								<div class="form-group">
									<label>Neue E-Mail: </label>
									<input type="email" name="email" class="form-control" placeholder="Neue E-Mail" required="">
								</div>
								<div class="form-group">
									<label>Neue E-Mail best&auml;tigen: </label>
									<input type="email" name="email-confirm" class="form-control" placeholder="Neue E-Mail best&auml;tigen" required="">
								</div>
								<div class="form-group">
									<label>Aktuelles Passwort: </label>
									<input type="password" name="password-actual" class="form-control" placeholder="Aktuelles Passwort" required="">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success">E-Mail &auml;ndern</button>
								</div>
							</form>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							Benutzerdaten &auml;ndern
						</div>
						<div class="card-body">
							<form action="?p=profil&c=data_change" method="post">
<?php echo csrf_field(); ?>
								<div class="form-group">
									<label>Benutzername: </label>
									<input type="text" name="edit-username" class="form-control" value="<?php echo htmlspecialchar(isset($_POST['edit-username']) ? $_POST['edit-username'] : $loginsystem->getUser('username')); ?>" placeholder="Benutzername" required="">
								</div>
								<div class="form-group">
									<label>Vor-/Nachname: </label>
									<input type="text" name="edit-fullname" class="form-control" value="<?php echo htmlspecialchar(isset($_POST['edit-fullname']) ? $_POST['edit-fullname'] : $loginsystem->getUser('first_name').' '.$loginsystem->getUser('last_name')); ?>" placeholder="Vor-/Nachname" required="">
								</div>
								<?php 
									echo $additional_fields->showFields(0, 'edit-', $loginsystem->getUser('id'));
								?>
								<div class="form-group">
									<label>Aktuelles Passwort: </label>
									<input type="password" name="password-actual" class="form-control" placeholder="Aktuelles Passwort" required="">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success">Daten &auml;ndern</button>
								</div>
							</form>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							Profilbild &auml;ndern
						</div>
						<div class="card-body">
							<form action="?p=profil&c=avatar_change" method="post" enctype="multipart/form-data">
<?php echo csrf_field(); ?>
								<div class="form-group">
									<label>Bild ausw&auml;hlen: <span class="wp11">ideal: 200x200px, max 1MB</span></label>
									<input type="file" name="avatar-file" class="form-control" accept="image/*">
								</div>
								<div class="form-group">
									<label>Bild entfernen: </label><br>
									<label><input type="checkbox" name="avatar-remove" value="delete"> Ja, Bild entfernen</label>
								</div>                            
								<div class="form-group">
									<label>Aktuelles Passwort: </label>
									<input type="password" name="password-actual" class="form-control" placeholder="Aktuelles Passwort" required="">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success">Bild &auml;ndern</button>
								</div>
							</form>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
	        </div><!-- /.row -->
		<?php } elseif($f == 'remove_self' && !$loginsystem->isTopRank()){ ?>
       		<div class="row mb-2">
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							Konto l&ouml;schen
						</div>
						<div class="card-body">
							<form action="?p=profil&c=remove_self&f=remove_self" method="post">
<?php echo csrf_field(); ?>
								<div class="form-group">
									<h5>Bist du sicher das du dein Konto l&ouml;schen m&ouml;schtest?</h5>
								</div>                        
								<div class="form-group">
									<label>Aktuelles Passwort: </label>
									<input type="password" name="password-actual" class="form-control" placeholder="Aktuelles Passwort" required="">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-danger btn-block">Konto wirklich l&ouml;schen</button>
								</div>
							</form>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
				<div class="col-sm-6 mt-2 mb-2">
					<div class="card">
						<div class="card-header">
							Hinweis
						</div>
						<div class="card-body">
							<p>Nach dem eingeben des Passwortes und absenden des Formulares, erh&auml;lst du eine E-Mail mit einem Best&auml;tigungslink. 
								Klicke innerhalb von 14 Tagen auf diesen Link um deinen Konto zu l&ouml;schen, ansonsten verf&auml;llt der Link und der Vorgang muss wiederholt werden.
							<br>Schaue auch in dein SPAM-Postfach nach.</p>
							<a class="btn btn-success btn-block" href="?p=profil">Abbrechen</a>
						</div>
					</div>
				</div><!-- /.col-sm-6 -->
			</div><!-- /.row -->
		<?php } ?>
</div><!-- /.content -->
</div><!-- /.content-wrapper -->
