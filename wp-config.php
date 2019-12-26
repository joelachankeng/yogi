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
define( 'DB_NAME', 'yogi' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'sIhmrx4vGz][77OFUx,5CP&{ZY,s![L}iIk!,pToP;.d0MmQ2pSs4|;b>Ka_[L|1' );
define( 'SECURE_AUTH_KEY',  't.~$?CWgPyvuW/q||^KA)21%iOgRU}C-Lqt$U,NCNd(k<2v8Z9r2ng`@{:KG?6Qg' );
define( 'LOGGED_IN_KEY',    '%2Y86kW.j9%6nF<dkccf3&>{9$:{K`.W^xenrgI5;)$v gYzq^P?m;qHnvzus{B2' );
define( 'NONCE_KEY',        ']G&0Z2:~EIYBX:7XH/hZv`Q4h3)8;.B!Xm6Fi93WH,X>`UV;g>H*`5V`JvWNq7#}' );
define( 'AUTH_SALT',        ']SwA#Kb.w/`I{+jPw1|H{MhjF`S< .#Wi@PGonnn0]!UUpJvYq 3TiEVCf$,*PHg' );
define( 'SECURE_AUTH_SALT', 'a,.`t$96q;tzQ;(1 Jn=j]vX@@zfbc&h,d=7Nz8Z@B2N7MkNJh/XBr6Q|>13n?w*' );
define( 'LOGGED_IN_SALT',   '(S2HQstovE.)Lg_WUv;*Hmkyn;Lvg:P%M[N.<fZGl|a6jO4oM[v9DC67V2pizi%|' );
define( 'NONCE_SALT',       '8E MY@d>FwOTlShQLoasrN|sbRP_|JN4)@%y|iFFtY-9Bvb~Rn`.-/FWxqE]`-ut' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
