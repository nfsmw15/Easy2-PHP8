<?php
/********************************************
*
* System:   EASY 2.0 Loginsystem
* Author:   Marius Rasche (aka Marlight)
* File:     run.inc.php
* FVersion: 0.5.4 (this file)
* SVersion: 0.9.8.5 (complete System)
* Date:     04.04.2023
*
* Created by www.marlight-systems.de
* Copyright by Marlight Systems (www.marlight-systems.de)
* All rights reserved.
*
* PHP 8 modifications: Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
* SPDX-License-Identifier: AGPL-3.0-or-later
*********************************************/


$a = length(isset($_GET['a']) ? $_GET['a'] : '', 64);
$b = length(isset($_GET['b']) ? $_GET['b'] : '', 64);
$c = length(isset($_GET['c']) ? $_GET['c'] : '', 64);
$d = length(isset($_GET['d']) ? $_GET['d'] : '', 64);
$e = length(isset($_GET['e']) ? $_GET['e'] : '', 64);
$f = length(isset($_GET['f']) ? $_GET['f'] : '', 64);
$g = length(isset($_GET['g']) ? $_GET['g'] : '', 64);
$h = length(isset($_GET['h']) ? $_GET['h'] : '', 64);
$p = length(isset($_GET['p']) ? $_GET['p'] : '', 64);
$s = length(isset($_GET['s']) ? $_GET['s'] : '', 64);
$t = length(isset($_GET['t']) ? $_GET['t'] : '', 64);
$v = length(isset($_GET['v']) ? $_GET['v'] : '', 64);
$id	= length(isset($_GET['id']) ? $_GET['id'] : '', 16);
$error = isset($error) ? $error : '';
$success = isset($success) ? $success : '';
$warning = isset($warning) ? $warning : '';

/* Automatic run */
$loginsystem->cookielogin();
auto_remover(); // Clean MySQL-Tabels

// ─── Zentraler CSRF-Guard für POST-Aktionen ─────────────────────────────────
// cookielogin() läuft zuerst: Session ist vollständig aufgebaut (inkl. Token),
// bevor der Guard prüft. Öffentliche Aktionen (kein Login nötig) werden
// explizit whitelisted; alles andere erfordert einen gültigen CSRF-Token.
// Sonderfall contact/send: wird auch ohne eingeloggte Session geprüft,
// da csrf_field() den Token für alle Besucher lazy initialisiert.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $__csrf_public = (
        $c === 'login'
    );
    $__needs_csrf = !empty($_SESSION['ml_csrfToken']) ||
        ($p === 'contact'  && $c === 'send')   ||
        ($p === 'regist'   && $c === 'regist') ||
        ($p === 'pwv'      && $c === 'send')   ||
        ($p === 'pw_reset' && $c === 'reset');
    if ($__needs_csrf && !$__csrf_public) {
        $__token = $_SESSION['ml_csrfToken'] ?? '';
        $__csrf  = $_POST['csrf'] ?? '';
        if (empty($__csrf) || empty($__token) || !hash_equals($__token, $__csrf)) {
            $error = 'Ung&uuml;ltiger CSRF-Token!';
            $c = '';
        }
        unset($__csrf, $__token);
    }
    unset($__csrf_public, $__needs_csrf);
}


/* Loginsystem */

if($c == 'lock'){
	$error = $loginsystem->lock();
}

if($c == 'unlock'){
	$error = $loginsystem->unlock();
}

if($c == 'login'){
	$error = $loginsystem->login();
}

if($c == 'logout'){
	$error = $loginsystem->logout();
}

if($p == 'regist' && $c == 'regist'){
	$error = $loginsystem->regist();
}

if($p == 'pwv' && $c == 'send'){
	$error = $loginsystem->password_forget();
}

if($p == 'pw_reset' && $c == 'reset'){
	$error = $loginsystem->password_forget_reset();
}

// Token-Validierung für Formular-Anzeige (GET und fehlgeschlagene POSTs)
$pwr_token_valid = false;
if($p == 'pw_reset'){
	$pwr_token_valid = $loginsystem->validatePwrToken($a);
	if(!$pwr_token_valid && empty($error)){
		$error = 'Dieser Passwort-Reset-Link ist ung&uuml;ltig oder abgelaufen.';
	}
}

if($p == 'profil' && $c == 'remove_self'){
	$error = $loginsystem->removeUserSelf();
}

