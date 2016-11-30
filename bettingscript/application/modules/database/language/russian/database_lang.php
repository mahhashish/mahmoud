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
$lang['database_backups'] = 'Резервные копии';
$lang['database_maintenance'] = 'Обслуживание';
$lang['database_migrations'] = 'Миграции';

$lang['database_backup'] = 'Резервное копирование';
$lang['database_backup_delete_confirm'] = 'Действительно удалить следующие файлы резервных копий?';
$lang['database_backup_delete_none'] = 'Резервных копий файлов, не выбранные для удаления';
$lang['database_backup_deleted_count'] = '%s файлы резервной копии были удалены.';
$lang['database_backup_deleted_error'] = 'Один или несколько файлов не могут быть удалены.';
$lang['database_backup_failure_validation'] = 'Возникла проблема сохранения файла резервной копии. Проверка не удалась.';
$lang['database_backup_failure_write'] = 'Возникла проблема сохранения файла резервной копии. Либо файл не может быть записан или каталог не был найден.';
$lang['database_backup_no_tables'] = 'Таблицы не были выбраны для резервного копирования';
$lang['database_backup_success'] = 'Файл резервной копии успешно сохранен. Его можно найти на сайте <a href="%s">%s</a>.';
$lang['database_backup_warning'] = 'Примечание: Из-за ограниченного времени выполнения и памяти, доступной для PHP, резервное копирование очень больших баз данных может оказаться невозможным. Если ваша база данных очень велика вам может понадобиться для резервного копирования непосредственно с вашего сервера SQL с помощью командной строки, или ваш сервер администратора сделать это для вас, если вы не имеете привилегии суперпользователя.';
$lang['database_add_inserts'] = 'Добавить Вставки';

$lang['database_compress_question'] = 'Добавить тип сжатия?';
$lang['database_compress_type'] = 'Тип сжатия';
$lang['database_gzip'] = 'gzip';
$lang['database_zip'] = 'zip';

$lang['database_data_free'] = 'Данные свободные';
$lang['database_data_free_unsupported'] = 'N/A';
$lang['database_data_size'] = 'размер данных';
$lang['database_data_size_unsupported'] = 'N/A';
$lang['database_engine'] = 'двигатель';
$lang['database_index_size'] = 'Индекс Размер';
$lang['database_index_field_unsupported'] = 'N/A';
$lang['database_num_records'] = '# записей';

$lang['database_drop'] = 'сбрасывать';
$lang['database_drop_attention'] = '<p>Удаление таблиц из базы данных приведет к потере данных.</p><p><strong>Это может сделать ваше приложение нефункциональным.</strong></p>';
$lang['database_drop_button'] = 'Drop Table(s)';
$lang['database_drop_confirm'] = 'Действительно удалить следующие таблицы базы данных?';
$lang['database_drop_none'] = 'Таблицы не были выбраны, чтобы сбрасывать';
$lang['database_drop_question'] = 'Add &lsquo;Drop Tables&rsquo; command to SQL?';
$lang['database_drop_success_plural'] = '%s таблицы успешно сняты.';
$lang['database_drop_success_singular'] = '%s таблица успешно отброшенa.';
$lang['database_drop_tables'] = 'удалять таблицы';
$lang['database_drop_title'] = 'Отбросьте таблиц базы данных';

$lang['database_optimize'] = 'оптимизировать';
$lang['database_optimize_failure'] = 'Невозможно оптимизировать базу данных.';
$lang['database_optimize_success'] = 'База данных была успешно оптимизирована.';

$lang['database_repair'] = 'ремонт';
$lang['database_repair_none'] = 'Таблицы не были выбраны для ремонта';
$lang['database_repair_success'] = '%s of %s Таблицы базы данных были успешно восстановлены.';

