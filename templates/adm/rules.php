<div class="container">
    <!-- Page header/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Regeln <small>verwalten</small></h1>
    <ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="./">&Uuml;bersicht</a></li>
  		<li class="breadcrumb-item">Verwaltung</li>
  		<li class="breadcrumb-item active">Regeln verwalten</li>
    </ol>
		<?php echo $error; ?>
		<div class="row">
			<div class="col-lg-12">
      
						<?php $rulelist = $rules->listRules($id); ?>
						<div class="row">
							<div class="col-md-4">
								<?php if(empty($f)){ ?>
									<div class="card">
										<div class="card-header"><i class="fa fa-plus"></i> Regel hinzuf&uuml;gen</div>
										<div class="card-body">
											<form action="?p=rules&c=new" method="post">
								<?php echo csrf_field(); ?>
												<div class="form-group">
													<input type="text" class="form-control" name="name" placeholder="Regelbezeichnung" maxlength="40" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="tag" placeholder="Regelname" maxlength="40" value="<?php echo htmlspecialchars($_POST['tag'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="description" placeholder="Beschreibung" maxlength="256" value="<?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="submit" value="Hinzuf&uuml;gen" class="btn btn-block btn-success">
												</div>
											</form>
										</div>
									</div><!-- /.card -->
								<?php } elseif($f == 'edit' && !empty($id)){ ?>
									<div class="card">
										<div class="card-header"><i class="fa fa-pencil"></i> Regel bearbeiten <a class="btn btn-sm btn-warning float-right" href="?p=rules">Abbrechen</a></div>
										<div class="card-body">
											<form action="?p=rules&c=edit&f=edit&id=<?php echo $id; ?>" method="post">
								<?php echo csrf_field(); ?>
												<div class="form-group">
													<input type="text" class="form-control" name="name" placeholder="Regelbezeichnung" maxlength="40" value="<?php echo htmlspecialchars($_POST['name'] ?? $rules->getValue('rules', 'id', $id, 'name'), ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="tag" placeholder="Regelname" maxlength="40" value="<?php echo htmlspecialchars($_POST['tag'] ?? $rules->getValue('rules', 'id', $id, 'tag'), ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="description" placeholder="Beschreibung" maxlength="256" value="<?php echo htmlspecialchars($_POST['description'] ?? $rules->getValue('rules', 'id', $id, 'description'), ENT_QUOTES, 'UTF-8'); ?>">
												</div>
												<div class="form-group">
													<input type="submit" value="Speichern" class="btn btn-block btn-success">
												</div>
											</form>
										</div>
									</div><!-- /.card -->
								<?php } elseif($f == 'delete' && !empty($id)){ ?>
									<div class="card">
										<div class="card-header"><i class="fa fa-trash"></i> Regel entfernen <a class="btn btn-sm btn-warning float-right" href="?p=rules">Abbrechen</a></div>
										<div class="card-body">
											<form action="?p=rules&c=delete&f=delete&id=<?php echo $id; ?>" method="post">
								<?php echo csrf_field(); ?>
												<div class="form-group">
													Soll diese Regel wirklich entfernt werden?
												</div>
												<div class="form-group">
													<input type="submit" value="Entfernen" class="btn btn-block btn-danger">
												</div>
											</form>
										</div>
									</div><!-- /.card -->
								<?php } ?>
								<div class="card mt-4">
									<div class="card-header"><i class="fa fa-info-circle"></i> Regel Infos</div>
									<div class="card-body">
										<strong>Was sind Regeln?</strong>
										<p>Regeln sind "Berechtigungsschl&uuml;ssel", mit denen du bestimmte Aktionen ausf&uuml;hren darfst.
										Diese sind werden in der jeweiligen Funktion abgefragt.</p>
										<p>Die Oberfl&auml;che bietet dir diese zu verwalten.
										Du kannst selbst eigene Funktionen schreiben und so deine eigenen Regeln hier hinzuf&uuml;gen und verwalten.
										Wie du diese Regeln in dein Script einbauen kannst, findest du auf dieser <a target="_blank" href="http://www.marlight-systems.de/?p=project_easy_2&u=maunal#rules">Seite</a>.</p>
										<p>Mehrere Regeln sind immer zu einem Rang (Gruppe) zusammengefasst. Somit musst du den jeweiligen Nutzern nicht einzeln die Regeln vergeben,
										sondern kannst ihm eine Gruppe zuweisen, welche du <a href="?p=ranks">hier</a> verwalten kannst.</p>
									</div>
								</div><!-- /.card -->
							</div><!--- /.col-md-4 -->
							<div class="col-md-8">
								<div class="table-responsive">
									<table class="table table-striped table-sorter table-hover">
										<thead>
											<tr>
												<th class="wp15">Regelbezeichnung</th>
												<th class="wp15">Regelname</th>
												<th class="wp15">Beschreibung</th>
												<th class="wp15">Optionen</th>
											</tr>
										</thead>
										<tbody>
											<?php echo $rulelist; ?>
										</tbody>
									</table>
								</div>
							</div><!-- /.col-md-8 -->
						</div><!-- /.row -->
				</div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
</div>