if($p == 'profil' && $c == 'passwd_change'){
	$error = $loginsystem->changePasswd();
}

if($p == 'profil' && $c == 'email_change'){
	$error = $loginsystem->changeEmail();
}

if($p == 'profil' && $c == 'data_change'){
	$error = $loginsystem->changeUserdata();
}

if($p == 'profil' && $c == 'avatar_change'){
	$error = $loginsystem->setUserAvatar();
}

// User Administration | Benutzerverwaltung
if($p == 'userlist'){
	if($c == 'new'){
		$error = $loginsystem->createUser();
		if($error === '__webmaster_confirm__'){ $webmaster_confirm = true; $error = ''; }
	}

	if($f == 'edit' && $c == 'edit'){
		$error = $loginsystem->editUser();
		if($error === '__webmaster_confirm__'){ $webmaster_confirm = true; $error = ''; }
	}
	
	if($c == 'activate'){
		$error = $loginsystem->activateUser();
	}
	
	if($c == 'deactivate'){
		$error = $loginsystem->deactivateUser();
	}
	
	if($f == 'pwreset' && $c == 'pwreset'){
		$error = $loginsystem->resetUserPasswd();
	}
	
	if($f == 'delete' && $c == 'delete'){
		$error = $loginsystem->deleteUser();
	}
	
	if($f == 'rm_avatar' && $c == 'rm_avatar'){
		$error = $loginsystem->rmUserAvatar();
	}
}

// Kontakt | Contact

if($p == 'contact' && $c == 'send'){
	$error = contact();
}

// Settings | Einstellungen

if($p == 'settings' && $c == 'mainsave'){
	$error = $loginsystem->mainSettings();
}

// Ranks | Rangverwaltung
if($p == 'ranks' && $_SERVER['REQUEST_METHOD'] === 'POST'){
	if($c == 'new_rank'){
		$error = $loginsystem->newRank();
	}

	if($f == 'edit_rank' && $c == 'edit_rank'){
		$error = $loginsystem->setRank();
	}

	if($c == 'default_rank'){
		$error = $loginsystem->setSpecialRank();
	}

	if($c == 'move_rank_up'){
		$error = $loginsystem->moveRank("up");
	}

	if($c == 'move_rank_down'){
		$error = $loginsystem->moveRank("down");
	}

	if($f == 'delete_rank' && $c == 'delete_rank'){
		$error = $loginsystem->removeRank();
	}
}

// Restore | Wiederherstellung

if($v == 'restore' && !empty($a)){
	$error = $loginsystem->restore();
}

// Benutzer  selbst aktiveren | User ativate self

if($v == 'activate' && !empty($a)){
	$error = $loginsystem->activateUserSelf();
}

// Benutzer  selbst entfernen | User remove self

if($v == 'remove' && !empty($a)){
	$error = $loginsystem->removeUserSelfLink();
}

// Rules | Regelverwaltung
if($p == 'rules' && $_SERVER['REQUEST_METHOD'] === 'POST'){
	if($c == 'new'){
		$error = $rules->newRule();
	}
	if($f == 'edit' && $c == 'edit'){
		$error = $rules->editRule();
	}
	if($f == 'delete' && $c == 'delete'){
		$error = $rules->removeRule();
	}
}

// Sites | Seiten verwalten
if($p == 'sites'){
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if($c == 'add_site'){
			$error = $sites->addSite();
		}

		if($c == 'edit' && $f == 'edit'){
			$error = $sites->editSite();
		}

		if($c == 'remove' && $f == 'remove'){
			$error = $sites->removeSite();
		}
	}

	// download ist lesend — GET erlaubt
	if($c == 'download'){
		$error = $sites->downloadSite();
	}
}

// Menu | Menue verwalten
if($p == 'menu' && $_SERVER['REQUEST_METHOD'] === 'POST'){
	if($c == 'add_menu'){
		$error = $menu->addMenu();
	}

	if($c == 'edit' && $f == 'edit'){
		$error = $menu->editMenu();
	}

	if($c == 'remove'){
		$error = $menu->removeMenu();
	}

	if($c == 'reset_positions' && $f == 'reset_positions'){
		$error = $menu->resetMenuPositions();
	}

	if($c == 'fill_gaps'){
		$error = $menu->fillGapsMenu();
	}
}