$lang['database_restore'] = 'Восстановить';
$lang['database_restore_attention'] = '<p>Restoring a database from a backup file will result in some or all of your database being erased before restoring.</p><p><strong>This may result in a loss of data</strong>.</p>';
$lang['database_restore_file'] = 'Restore database from file: <span class=\'filename\'>%s</span>?';
$lang['database_restore_note'] = 'The Restore option is only capable of reading un-compressed files. Gzip and Zip compression is good if you just want a backup to download and store on your computer.';
$lang['database_restore_out_successful'] = '<strong class="text-success">Successful Query</strong>: <span class="small">%s</span>';
$lang['database_restore_out_unsuccessful'] = '<strong class="text-error">Unsuccessful Query</strong>: <span class="small">%s</span>';
$lang['database_restore_read_error'] = 'Не удалось прочитать файл: %s.';
$lang['database_restore_results'] = 'Восстановление результатов';

$lang['database_title_backup_create'] = 'Создать новую резервную копию';
$lang['database_title_backups'] = 'Резервное копирование баз данных';
$lang['database_title_maintenance'] = 'Ведение базы данных';
$lang['database_title_restore'] = 'Восстановление базы данных';

$lang['database_apply'] = 'применять';
$lang['database_back_to_tools'] = 'Назад в раздел Инструменты баз данных';
$lang['database_browse'] = 'Просматривать: %s';
$lang['database_filename'] = 'Имя файла';
$lang['database_get_backup_error'] = '%s невозможно найти.';
$lang['database_insert_question'] = 'Добавить &lsquo;Inserts&rsquo; для данных SQL?';
$lang['database_link_title_download'] = 'Скачать %s';
$lang['database_link_title_restore'] = 'Восстановить %s';
$lang['database_no_backups'] = 'Не было обнаружено предыдущие резервные копии.';
$lang['database_no_rows'] = 'Нет данных для таблицы.';
$lang['database_no_table_name'] = 'Не было представлено имя таблицы.';
$lang['database_no_tables'] = 'Таблицы не были найдены для текущей базы данных.';
$lang['database_sql_query'] = 'SQL-запрос';
$lang['database_table_name'] = 'Название таблицы';
$lang['database_tables'] = 'таблицы';
$lang['database_total_results'] = 'Всего результатов: %s';

$lang['database_backup_tables'] = 'Резервное копирование таблицы';

$lang['database_validation_errors_heading'] = 'Пожалуйста, исправьте следующие ошибки:';
$lang['database_action_unknown'] = 'Неподдерживаемый действия был выбран.';

$lang['form_validation_database_filename'] = 'Имя файла';
$lang['form_validation_database_tables'] = 'таблицы';

// -----------------------------------------------------------------------------
// The remaining items appear to no longer be in use...
// -----------------------------------------------------------------------------
$lang['database_advanced_options'] = 'Расширенные опции';
$lang['database_cache_dir'] = 'Каталог кэша';
$lang['database_database'] = 'База данных';
$lang['database_database_settings'] = 'Настройки базы данных';
$lang['database_dbname'] = 'Имя базы данных';
$lang['database_debug_on'] = 'Debug On';
$lang['database_delete_note'] = 'Удалить выбранные файлы резервного копирования:';
$lang['database_display_errors'] = 'Показывать ошибки базы данных';
$lang['database_driver'] = 'драйвер';
$lang['database_enable_caching'] = 'Включите Query Caching';
$lang['database_erroneous_save'] = 'Возникла проблема сохранения настроек.';
$lang['database_erroneous_save_act'] = 'Database settings did not save correctly';
$lang['database_hostname'] = 'Hostname';
$lang['database_persistent'] = 'настойчивый';
$lang['database_persistent_connect'] = 'Постоянное подключение';
$lang['database_prefix'] = 'Префикс';
$lang['database_records'] = 'документация';
$lang['database_running_on_1'] = 'You are currently running on the';
$lang['database_running_on_2'] = 'server.';
$lang['database_serv_dev'] = 'Development';
$lang['database_serv_prod'] = 'Production';
$lang['database_serv_test'] = 'Testing';
$lang['database_server_type'] = 'Server Type';
$lang['database_servers'] = 'Servers';
$lang['database_strict_mode'] = 'Strict Mode';
$lang['database_successful_save'] = 'Your settings were successfully saved.';
$lang['database_successful_save_act'] = 'Database settings were successfully saved';
