<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress-4-7-2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'LOflower');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'LZMm UTN>bQL5q5]G0(X<Y04=(d !V&?)91G7iTX<ej/ fe{oVUf7s@I~3:X,8Q1');
define('SECURE_AUTH_KEY',  'L-cM~Fn? #~SP3_-*`4sY/9`R pb+}P7?u`-nbJOix%$M=FlUH(?siN4t/Vyc4|a');
define('LOGGED_IN_KEY',    'YJF4%6MCiu<O(DIe:X6%h2qS!c(cPX@c:z*:4^EN +d6uJy;fiqP!zEwq^3WXQPS');
define('NONCE_KEY',        'fyee,n.Z0E=R{kgMU?vJ4(t*sZ?UAC-BWL~v9v`:fgdxCP?+hv8?0K<L6RBa##sO');
define('AUTH_SALT',        'V~!#@{/9QO4Hu.k#%(A5/@b*)9%@7wQzw3]$1kcM)5|;q!z-zM-4f0;3mf~e:H,P');
define('SECURE_AUTH_SALT', 'ag#P%By0TK#2hCcHe05;rIIj{CA2FuM+J46~z(bVyo0^QL3Z;btROwVN)Gf!=D,o');
define('LOGGED_IN_SALT',   'Z3GK@)q26_ -FJ8I0$UGUy0vt@Kr]c9fa2^f(DI<_rtf+v 3mh]%B #*$}?wr>^D');
define('NONCE_SALT',       '@yQKNl ~NtI26rW){Q5J#|Q?I]w0~zA(3=j>=h$c1qR69hr2X|now$2I#NVL3_Zj');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
