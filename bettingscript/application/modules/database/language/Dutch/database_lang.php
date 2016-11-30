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
 * @filesource
 */

/**
 * Language file for the Database Module (English)
 *
 * @package    Bonfire\Modules\Database\Language\English
 * @author     Bonfire Dev Team
 * @link       http://cibonfire.com/docs
 */

// Sub_nav titles
$lang['database_backups'] = 'Backups';
$lang['database_maintenance'] = 'Onderhoud';
$lang['database_migrations'] = 'Migraties';

$lang['database_backup'] = 'Backup';
$lang['database_backup_delete_confirm'] = 'De volgende back-up bestanden echt te verwijderen ?';
$lang['database_backup_delete_none'] = 'Geen back-up bestanden werden geselecteerd om te verwijderen';
$lang['database_backup_deleted_count'] = '%s backup bestanden werden verwijderd.';
$lang['database_backup_deleted_error'] = 'Een of meer bestanden konden niet worden verwijderd.';
$lang['database_backup_failure_validation'] = 'Er is een probleem ontstaan bij opslaan van het backup bestand. Validatie gefaald.';
$lang['database_backup_failure_write'] = 'Er was een probleem met het opslaan van de back-upbestand. Ofwel kon het bestand niet worden weggeschreven, of de directory werd niet gevonden.';
$lang['database_backup_no_tables'] = 'Geen tabellen werden geselecteerd voor backup.';
$lang['database_backup_success'] = 'Backup bestand werd succesvol opgeslaan. U kunt het vinden op <a href="%s">%s</a>.';
$lang['database_backup_warning'] = 'Let op: Als gevolg van de beperkte uitvoeringstermijn en geheugen beschikbaar voor PHP, back-up van zeer grote databases wellicht niet mogelijk is. Als uw database is erg groot dat u misschien nodig hebt om back-up direct van uw SQL server via de command line, of je serverbeheerder het voor je doen als je geen root rechten hebt.';
$lang['database_add_inserts'] = 'Voeg toe te voegen';

$lang['database_compress_question'] = 'Compressie type?';
$lang['database_compress_type'] = 'Compressie type';
$lang['database_gzip'] = 'gzip';
$lang['database_zip'] = 'zip';

$lang['database_data_free'] = 'Data Vrij';
$lang['database_data_free_unsupported'] = 'Niet beschikbaar';
$lang['database_data_size'] = 'Data grootte';
$lang['database_data_size_unsupported'] = 'Niet beschikbaar';
$lang['database_engine'] = 'Engine';
$lang['database_index_size'] = 'Index Grootte';
$lang['database_index_field_unsupported'] = 'Niet beschikbaar';
$lang['database_num_records'] = '# Records';

$lang['database_drop'] = 'Laat vallen';
$lang['database_drop_attention'] = '<p>Tabellen verwijderen uit de database kan leiden tot verlies van gegevens. </p><p><strong> Dit kan uw aanvraag niet-functioneel te maken. </strong></p>';
$lang['database_drop_button'] = 'Laat Tabel(len) vallen';
$lang['database_drop_confirm'] = 'Volgende databank tabellen echt verwijderen?';
$lang['database_drop_none'] = 'Geen tabellen geselecteerd voor verwijderen';
$lang['database_drop_question'] = 'Add &lsquo;Drop Tables&rsquo; command naar SQL?	';
$lang['database_drop_success_plural'] = '%s tabellen succesvol laten vallen';
$lang['database_drop_success_singular'] = '%s tabel succesvol laten vallen';
$lang['database_drop_tables'] = 'Laat tabellen vallen';
$lang['database_drop_title'] = 'Laat databank tabellen vallen';

$lang['database_optimize'] = 'Optimaliseer';
$lang['database_optimize_failure'] = 'Optimaliseren databank niet mogelijk';
$lang['database_optimize_success'] = 'Databank werd succesvol geoptimaliseerd';

$lang['database_repair'] = 'Repareren';
$lang['database_repair_none'] = 'Geen tabellen werden geselecteerd voor herstel';
$lang['database_repair_success'] = '%s of %s tabellen werden succesvol hersteld.';

