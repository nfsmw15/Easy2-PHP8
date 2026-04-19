        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Installation - Step 5</h1>
                        <ol class="breadcrumb pull-right">
                            <li><a href="index.php">Einleitung</a></li>
                            <li><a href="index.php?p=terms_of_use">Nutzungsbedingungen</a></li>
                            <li><a href="index.php?p=step1">Step 1 - Voraussetzungen</a></li>
                            <li><a href="index.php?p=step2">Step 2 - MySQL-Daten</a></li>
							<li><a href="index.php?p=step3">Step 3 - Grundeinstellungen</a></li>
							<li><a href="index.php?p=step4">Step 4 - Accounts</a></li>
							<li class="active actual">Step 5 - Impressum</li>
                            <li class="active">Fertig</li>
              	        </ol>
                    </div>
				</div>
				<?php echo $error; ?>
                <div class="row">
                	<div class="col-sm-12">
                    	<div class="panel panel-primary">
                        	<div class="panel-heading">
                            	<i class="fa fa-legal"></i> Impressum
                            </div>
                            <div class="panel-body">
                                <form action="index.php?p=step5&c=replace" method="post">
									<div class="form-group">
										<blockquote>
											Wie sich aus &sect; 55 I RStV ergibt trifft einen Anbieter keine Impressumspflicht, 
											d.h. er kann seine Webseite v&ouml;llig anonym ins World Wide Web stellen, 
											wenn das Angebot ausschließlich pers&ouml;nlichen oder famili&auml;ren Zwecken dient.<br>
											<i class="text-muted">- Zitat: Dr. Stephan Ott -</i>
											<br>
											Solltest du dir nicht sicher sein, ob du ein Impressum ben&ouml;tigst, dann lese folgenden Artikel:<br>
											<a href="http://www.linksandlaw.info/Impressumspflicht-Notwendige-Angaben.html" target="_blank">http://www.linksandlaw.info/Impressumspflicht-Notwendige-Angaben.html</a>
										</blockquote>
										<p>Solltest du kein Impressum ben&ouml;tigen, lasse die Felder leer und dr&uuml;cke auf "Weiter".</p>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="company">Firma:</label>
												<div class="form-group input-group">
													<span class="input-group-addon"><i class="fa fa-fw fa-building fa-fw"></i></span>
													<input type="text" name="company" class="form-control" id="company" maxlength="64" placeholder="Firma">
												</div>
											</div>
											<div class="col-sm-6"><br>
												Wenn du deine Webseite ale Gewerbetreibender/Gewerbe nutzt, gebe hier den Namen deines Gewerbes ein.
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="name">Vollst&auml;ndiger Name:</label>
												<div class="form-group input-group">
													<span class="input-group-addon"><i class="fa fa-fw fa-font fa-fw"></i></span>
													<input type="text" name="name" require class="form-control" id="name" maxlength="16" placeholder="Vor-/ Nachname">
												</div>
											</div>
											<div class="col-sm-6">
												Gebe deinen Vollst&auml;ndigen Namen an, wenn du ein Impressum ben&ouml;tigst.
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="adress">Adresse:</label>
												<div class="form-group">
													<textarea name="adress" id="adress" rows="4" class="form-control" maxlength="256" placeholder="Max-Mustermann-Straße 54"></textarea>
												</div>
											</div>
											<div class="col-sm-6">
												Gebe deine Adresse bzw. Adresse deines Gewerbes ein.
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="contact">Kontaktdaten:</label>
												<div class="form-group">
													<textarea name="contact" rows="6" id="contact" class="form-control" maxlength="1024" placeholder="Tel.: 01234/5678912345"></textarea>
												</div>
											</div>
											<div class="col-sm-6"><br>
												Gebe Kontaktdaten ein, du kannst auch HTML-Code verwenden.
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="disclaimer">Haftungsausschluss:</label>
												<div class="form-group">
													<textarea name="disclaimer" id="disclaimer" rows="10" class="form-control"></textarea>
												</div>
											</div>
											<div class="col-sm-6"><br>
												F&uuml;ge hier deinen Haftungsausschluss ein welcher im Impressum stehe soll.<br>
												Wenn du noch keinen eigenen hast, erstelle dir einfach hier deinen eigenen:<br>
												<a href="https://www.e-recht24.de/muster-disclaimer.html" target="_blank">https://www.e-recht24.de/muster-disclaimer.html</a>
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<input type="submit" class="btn btn-md btn-success btn-block" value="Weiter">
											</div>
										</div>
									</div>
                                </form>
                       		</div>
                        </div>
                    </div>
				</div>
            </div>
        </div>