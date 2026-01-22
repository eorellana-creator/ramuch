<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "ramuchcl_web" );
/** Database username */
define( 'DB_USER', "ramuchcl_ramuch" );
/** Database password */
define( 'DB_PASSWORD', "ramuch2024#1105" );
/** Database hostname */
define( 'DB_HOST', "localhost" );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
define('FS_METHOD', 'direct');
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
define( 'AUTH_KEY',         ':vMndw@T5U!x4:uL8qar,<d9smbs<rzUMGk=Ve_6RR%QACv9Km6K:;yhjzt:_x0F' );
define( 'SECURE_AUTH_KEY',  '`J-<mB{)XWk+I1c+W}.eedVfjklI8*6y9L> s&:om,In55S>Ai46RGSO/^~4di4j' );
define( 'LOGGED_IN_KEY',    'TK&J]Z)7L{+JZ!k9h;RUM;`Pu1zD6&F@_Wnt8`cO|V-&W]Jld>^jbdx6I|#(Kjs8' );
define( 'NONCE_KEY',        ']yC]gZ5@]hrOSl*L{E[5lbuJ@+{L1[g9QlAdNAD)`2IB<f{&&K@fYJ&$!f+N`?{.' );
define( 'AUTH_SALT',        'KgbnUx//|!.qPgZiX63[@7.E`(Be&]c{&WP~*w|{Yk&84>S^v,onNfsKMPcu1*}#' );
define( 'SECURE_AUTH_SALT', 'OOpdA2ye#Km_arV}1c6%Wx1`!qm}hMsK8p-&{}[|Bk>/.?89jd3J=@v;D0>jv$n3' );
define( 'LOGGED_IN_SALT',   'ZF.:0i(Ptm659OMev=[@m*DN/qk~_o9*f2z-&j~; e+W%fLXe=(.z~rh^G^!{20;' );
define( 'NONCE_SALT',       'B^lp#fE|k2rd/AD=[=rkZb mRD5{yAmxjTBFR,C)|}K}?|R.Zi=~=Bx.]m#G^[>z' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ramuch_';
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
/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