if($p == 'additional_fields' && $_SERVER['REQUEST_METHOD'] === 'POST'){
	if($f == 'new' && $c == 'new'){
		$error = $additional_fields->addField();
	}

	if($f == 'edit' && $c == 'edit'){
		$error = $additional_fields->editField();
	}

	if($f == 'remove' && $c == 'remove'){
		$error = $additional_fields->removeField();
	}
}

// Delete Install
if(is_dir("./install/")){
	delTree('./install/') ? $success = 'Installationsverzeichnis wurde gel&ouml;scht!' : $error = 'Fehler beim l&ouml;schen des Installationsverzeichnis! Bitte "./install" l&ouml;schen!';
}


/******** Benachrichtigungen *********/

// Passwort vergessen

if($h == 'pwv_success'){
	$success = 'Du hast einen Link erhalten um dein Passwort zur&uuml;ck zu setzen. Bitte schaue in dein E-Mail Postfach! (Auch im SPAM)';
}

if($h == 'pwr_success'){
	$success = 'Du hast erfolgreich dein Passwort zur&uuml;ckgesetzt!';
}

// Registrierung

if($h == 'regist_0_successfully'){
	$success = 'Du hast dich erfolgreich registriert! Wir haben dir eine E-Mail mit allen Informationen gesendet! Schaue bitte auch ggf. in dein SPAM-Postfach.';
}

if($h == 'regist_1_successfully'){
	$success = 'Du hast dich erfolgreich registriert! Wir haben dir eine E-Mail mit einem Freischaltungslink gesendet! Schaue bitte auch ggf. in dein SPAM-Postfach.';
}

if($h == 'regist_2_successfully'){
	$success = 'Du hast dich erfolgreich registriert! Wir haben dir eine E-Mail mit allen Informationen gesendet! Bitte gedulde dich, bis ein Administrator dich frei schaltet. Schaue bitte auch ggf. in dein SPAM-Postfach.';
}

// Profil

if($p == 'profil' && $h == 'pwchange_success'){
	$success = 'Du hast erfolgreich dein Passwort ge&auml;ndert!';
}

if($p == 'profil' && $h == 'emailchange_success'){
	$success = 'Du hast erfolgreich deine E-Mail Adresse ge&auml;ndert!';
}

if($p == 'profil' && $h == 'datachange_success'){
	$success = 'Du hast erfolgreich deine Benutzerdaten ge&auml;ndert!';
}

if($p == 'profil' && $h == 'remove_avatar_success'){
	$success = 'Du hast erfolgreich dein Profilbild entfernt';
}

if($p == 'profil' && $h == 'set_new_avatar_success'){
	$success = 'Du hast erfolgreich dein Profilbild ge&auml;ndert!';
}

if($p == 'profil' && $h == 'remove_self_link'){
	$success = 'Du hast eine E-Mail mit einem Link zur L&ouml;schung erhalten!';
}

// User Administration

if($p == 'userlist' && $h == 'create_user_successfully' && !empty($id)){
	$success = 'Du hast den Benutzer "'.$loginsystem->getUser('username', $id).'" erfolgreich erstellt!';
}

if($p == 'userlist' && $h == 'deactivate_user_successfully' && !empty($id)){
	$success = 'Du hast den Benutzer "'.$loginsystem->getUser('username', $id).'" erfolgreich deaktiviert!';
}

if($p == 'userlist' && $h == 'activate_user_successfully' && !empty($id)){
	$success = 'Du hast den Benutzer "'.$loginsystem->getUser('username', $id).'" erfolgreich aktiviert!';
}

if($p == 'userlist' && $h == 'reset_passwd_user_successfully' && !empty($id)){
	$success = 'Du hast das Passwort des Benutzers "'.$loginsystem->getUser('username', $id).'" erfolgreich zur&uuml;ckgesetzt!';
}

if($p == 'userlist' && $h == 'remove_user_successfully' && !empty($a)){
	$success = 'Du hast den Benutzer "'.$a.'" erfolgreich gel&ouml;scht!';
}

if($p == 'userlist' && $h == 'edit_user_successfully' && !empty($id)){
	$success = 'Du hast den Benutzer "'.$loginsystem->getUser('username', $id).'" erfolgreich ge&auml;ndert!';
}

if($p == 'userlist' && $h == 'rm_avatar_success' && !empty($id)){
	$success = 'Du hast das Profilbild des Benutzers "'.$loginsystem->getUser('username', $id).'" erfolgreich entfernt!';
}

