    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-envelope"></i> Kontakt
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li class="active">Kontakt</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

      <!-- Content Row -->
      <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8 mb-4">
          <!-- Embedded Google Map -->
          <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="//maps.google.com/maps?hl=en&amp;ie=UTF8&amp;ll=37.0625,-95.677068&amp;spn=56.506174,79.013672&amp;t=m&amp;z=4&amp;output=embed"></iframe>
        </div>
        <!-- Contact Details Column -->
        <div class="col-lg-4 mb-4">
          <h3>Kontakt Details</h3>
          <p>
            3481 Melrose Place
            <br>Beverly Hills, CA 90210
            <br>
          </p>
          <p>
            <abbr title="Phone">P</abbr>: (123) 456-7890
          </p>
          <p>
            <abbr title="Email">E</abbr>:
            <a href="mailto:name@example.com">name@example.com
            </a>
          </p>
          <p>
            <abbr title="Hours">H</abbr>: Monday - Friday: 9:00 AM to 5:00 PM
          </p>
        </div>
      </div>
      <!-- /.row -->

      <!-- Contact Form -->
      <!-- In order to set the email address and subject line for the contact form go to the bin/contact_me.php file. -->
      <div class="row">
        <div class="col-lg-8 mb-4">
          <h3>Sende uns eine Nachricht</h3>
			<?php echo $error; ?>
          <form name="sentMessage" action="?p=contact&c=send" method="post" id="contactForm" novalidate>
            <div class="control-group form-group">
              <div class="controls">
                <label>Name: *</label>
                <input type="text" class="form-control" name="name" maxlength="64" id="name" required value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Telefonnummer:</label>
                <input type="tel" class="form-control" name="phone" maxlength="16" id="phone" required value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>E-Mail Adresse: *</label>
                <input type="email" class="form-control" name="email" maxlength="64" id="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Nachricht: *</label>
                <textarea rows="10" cols="100" class="form-control" name="message" id="message" required maxlength="2048" style="resize:none"><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
              </div>
            </div>
  			<div><label>Sicherheitscode: *</label></div>
			<div class="form-group">
				<img class="captcha-img" src="?captcha=img" onClick="this.src = '?captcha=img&generate=' + Math.random()" title="Klicke um neuen Code zu erhalten">
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
