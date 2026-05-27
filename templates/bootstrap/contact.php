<div class="content-wrapper">
<div class="container">

      <!-- Page Heading/Breadcrumbs -->
      <h1 class="mt-4 mb-3">Contact
        <small>Subheading</small>
      </h1>

      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.php">Home</a>
        </li>
        <li class="breadcrumb-item active">Contact</li>
      </ol>

      <!-- Content Row -->
      <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8 mb-4">
          <!-- OpenStreetMap (DSGVO-konform, kein Tracking) -->
          <!-- URL wird in ?p=settings unter "Karte" konfiguriert -->
          <?php
            $osm_url = (string)$loginsystem->getMainData('osm_embed_url');
            if (!empty($osm_url) && str_starts_with($osm_url, 'https://www.openstreetmap.org/')):
          ?>
          <iframe
            width="100%"
            height="400px"
            frameborder="0"
            allowfullscreen
            src="<?php echo htmlspecialchars($osm_url); ?>">
          </iframe>
          <small class="text-muted">
            <a href="https://www.openstreetmap.org/" target="_blank" rel="noopener">Größere Karte anzeigen</a>
          </small>
          <?php else: ?>
          <div class="alert alert-info">Keine Karte konfiguriert. Bitte die OSM-URL in den <a href="?p=settings">Einstellungen</a> hinterlegen.</div>
          <?php endif; ?>
        </div>
        <!-- Contact Details Column -->
        <div class="col-lg-4 mb-4">
          <h3>Contact Details</h3>
          <p>
            Musterstraße 1
            <br>D-12345 Musterstadt
            <br>
          </p>
          <p>
            <abbr title="Phone">P</abbr>: +49 (0) 123 456789
          </p>
          <p>
            <abbr title="Email">E</abbr>:
            <a href="mailto:info@example.com">info@example.com
            </a>
          </p>
          <p>
            <abbr title="Hours">H</abbr>: Mo–Fr: 9:00–17:00 Uhr
          </p>
        </div>
      </div>
      <!-- /.row -->

      <!-- Contact Form -->
      <!-- In order to set the email address and subject line for the contact form go to the bin/contact_me.php file. -->
      <div class="row">
        <div class="col-lg-8 mb-4">
          <h3>Sende uns eine Nachricht</h3>
			<?php echo $error ?? ''; ?>
          <form name="sentMessage" action="?p=contact&c=send" method="post" id="contactForm" novalidate>
<?php echo csrf_field(); ?>
            <div class="control-group form-group">
              <div class="controls">
                <label>Name: *</label>
                <input type="text" class="form-control" name="name" maxlength="64" id="name" required value="<?php echo e(isset($_POST['name']) ? $_POST['name'] : ''); ?>">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Telefonnummer:</label>
                <input type="tel" class="form-control" name="phone" maxlength="16" id="phone" required value="<?php echo e(isset($_POST['phone']) ? $_POST['phone'] : ''); ?>">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>E-Mail Adresse: *</label>
                <input type="email" class="form-control" name="email" maxlength="64" id="email" required value="<?php echo e(isset($_POST['email']) ? $_POST['email'] : ''); ?>">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Nachricht: *</label>
                <textarea rows="10" cols="100" class="form-control" name="message" id="message" required maxlength="2048" style="resize:none"><?php echo e(isset($_POST['message']) ? $_POST['message'] : ''); ?></textarea>
              </div>
            </div>
  			<div><label>Sicherheitscode: *</label></div>
			<div class="form-group">
				<img class="captcha-img" src="?captcha=img" title="Klicke um neuen Code zu erhalten">
				<input type="text" name="captcha" maxlength="4" class="form-control captcha-field" placeholder="Code">
			</div>
				<div class="form-group">
					<label class="checkbox">
						<input type="checkbox" name="dsgvo" value="1">
						Ich stimme zu, dass meine Angaben aus dem Kontaktformular zur Beantwortung meiner Anfrage erhoben und verarbeitet werden. 
						Die Daten werden nach abgeschlossener Bearbeitung Ihrer Anfrage gel&ouml;scht. 
						Hinweis: Sie k&ouml;nnen Ihre Einwilligung jederzeit f&uuml;r die Zukunft per E-Mail an <?php echo $loginsystem->getMainData('dsgvo_email'); ?> widerrufen. 
						Detaillierte Informationen zum Umgang mit Nutzerdaten finden Sie in unserer Datenschutzerkl&auml;rung. *
					</label>
				</div>
            <!-- For success/fail messages -->
            <button type="submit" class="btn btn-primary" id="sendMessageButton">Nachricht senden</button>
          </form>
        </div>

      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->
</div><!-- /.content-wrapper -->