// Kontakt

if($p == 'contact' && $h == 'contact_success'){
	$success = 'Du hast die Nachricht erfolreich gesendet!';
}

// Einstellungen

if($p == 'settings' && $h == 'settings_saved'){
	$success = 'Du hast die Einstellungen erfolreich gespeichert!';
}

// Rangverwaltung

if($p == 'ranks' && $h == 'new_rank_successfully'){
	$success = 'Du hast einen neuen Rang erfolgreich angelegt.';
}

if($p == 'ranks' && $h == 'edit_rank_successfully'){
	$success = 'Du hast den Rang erfolgreich bearbeitet.';
}

if($p == 'ranks' && $h == 'remove_rank_successfully'){
	$success = 'Du hast den Rang erfolgreich gel&ouml;scht.';
}

if($p == 'ranks' && $h == 'move_rank_successfully'){
	$success = 'Du hast den Rang erfolgreich verschoben.';
}

if($p == 'ranks' && $h == 'special_rank_successfully'){
	$success = 'Du hast den Rang erfolgreich als Standard gesetzt.';
}

// Restore

if($v == 'restore' && $h == 'restore_successfully'){
	$success = 'Du hast die &Auml;nderung erfolgreich r&uuml;ckg&auml;ngig gemacht!';
}

// User activate self

if($h == 'activate_successfully'){
	$success = 'Du hast dein Benutzerkonto erfolgreich freigeschaltet!';
}

// User remove self

if($h == 'user_remove_self_successfully'){
	$success = 'Du hast dein Benutzerkonto erfolgreich gel&ouml;scht!';
}


// Rules | Regelverwaltung

if($p == 'rules'){
	if($h == 'rule_add_successfully'){
		$success = 'Du hast die Regel erfolgreich hinzugef&uuml;gt!';
	}
	
	if($h == 'rule_edit_successfully'){
		$success = 'Du hast die Regel erfolgreich gespeichert!';
	}
	
	if($h == 'rule_remove_successfully'){
		$success = 'Du hast die Regel erfolgreich entfernt!';
	}
}

// Sites | Seiten verwalten
if($p == 'sites'){
	if($h == 'site_add_successfully'){
		$success = 'Du hast die Seite erfolgreich hinzugef&uuml;gt!';
	}
	
	if($h == 'site_edit_successfully'){
		$success = 'Du hast die Seite erfolgreich bearbeitet!';
	}
	
	if($h == 'site_add_successfully_upl_error'){
		$success = 'Du hast die Seite erfolgreich hinzugef&uuml;gt! (Die Datei konnte leider nicht hochgeladen werden!)';
	}
	
	if($h == 'site_edit_successfully_upl_error'){
		$success = 'Du hast die Seite erfolgreich bearbeitet! (Die Datei konnte leider nicht hochgeladen werden!)';
	}
	
	if($h == 'site_remove_successfully'){
		$success = 'Du hast die Seite erfolgreich entfernt!';
	}
}

// Menu | Menue verwalten
if($p == 'menu'){
	if($h == 'menu_add_successfully'){
		$success = 'Du hast den Men&uuml;punkt erfolgreich hinzugef&uuml;gt!';
	}
	
	if($h == 'menu_edit_successfully'){
		$success = 'Du hast den Men&uuml;punkt erfolgreich bearbeitet!';
	}
	
	if($h == 'menu_remove_successfully'){
		$success = 'Du hast den Men&uuml;punkt erfolgreich entfernt!';
	}
	
	if($h == 'reset_positions_successfully'){
		$success = 'Du hast die Positionen der Men&uuml;punkte erfolgreich zur&uuml;ckgesetzt!';
	}
	
	if($h == 'fill_gaps_successfully'){
		$success = 'Du hast die Lücken in den Positionen der Men&uuml;punkte erfolgreich geschlossen! '.$a.' Probleme gefunden und behoben.';
	}
}

if($p == 'additional_fields'){
	if($h == 'additional_fields_add_successfully'){
		$success = 'Du hast das Zusatzfeld erfolgreich angelegt!';
	}
	if($h == 'additional_fields_edit_successfully'){
		$success = 'Du hast das Zusatzfeld erfolgreich bearbeitet!';
	}
	if($h == 'additional_fields_delete_successfully'){
		$success = 'Du hast das Zusatzfeld erfolgreich gel&ouml;scht!';
	}
}


