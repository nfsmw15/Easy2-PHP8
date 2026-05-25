<div class="container">
    <!-- Page Heading/Breadcrumbs -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <i class="fa fa-plus-circle"></i> Zusatzfelder
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="./">&Uuml;bersicht</a>
                </li>
                <li>Verwaltung</li>
                <li>
                    <a href="./?p=settings">Einstellungen</a>
                </li>
                <li class="active">Zusatzfelder verwalten</li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
    <?php echo $error; ?>
	<div class="row">
		<div class="col-sm-12">
			<div class="clearfix">
				<?php if($f != 'new'){ ?>
					<a class="btn btn-primary pull-right" href="./?p=additional_fields&f=new"><i class="fa fa-plus"></i> Neues Feld anlegen</a>
					<?php if($f == 'edit' || $f == 'remove' || $f == 'show'){ ?>
						<a class="btn btn-warning pull-right mr5px" href="./?p=additional_fields"><i class="fa fa-times"></i> Abbrechen</a>
					<?php } ?>
				<?php } else { ?>
					<a class="btn btn-warning pull-right" href="./?p=additional_fields"><i class="fa fa-times"></i> Abbrechen</a>
				<?php } ?>
				<a class="btn btn-info pull-right mr5px" href="./?p=settings"><i class="fa fa-arrow-left"></i> zu den Einstellungen</a>
			</div>
			<?php if(empty($f)){ ?>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Titel</th>
								<th>Typ</th>
								<th>Pflicht</th>
								<th>Regist</th>
								<th>Aktionen</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $additional_fields->listFields(); ?>
						</tbody>
					</table>
				</div><!-- /.table-responsive -->
			<?php } elseif($f == 'edit' && !empty($id)){ ?>
				<div class="panel panel-primary mt15px">
					<div class="panel-heading">
						<i class="fa fa-pencil"></i> Feld bearbeiten
					</div>
					<div class="panel-body">
						<form action="./?p=additional_fields&f=edit&c=edit&id=<?php echo $id; ?>" method="post">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="name">Name des Feldes</label>
										<input type="text" name="name" id="name" class="form-control" placeholder="Name" maxlength="64" value="<?php echo isset($_POST['name']) ? $_POST['name'] : $additional_fields->getValue('fields', 'id', $id, 'name'); ?>">
									</div>
									<div class="col-sm-6">
										Hier kann ein Name vergeben werden, welcher im HTML-Code f&uuml;r das Feld verwendet wird.
										Es d&uuml;rfen nur Buchstaben a-Z, Zahlen 0-9, -, _ verwendet werden.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="title">Titel des Feldes</label>
										<input type="text" name="title" id="title" class="form-control" placeholder="Titel" maxlength="64" value="<?php echo isset($_POST['title']) ? $_POST['title'] : $additional_fields->getValue('fields', 'id', $id, 'title'); ?>">
									</div>
									<div class="col-sm-6"><br>
										Dies ist der Name der dem Benutzer &uuml;ber dem Feld angezeigt wird.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="type">Feldtyp</label>
										<select name="type" id="type" class="form-control">
											<?php $type_field = isset($_POST['type']) ? $_POST['type'] : $additional_fields->getValue('fields', 'id', $id, 'type'); ?>
											<option <?php echo checker($type_field, "text"); ?> value="text">Text-Feld</option>
											<option <?php echo checker($type_field, "number"); ?> value="number">Nummern-Feld</option>
											<option <?php echo checker($type_field, "email"); ?> value="email">E-Mail-Feld</option>
											<option <?php echo checker($type_field, "url"); ?> value="url">URL-Feld</option>
											<option <?php echo checker($type_field, "textarea"); ?> value="textarea">Gro&szlig;es Text-Feld</option>
											<option <?php echo checker($type_field, "checkbox"); ?> value="checkbox">Checkbox (auch Mehrfachauswahl)</option>
											<option <?php echo checker($type_field, "radio"); ?> value="radio">Radio-Button (nur eine Auswahlm&ouml;glichkeit)</option>
											<option <?php echo checker($type_field, "select"); ?> value="select">Select - Auswahl Pull-Down Feld (nur eine Auswahlm&ouml;glichkeit)</option>
										</select>
									</div>
									<div class="col-sm-6"><br>
										Was soll es f&uuml;r ein Feld werden?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="placeholder">Platzhalter</label>
										<input type="text" name="placeholder" id="placeholder" class="form-control" placeholder="Titel" maxlength="128" value="<?php echo isset($_POST['placeholder']) ? $_POST['placeholder'] : $additional_fields->getValue('fields', 'id', $id, 'placeholder'); ?>">
									</div>
									<div class="col-sm-6"><br>
										Dieser Text erscheint im Feld, wenn es leer ist.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="maxlength">Maximale L&auml;nge</label>
										<input type="number" name="maxlength" id="maxlength" class="form-control" placeholder="Platzhalter" min="0" max="99999999999" maxlength="11" value="<?php echo isset($_POST['maxlength']) ? $_POST['maxlength'] : $additional_fields->getValue('fields', 'id', $id, 'maxlength'); ?>">
									</div>
									<div class="col-sm-6"><br>
										Wie lang darf der Inhalt maximal sein?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="value">Standardwert</label>
										<input type="text" name="value" id="value" class="form-control" placeholder="Standardwert" maxlength="64" value="<?php echo isset($_POST['value']) ? $_POST['value'] : $additional_fields->getValue('fields', 'id', $id, 'value'); ?>">
									</div>
									<div class="col-sm-6"><br>
										Hier kann ein Standardwert eingegeben werden.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="description">Beschreibung</label>
										<textarea name="description" id="description" class="form-control" placeholder="Beschreibung"><?php echo isset($_POST['description']) ? $_POST['description'] : $additional_fields->getValue('fields', 'id', $id, 'description'); ?></textarea>
									</div>
									<div class="col-sm-6"><br>
										Gebe eine Beschreibung f&uuml;r den Benutzer ein
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="regex">Regex-Code</label>
										<div class="input-group">
											<span class="input-group-addon">#</span>
											<textarea name="regex" id="regex" class="form-control" placeholder="Regex-Code"><?php echo isset($_POST['regex']) ? $_POST['regex'] : $additional_fields->getValue('fields', 'id', $id, 'regex'); ?></textarea>
											<span class="input-group-addon">#</span>
										</div>
									</div>
									<div class="col-sm-6"><br>
										Als erfahrener Benutzer kannst du hier einen Regex-Code hineinschreiben, welcher das Feld auf deine eigenen Kretieren pr&uuml;fen soll.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="regex_options">Regex Optionen</label>
										<input type="text" name="regex_options" id="regex_options" class="form-control" placeholder="Regex Optionen" maxlength="8" value="<?php echo isset($_POST['regex_options']) ? $_POST['regex_options'] : $additional_fields->getValue('fields', 'id', $id, 'regex_options'); ?>">
									</div>
									<div class="col-sm-6"><br>
										Hier kannst du die Regex Optionen eingeben. Wie z.B. i, s, etc.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="options">Optionen</label>
										<textarea name="options" id="options" class="form-control" rows="4" placeholder="1^Example Text#2^Example Text 2"><?php echo isset($_POST['options']) ? $_POST['options'] : $additional_fields->getValue('fields', 'id', $id, 'options'); ?></textarea>
									</div>
									<div class="col-sm-6">
										Wenn beim Typ "Radio, Checkbox oder Select" gew&auml;hlt wurde, m&uuml;ssen hier die Optionen hineingeschrieben werden.<br>
										Die Eingabe muss so aussehen: <br>
										Wert^Text oder Beschreibung#Wert^Text<br>
										Mit ^ werden Wert und Text einer Option getrennt, mit # werden die Optionen von einander getrennt.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="position">Position</label>
										<input type="number" name="position" id="position" class="form-control" placeholder="Position" min="0" max="999" maxlength="3" value="<?php echo isset($_POST['position']) ? $_POST['position'] : $additional_fields->getValue('fields', 'id', $id, 'pos'); ?>">
									</div>
									<div class="col-sm-6"><br>
										An welcher Position soll das Feld stehen?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label>
											<input type="checkbox" name="required" <?php echo checker(isset($_POST["required"]) ? $_POST["required"] : $additional_fields->getValue('fields', 'id', $id, 'required'), 1, 1); ?> value="1"/> Pflichtfeld?
										</label>
										<br>
										<label>
											<input type="checkbox" name="regist" <?php echo checker(isset($_POST["regist"]) ? $_POST["regist"] : $additional_fields->getValue('fields', 'id', $id, 'regist'), 1, 1); ?> value="1"/> Bei der Registrierung sichtbar?
										</label>
									</div>
									<div class="col-sm-6">
										<button type="submit" class="btn btn-block btn-success mt-3"><i class="fa fa-check"></i> Speichern</button>
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							
						</form>
					</div>
				</div>
			<?php } elseif($f == 'remove' && !empty($id)){ ?>
				<div class="panel panel-primary mt15px">
					<div class="panel-heading">
						<i class="fa fa-pencil"></i> Feld l&ouml;schen
					</div>
					<div class="panel-body">
						<form action="./?p=additional_fields&f=remove&c=remove&id=<?php echo $id; ?>" method="post">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="password">Passwort zur Best&auml;tigung</label>
										<input type="password" name="password" id="password" class="form-control" placeholder="Passwort" maxlength="64">
									</div>
									<div class="col-sm-6">
										Wenn du das Feld "<?php echo $additional_fields->getValue('fields', 'id', $id, 'name'); ?>" wirklich l&ouml;schen m&ouml;chtest, gebe dein Passwort zur Best&auml;tigung ein.
										Mit dem Feld werden alle Feldbezogenen Daten mit enfternt!
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<button type="submit" class="btn btn-block btn-danger mt-3"><i class="fa fa-trash"></i> L&ouml;schen</button>
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							
						</form>
					</div>
				</div>
			<?php } elseif($f == 'show' && !empty($id)){ ?>
				<div class="panel panel-primary mt15px">
					<div class="panel-heading">
						<i class="fa fa-eye"></i> Feld ansehen
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-striped">
								<tbody>
									<tr>
										<td class="text-bold">Name:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'name'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Titel:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'title'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Type:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'type'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Platzhalter:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'placeholder'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Maximale L&auml;nge:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'maxlength'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Standardwert:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'value'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Beschreibung:</td>
										<td><?php echo nl2br($additional_fields->getValue('fields', 'id', $id, 'description')); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Regex-Code:</td>
										<td>#<?php echo $additional_fields->getValue('fields', 'id', $id, 'regex'); ?>#<?php echo $additional_fields->getValue('fields', 'id', $id, 'regex_options'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Optionen:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'options'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Position:</td>
										<td><?php echo $additional_fields->getValue('fields', 'id', $id, 'pos'); ?></td>
									</tr>
									<tr>
										<td class="text-bold">Pflichtfeld:</td>
										<td><?php echo ($additional_fields->getValue('fields', 'id', $id, 'required') == 1) ? 'Ja' : 'Nein'; ?></td>
									</tr>
									<tr>
										<td class="text-bold">Bei der Registrierung sichtbar:</td>
										<td><?php echo ($additional_fields->getValue('fields', 'id', $id, 'regist') == 1) ? 'Ja' : 'Nein'; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			<?php } elseif($f == 'new'){ ?>
				<div class="panel panel-primary mt15px">
					<div class="panel-heading">
						<i class="fa fa-plus"></i> Feld hinzuf&uuml;gen
					</div>
					<div class="panel-body">
						<form action="./?p=additional_fields&f=new&c=new" method="post">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="name">Name des Feldes</label>
										<input type="text" name="name" id="name" class="form-control" placeholder="Name" maxlength="64" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
									</div>
									<div class="col-sm-6">
										Hier kann ein Name vergeben werden, welcher im HTML-Code f&uuml;r das Feld verwendet wird.
										Es d&uuml;rfen nur Buchstaben a-Z, Zahlen 0-9, -, _ verwendet werden.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="title">Titel des Feldes</label>
										<input type="text" name="title" id="title" class="form-control" placeholder="Titel" maxlength="64" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>">
									</div>
									<div class="col-sm-6"><br>
										Dies ist der Name der dem Benutzer &uuml;ber dem Feld angezeigt wird.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="type">Feldtyp</label>
										<select name="type" id="type" class="form-control">
											<?php $type_field = isset($_POST['type']) ? $_POST['type'] : 'text'; ?>
											<option <?php echo checker($type_field, "text"); ?> value="text">Text-Feld</option>
											<option <?php echo checker($type_field, "number"); ?> value="number">Nummern-Feld</option>
											<option <?php echo checker($type_field, "email"); ?> value="email">E-Mail-Feld</option>
											<option <?php echo checker($type_field, "url"); ?> value="url">URL-Feld</option>
											<option <?php echo checker($type_field, "textarea"); ?> value="textarea">Gro&szlig;es Text-Feld</option>
											<option <?php echo checker($type_field, "checkbox"); ?> value="checkbox">Checkbox (auch Mehrfachauswahl)</option>
											<option <?php echo checker($type_field, "radio"); ?> value="radio">Radio-Button (nur eine Auswahlm&ouml;glichkeit)</option>
											<option <?php echo checker($type_field, "select"); ?> value="select">Select - Auswahl Pull-Down Feld (nur eine Auswahlm&ouml;glichkeit)</option>
										</select>
									</div>
									<div class="col-sm-6"><br>
										Was soll es f&uuml;r ein Feld werden?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="placeholder">Platzhalter</label>
										<input type="text" name="placeholder" id="placeholder" class="form-control" placeholder="Titel" maxlength="128" value="<?php echo isset($_POST['placeholder']) ? $_POST['placeholder'] : ''; ?>">
									</div>
									<div class="col-sm-6"><br>
										Dieser Text erscheint im Feld, wenn es leer ist.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="maxlength">Maximale L&auml;nge</label>
										<input type="number" name="maxlength" id="maxlength" class="form-control" placeholder="Platzhalter" min="0" max="99999999999" maxlength="11" value="<?php echo isset($_POST['maxlength']) ? $_POST['maxlength'] : ''; ?>">
									</div>
									<div class="col-sm-6"><br>
										Wie lang darf der Inhalt maximal sein?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="value">Standardwert</label>
										<input type="text" name="value" id="value" class="form-control" placeholder="Standardwert" maxlength="1024" value="<?php echo isset($_POST['value']) ? $_POST['value'] : ''; ?>">
									</div>
									<div class="col-sm-6"><br>
										Hier kann ein Standardwert eingegeben werden.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="description">Beschreibung</label>
										<textarea name="description" id="description" class="form-control" placeholder="Beschreibung"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
									</div>
									<div class="col-sm-6"><br>
										Gebe eine Beschreibung f&uuml;r den Benutzer ein
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="regex">Regex-Code</label>
										<div class="input-group">
											<span class="input-group-addon">#</span>
											<textarea name="regex" id="regex" class="form-control" placeholder="Regex-Code"><?php echo isset($_POST['regex']) ? $_POST['regex'] : ''; ?></textarea>
											<span class="input-group-addon">#</span>
										</div>
									</div>
									<div class="col-sm-6"><br>
										Als erfahrener Benutzer kannst du hier einen Regex-Code hineinschreiben, welcher das Feld auf deine eigenen Kretieren pr&uuml;fen soll.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="regex_options">Regex Optionen</label>
										<input type="text" name="regex_options" id="regex_options" class="form-control" placeholder="Regex Optionen" maxlength="8" value="<?php echo isset($_POST['regex_options']) ? $_POST['regex_options'] : ''; ?>">
									</div>
									<div class="col-sm-6"><br>
										Hier kannst du die Regex Optionen eingeben. Wie z.B. i, s, etc.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="options">Optionen</label>
										<textarea name="options" id="options" class="form-control" rows="4" placeholder="1^Example Text#2^Example Text 2"><?php echo isset($_POST['options']) ? $_POST['options'] : ''; ?></textarea>
									</div>
									<div class="col-sm-6">
										Wenn beim Typ "Radio, Checkbox oder Select" gew&auml;hlt wurde, m&uuml;ssen hier die Optionen hineingeschrieben werden.<br>
										Die Eingabe muss so aussehen: <br>
										Wert^Text oder Beschreibung#Wert^Text<br>
										Mit ^ werden Wert und Text einer Option getrennt, mit # werden die Optionen von einander getrennt.
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="position">Position</label>
										<input type="number" name="position" id="position" class="form-control" placeholder="Position" min="0" max="999" maxlength="3" value="<?php echo isset($_POST['position']) ? $_POST['position'] : '0'; ?>">
									</div>
									<div class="col-sm-6"><br>
										An welcher Position soll das Feld stehen?
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							<hr>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label>
											<input type="checkbox" name="required" <?php echo checker(isset($_POST["required"]) ? $_POST["required"] : 0, 1, 1); ?> value="1"/> Pflichtfeld?
										</label>
										<br>
										<label>
											<input type="checkbox" name="regist" <?php echo checker(isset($_POST["regist"]) ? $_POST["regist"] : 0, 1, 1); ?> value="1"/> Bei der Registrierung sichtbar?
										</label>
									</div>
									<div class="col-sm-6">
										<button type="submit" class="btn btn-block btn-success mt-3"><i class="fa fa-plus"></i> Anlegen</button>
									</div>
								</div><!-- /.row -->
							</div><!-- /.form-group -->
							
						</form>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>