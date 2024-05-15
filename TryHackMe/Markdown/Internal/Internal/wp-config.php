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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wordpress' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wordpress123' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'No9]-c] _7M5ae[&|ow)97dfBLUV1G8AakB)?#XIN:W`y4?tgN,DOoC8 mD/)8vh' );
define( 'SECURE_AUTH_KEY',  'xs.zSjNj^a: zpzBLb@r[u65WA9uNd:vLXtLs^>@q38*x.kVxr g,yoGlOpd%Xde' );
define( 'LOGGED_IN_KEY',    'rZU=>v+8g,ey/*Q;c**79^K14&M@2-IDB)DknMf7<a/;hviCw?kRv=MW5lk.vSoG' );
define( 'NONCE_KEY',        '8v={}7jgkSu|D[Nfy]y}>MX}60oSjSMn^qC2rW%V,3|Fg0TJrB6m4}Mb>V@[pZ<w' );
define( 'AUTH_SALT',        'ASOB>S,c3MiYiYSh!;My@BaY7MYRQRI}/~ZC6k?9^e7/jCB00r@Z0)Oe@gQ8Trk*' );
define( 'SECURE_AUTH_SALT', 'd(=umc=!qOCnjIvr~_T_(Ia5.mG6VGF~ktdtt1uzj6A$KJsEAAA5k7.(zFgLa96[' );
define( 'LOGGED_IN_SALT',   '~A,!e|5RGqu!KB=/1R4TN_tcGuK}+]]I_p`FZ[(~L0rv_OY#EItD)tC [hM|l|0z' );
define( 'NONCE_SALT',       'H+T|fK,+u K}_qDTs,ob{,h0TLbd}#pwksNuBzu9~Kw<GcDnJiMYm}[AvPQVTr_,' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

