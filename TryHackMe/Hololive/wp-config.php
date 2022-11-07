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
define( 'DB_USER', 'admin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'DBManagerLogin!' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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

define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );
*/
define('AUTH_KEY',         'c 8Ui7]??:^)!64-*duMT<JE=j^qG|,`jn<|c,G[-UG4]6lveE|JAMs s,f~]Bus');
define('SECURE_AUTH_KEY',  'y={Qrj;rk-guTo&G0lIm7TGPrSg+,J2CBRf2F<9#(O.:~2XR5 2$nTP|{5X|jP+*');
define('LOGGED_IN_KEY',    'c.G5Xn>d~Dc^<o]?S&hHY,:-nubV.D%V6&g734%#ht8t>+@0FONb+k$iz%BB.tTX');
define('NONCE_KEY',        'k2F.{B@kOl2Vzm]k66*}G&%(ywH1Hj-{KP`/#R_B9Rl (HLC`Dy=_2Ol1LfbB&+E');
define('AUTH_SALT',        'wq)-kpAUMl!e{XI$/g e8HKMP,$vyi-@(@e77CK%N|)@z9oTz}%/WXoF0)5|ye<>');
define('SECURE_AUTH_SALT', 'W&|w$+ EmvLO6o/$|{lU&ACm+Wazf5Y3`.csw.[0/k#HgW)K,5n?2r-a|<DH{h3?');
define('LOGGED_IN_SALT',   '+,.gb4Cd7S?u.g 0`e_h}FXpBW|COgv-GG>{F-I@#$2p.|#oYjQ/{L]e3g:gvZ)e');
define('NONCE_SALT',       'CF;H:Q-w+_&@)vQJ;V0kZy<-MhBhD_MB#x)sCOU}Y(( E:ZkKx-kNxh[u01y.OuR');
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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
