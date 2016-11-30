<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bonfire
 *
 * An open source project to allow developers to jumpstart their development of
 * CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2015, Bonfire Dev Team
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @link      http://cibonfire.com
 * @since     Version 1.0
 */

/**
 * Emailer language file (English)
 *
 * Localization strings used by Bonfire's Emailer module.
 *
 * @package Bonfire\Modules\Emailer\Language\English
 * @author  Bonfire Dev Team
 * @link    http://cibonfire.com/docs/developer/emailer
 */

$lang['emailer_template'] = 'Sjabloon';
$lang['emailer_email_template'] = 'E-mail Sjabloon';
$lang['emailer_emailer_queue'] = 'E-mail wachtrij';
$lang['emailer_email_test'] = 'Test E-mail';

$lang['emailer_system_email'] = 'Systeem e-mail';
$lang['emailer_system_email_note'] = 'E-mail waar alle systeem-gegenereerde mails worden verzonden.';
$lang['emailer_email_server'] = 'E-mail server';
$lang['emailer_settings'] = 'E-mail instellingen';
$lang['emailer_settings_note'] = '<b>Mail</b> maakt gebruik van de standaard PHP mail functie, zodat geen instellingen nodig.';
$lang['emailer_location'] = 'locatie';
$lang['emailer_server_address'] = 'Server Adres';
$lang['emailer_port'] = 'Poort';
$lang['emailer_timeout_secs'] = 'Timeout (seconden)';
$lang['emailer_email_type'] = 'E-mail type';
$lang['emailer_save_settings'] = 'Sla instellingen op';
$lang['emailer_test_settings'] = 'Test e-mail instellingen';
$lang['emailer_sendmail_path'] = 'Sendmail pad';
$lang['emailer_smtp_address'] = 'SMTP Server adres';
$lang['emailer_smtp_username'] = 'SMTP gebruikersnaam';
$lang['emailer_smtp_password'] = 'SMTP wachtwoord';
$lang['emailer_smtp_port'] = 'SMTP poort';
$lang['emailer_smtp_timeout'] = 'SMTP timeout';
$lang['emailer_smtp_timeout_secs'] = 'SMTP timeout (seconden)';

$lang['emailer_template_note'] = 'E-mails worden verzonden in HTML-formaat. Ze kunnen worden aangepast door het bewerken van de kop- en voettekst, hieronder.';
$lang['emailer_header'] = 'Koptekst';
$lang['emailer_footer'] = 'Voettekst';
$lang['emailer_save_template'] = 'Sla sjabloon op';

$lang['emailer_test_header'] = 'Test uw instellingen';
$lang['emailer_test_intro'] = 'Geef een e- mailadres hieronder in om te controleren of uw e-mailinstellingen werken. <br/>Sla huidige instellingen op voor het testen.';
$lang['emailer_test_button'] = 'Verzend Test E-mail';
$lang['emailer_test_result_header'] = 'Test resultaten';
$lang['emailer_test_debug_header'] = 'Debug informatie';
$lang['emailer_test_success'] = 'De e-mail lijkt correct ingesteld. Als u niet de e-mail niet ontvangt, probeer te kijken in uw Spam of Ongewenste e-mail.';
$lang['emailer_test_error'] = 'De e-mail lijkt niet juist ingesteld.';

$lang['emailer_test_mail_subject'] = 'Gefeliciteerd! Uw Bet Stars Emailer is werken!';
$lang['emailer_test_mail_body'] = 'Als u deze e-mail ziet, Dan blijkt uw Bet Stars Emailer te werken!';

$lang['emailer_stat_no_queue'] = 'Je hebt momenteel geen e-mails in de wachtrij.';
$lang['emailer_total_in_queue'] = 'Totaal aantal E-mails in wachtrij:';
$lang['emailer_total_sent'] = 'Totaal aantal E-mails verzonden:';
$lang['emailer_force_process'] = 'Verwerk Nu';
$lang['emailer_insert_test'] = 'Voeg test e-mail in';

$lang['emailer_sent'] = 'Verzonden?';
$lang['emailer_attempts'] = 'Pogingen';
$lang['emailer_id'] = 'ID';
$lang['emailer_to'] = 'Naar';
$lang['emailer_subject'] = 'Onderwerp';
$lang['emailer_email_subject'] = 'E-mail Onderwerp';
$lang['emailer_email_content'] = 'E-mail inhoud';

$lang['emailer_missing_data'] = 'Eén of meer verplichte velden ontbreken.';
$lang['emailer_no_debug'] = 'E-mail is in de wachtrij. Geen debug gegevens beschikbaar.';

$lang['emailer_delete_success'] = '%d records verwijderd.';
$lang['emailer_delete_failure'] = 'Kan records niet verwijderen: %s';
$lang['emailer_delete_error'] = 'Fout bij verwijderen records: %s';
$lang['emailer_delete_confirm'] = 'Bent u zeker dat u deze e-mail wilt verwijderen?';
$lang['emailer_delete_none'] = 'Geen berichten geselecteerd om te verwijderen.';

$lang['emailer_create_email'] = 'Verzend nieuwe E-mail';
$lang['emailer_create_setting'] = 'E-mail configuratie';
$lang['emailer_create_email_error'] = 'Fout bij aanmaken e-mails: %s';
$lang['emailer_create_email_success'] = 'E-mail(s) zijn toegevoegd aan de wachtrij';
$lang['emailer_create_email_queued'] = '%s e-mails zijn toegevoegd aan de wachtrij';
$lang['emailer_create_email_failure'] = 'Fout bij maken e-mails: %s';
$lang['emailer_create_email_no_users'] = 'Geen gebruikers geselecteerd als ontvangers';

$lang['emailer_validation_errors_heading'] = 'Gelieve de volgende fouten te verbeteren:';
$lang['emailer_no_users_found'] = 'Geen gebruikers gevonden die overeenkomen met uw selectie.';
$lang['emailer_queue_debug_heading'] = 'E-mail debugger';
$lang['emailer_queue_debug_error'] = 'Er is een fout opgetreden het verzenden van e-mails van de wachtrij. De resultaten worden hieronder weergegeven.';

$lang['emailer_general_settings'] = 'Algemene instellingen';
$lang['emailer_mailtype_text'] = 'Tekst';
$lang['emailer_mailtype_html'] = 'HTML';
$lang['emailer_protocol_mail'] = 'mail';
$lang['emailer_protocol_sendmail'] = 'sendmail';
$lang['emailer_protocol_smtp'] = 'SMTP';

$lang['emailer_settings_save_error'] = 'Er is een fout opgetreden het opslaan van uw instellingen.';
$lang['emailer_settings_save_success'] = 'E-mail instellingen succesvol opgeslagen.';

$lang['form_validation_emailer_system_email'] = 'Systeem e-mail';
$lang['form_validation_emailer_email_server'] = 'E-mail server';
$lang['form_validation_emailer_sendmail_path'] = 'Sendmail pad';
$lang['form_validation_emailer_smtp_address'] = 'SMTP Server Adres';
$lang['form_validation_emailer_smtp_username'] = 'SMTP gebruikersnaam';
$lang['form_validation_emailer_smtp_password'] = 'SMTP Wachtwoord';
$lang['form_validation_emailer_smtp_port'] = 'SMTP Poort';
$lang['form_validation_emailer_smtp_timeout'] = 'SMTP Timeout';
