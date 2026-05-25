<!-- Page Content -->
<div class="container">
    <!-- Marketing Icons Section -->
    <div class="row mt25px">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading"><h4 class="mt0px mb0px">Registrierung</h4></div>
                <form action="./?c=regist<?php echo isset($_GET['p']) ? '&p='.$_GET['p'] : null; ?>" method="POST">
                    <div class="panel-body">
                        <?php echo $error; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Benutzername*:</label>
                                    <input type="text" class="form-control" name="regist-username" maxlength="64"
                                           placeholder="Benutzername" required
                                           value="<?php echo isset($_POST["regist-username"]) ? $_POST["regist-username"] : ''; ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>Passwort*:</label>
                                    <input type="password" class="form-control" name="regist-passwd"
                                           placeholder="Passwort" maxlength="64" required/>
                                </div>
                                <div class="form-group">
                                    <label>Passwort wiederholen*:</label>
                                    <input type="password" class="form-control" name="regist-passwd-confirm"
                                           placeholder="Passwort wiederholen" maxlength="64" required/>
                                </div>
                                <div class="form-group">
                                    <label>Vor-/Nachname:</label>
                                    <input type="text" class="form-control" name="regist-fullname" maxlength="64"
                                           placeholder="Vor-/Nachname"
                                           value="<?php echo isset($_POST["regist-fullname"]) ? $_POST["regist-fullname"] : ''; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>E-Mail Adresse*:</label>
                                    <input type="email" class="form-control" name="regist-email" maxlength="64"
                                           placeholder="E-Mail Adresse" required
                                           value="<?php echo isset($_POST["regist-email"]) ? $_POST["regist-email"] : ''; ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>E-Mail Adresse wiederholen*:</label>
                                    <input type="email" class="form-control" name="regist-email-confirm" maxlength="64"
                                           placeholder="E-Mail Adresse wiederholen" required
                                           value="<?php echo isset($_POST["regist-email-confirm"]) ? $_POST["regist-email-confirm"] : ''; ?>"/>
                                </div>
                                <?php
                                echo $additional_fields->showFields(1, 'regist-');
                                ?>
                                <div><label>Sicherheitscode*:</label></div>
                                <div class="form-group">
                                    <img class="captcha-img" src="./?captcha=img"
                                         title="Klicke um neuen Code zu erhalten">
                                    <input type="text" name="regist-captcha" maxlength="4"
                                           class="form-control captcha-field" placeholder="Code" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="checkbox">
                                    <input type="checkbox" name="dsgvo" value="1">
                                    Ich stimme zu, dass meine Angaben aus dem Registrierungsformular zur Nutzung der
                                    Webseite erhoben und verarbeitet werden.
                                    Hinweis: Sie k&ouml;nnen Ihre Einwilligung jederzeit f&uuml;r die Zukunft per E-Mail
                                    an <?php echo $loginsystem->getMainData('dsgvo_email'); ?> widerrufen.
                                    Detaillierte Informationen zum Umgang mit Nutzerdaten finden Sie in unserer
                                    Datenschutzerkl&auml;rung. *
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="./?p=login" class="btn btn-primary">oder einloggen</a>
                        <button type="submit" class="btn btn-success pull-right">Registrieren</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.row -->
</div><!-- /.container -->
