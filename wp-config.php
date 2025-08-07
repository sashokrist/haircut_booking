<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'booking' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'W5KITPST(o<&pct1R/PCV0OT]!PI:0m(LAV#.Fo&*0`4F`(`=jA-7=ZG1YY@}jmw' );
define( 'SECURE_AUTH_KEY',  '<]oAlg8W55maew4}(56ld]Jrpez,u&#GSga`h<D4-ENCKAaBrKW]5bOREcMC1}%G' );
define( 'LOGGED_IN_KEY',    '}}vJ+>qT?Thd=RR/PnURlGcR$Sp.v%i683`Fu[{(V$EC{C6Wn%(bZ.%<i$k6/AVT' );
define( 'NONCE_KEY',        '}nx9Rw]uiOFT,xI;i-Fm]J4>fS6qrJiaDcf!:bZ[.,X.G<ui/g=:x &L8ifux#*B' );
define( 'AUTH_SALT',        ' N&{SS{v@Q[<R4>=L~gKu)zvUPWPukZTcw6&TC#|_~7z4[x.-,g EYPAd&:;qXr:' );
define( 'SECURE_AUTH_SALT', '`w#i]y9o7yeIQ:bM_t=EW.S8,Wuu!uJwQ.K7`M8.8ro>_@Z Bsi@]mzd*u::#V-|' );
define( 'LOGGED_IN_SALT',   'lWV-&BN74Mjs9)S9a5>aZ+QbDPai)44;qN|Ye~<M#0nB&W,Gw~|gja2Ls}C5{^7$' );
define( 'NONCE_SALT',       '><ZY!L+ApZzdcf7B3?Bo<7myOOtgPmnQ77_oDe47J(jP[v94P@p>l]7:ZuKjnHkf' );

define('WP_HOME', 'http://haircut-booking.test');
define('WP_SITEURL', 'http://haircut-booking.test');

define('FS_METHOD', 'direct');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