$lang['database_restore'] = 'Herstel';
$lang['database_restore_attention'] = '<p>Een databank herstellen vanaf een back-upbestand zal resulteren in sommige of alle van uw database worden gewist voordat het herstelt.</p><p><strong>Dit kan resulteren in dataverlies.</strong>.</p>';
$lang['database_restore_file'] = 'Herstel databank van bestand: <span class=\'filename\'>%s</span>?';
$lang['database_restore_note'] = 'De optie Restore is alleen geschikt voor het lezen van niet gecomprimeerde bestanden. Gzip en Zip -compressie is goed als je gewoon een back-up wil downloaden en opslaan op uw computer.';
$lang['database_restore_out_successful'] = '<strong class="text-success">Successvolle Query</strong>: <span class="small">%s</span>';
$lang['database_restore_out_unsuccessful'] = '<strong class="text-error">Niet successvolle Query</strong>: <span class="small">%s</span>';
$lang['database_restore_read_error'] = 'Kan het bestand niet lezen: %s.';
$lang['database_restore_results'] = 'Resultaten herstellen';

$lang['database_title_backup_create'] = 'Maak nieuwe backup';
$lang['database_title_backups'] = 'Databank backups';
$lang['database_title_maintenance'] = 'Databank onderhoud';
$lang['database_title_restore'] = 'Databank hersteld';

$lang['database_apply'] = 'Toepassen';
$lang['database_back_to_tools'] = 'Terug naar Databank gereedschap';
$lang['database_browse'] = 'Verkennen: %s';
$lang['database_filename'] = 'Bestandsnaam';
$lang['database_get_backup_error'] = '%s kon niet worden gevonden.';
$lang['database_insert_question'] = 'Add &lsquo;Inserts&rsquo; for data to SQL?	';
$lang['database_link_title_download'] = 'Download %s';
$lang['database_link_title_restore'] = 'Herstel %s';
$lang['database_no_backups'] = 'Geen vorige backups gevonden.';
$lang['database_no_rows'] = 'Geen data gevonden voor tabel';
$lang['database_no_table_name'] = 'Geen tabel naam werd verstrekt.';
$lang['database_no_tables'] = 'Geen tabellen werden gevonden voor de huidige databank.';
$lang['database_sql_query'] = 'SQL Query';
$lang['database_table_name'] = 'Naam Tabel';
$lang['database_tables'] = 'Tabellen';
$lang['database_total_results'] = 'Totaal resultaten: %s';

$lang['database_backup_tables'] = 'Backup tabellen';

$lang['database_validation_errors_heading'] = 'Gelieve de volgende fouten te verbeteren:';
$lang['database_action_unknown'] = 'Een niet-ondersteunde actie werd gekozen.';

$lang['form_validation_database_filename'] = 'Volledige naam';
$lang['form_validation_database_tables'] = 'Tabellen';

// -----------------------------------------------------------------------------
// The remaining items appear to no longer be in use...
// -----------------------------------------------------------------------------
$lang['database_advanced_options'] = 'Geavanceerde opties';
$lang['database_cache_dir'] = 'Cache map';
$lang['database_database'] = 'Databank';
$lang['database_database_settings'] = 'Databank instellingen';
$lang['database_dbname'] = 'Naam databank';
$lang['database_debug_on'] = 'Debuggen Op';
$lang['database_delete_note'] = 'Verwijder gekozen backup bestanden:';
$lang['database_display_errors'] = 'Toon databank fouten';
$lang['database_driver'] = 'Stuurprogramma';
$lang['database_enable_caching'] = 'Schakel query caching in';
$lang['database_erroneous_save'] = 'Er is een fout opgetreden het opslaan van de instellingen.';
$lang['database_erroneous_save_act'] = 'Databank instellingen werden niet correct opgeslaan';
$lang['database_hostname'] = 'Hostnaam';
$lang['database_persistent'] = 'Persistent';
$lang['database_persistent_connect'] = 'Persistente connectie';
$lang['database_prefix'] = 'Prefix';
$lang['database_records'] = 'Records';
$lang['database_running_on_1'] = 'U draait op de';
$lang['database_running_on_2'] = 'server.';
$lang['database_serv_dev'] = 'Ontwikkeling';
$lang['database_serv_prod'] = 'Productie';
$lang['database_serv_test'] = 'Testen';
$lang['database_server_type'] = 'Server type';
$lang['database_servers'] = 'Servers';
$lang['database_strict_mode'] = 'Stricte mode';
$lang['database_successful_save'] = 'Uw instellingen werden succesvol opgeslaan.';
$lang['database_successful_save_act'] = 'Databank instellingen werder succesvol opgeslaan.';
