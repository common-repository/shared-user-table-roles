=== Shared User Table Roles ===
Contributors: wp_joris
Tags: users, plugin, user, wp-config, roles, meta, usermeta, database
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share user roles when sharing the user table between multiple installations.

== Description ==

When sharing the user and usermeta table between multiple wordpress 
installations using the same database, any role given to the user is only valid 
for a single wordpress installation.

This plugin ensures user roles, user capabilities and user settings are 
shared between the multiple installations. This is configured using a wp-config 
constant.

Any role set in any of the installations will be reflected in all other 
installations.

Caveats:

* Compatability with wordpress multisite is unknown

== Installation ==

To set up multiple wordpress installations using the same database:

1. Install all wordpress installations using the same database, but using a 
   different $table_prefix in wp_config.
   
   For example two installations with "eng_" and "nld_".
   
2. Designate one wordpress installation as "primary". All other installations are
   secondary
   
   For example the installation with the prefix "eng_".
   
3. Install and activate this plugin on all secondary wordpress installations.
   
   In this example "nld_".
   
4. On all secondary wordpress installations configure CUSTOM_USER_TABLE and 
   CUSTOM_USER_META_TABLE in wp-config to use the tables (users and usermeta) 
   of the primary installation.
   
   See below for an example
   
5. On all secondary wordpress installations, set SHARED_USER_TABLE_ROLES_PREFIX 
   to be equal to the table prefix of the primary installation.


= Primary =
For example the relevant wp-config settings for the primary installation would 
look like:

        $table_prefix  = 'eng_';

= Secondary =
For example the relevant wp-config settings for secondary installations would 
look like:

        $table_prefix  = 'nld_';
        define('SHARED_USER_TABLE_ROLES_PREFIX', 'eng_');
        define('CUSTOM_USER_TABLE', 'eng_users');
        define('CUSTOM_USER_META_TABLE', 'eng_usermeta');



== Changelog ==

= 1.0 =
* initial release

