<?php
/*
Plugin Name: Shared User Table Roles
Plugin URI: https://wordpress.org/extend/plugins/shared-user-table-roles/
Description: Share user roles when sharing the user table between multiple installations.
Author: wp_joris
Version: 1.0
*/

if (defined('SHARED_USER_TABLE_ROLES_PREFIX')) // constant to be used in wp-config
{
        add_filter('add_user_metadata'   , array('SharedUserTableRoles', 'add_user_metadata'   ), 10, 5);
        add_filter('update_user_metadata', array('SharedUserTableRoles', 'update_user_metadata'), 10, 5);
        add_filter('delete_user_metadata', array('SharedUserTableRoles', 'delete_user_metadata'), 10, 5);
        add_filter('get_user_metadata'   , array('SharedUserTableRoles', 'get_user_metadata'   ), 10, 4);
        add_filter('get_meta_sql'        , array('SharedUserTableRoles', 'get_meta_sql'        ), 10, 6);
}

class SharedUserTableRoles
{
        private static $keys = array('capabilities', 'user_level', 'user-settings', 'user-settings-time');
        private static $preg_quoted_keys = array('capabilities', 'user_level', 'user-settings', 'user-settings-time');
        
        private static function replaceKey($key)
        {
                global $table_prefix;
                
                if ($table_prefix === SHARED_USER_TABLE_ROLES_PREFIX)
                {
                        return false;
                }
                
                $table_prefix_len = strlen($table_prefix);
        
                if (substr($key, 0, $table_prefix_len) == $table_prefix)
                {
                        $key = substr($key, $table_prefix_len);
                        
                        if (in_array($key, self::$keys))
                        {
                                return SHARED_USER_TABLE_ROLES_PREFIX . $key;
                        }
                }
                
                return false;
        }
        
        public static function add_user_metadata($check, $user_id, $key, $value, $unique)
        {
                $newKey = self::replaceKey($key);
                if ($newKey)
                {
                        return add_metadata('user', $user_id, $newKey, $value, $unique);
                }
                
                return $check;
        }
        
        public static function update_user_metadata($check, $user_id, $key, $value, $prev_value)
        {
                $newKey = self::replaceKey($key);
                if ($newKey)
                {
                        return update_metadata('user', $user_id, $newKey, $value, $prev_value);
                }
        
                return $check;
        }
        
        public static function delete_user_metadata($check, $user_id, $key, $value, $delete_all)
        {
                $newKey = self::replaceKey($key);
                if ($newKey)
                {
                        return delete_metadata('user', $user_id, $newKey, $value, $delete_all);
                }
        
                return $check;
        }
        
        public static function get_user_metadata($check, $user_id, $key, $single)
        {
                $newKey = self::replaceKey($key);
                
                if ($newKey)
                {
                        $val = get_metadata('user', $user_id, $newKey, $single);
                        if ($single)
                        {
                                return array($val); // ...
                        }
                        else
                        {
                                return $val;
                        }
                }
        
                return $check;
        }
        
        public static function get_meta_sql($clauses, $queries, $type, $primary_table, $primary_id_column, $context)
        {
                global $table_prefix;
                
                if ($type === 'user' && $table_prefix !== SHARED_USER_TABLE_ROLES_PREFIX)
                {
                        // the get_meta_sql filter does not provide enough data 
                        // to be able to reconstruct the query (for example by creating 
                        // another instance of WP_Meta_Query).
                        // So use a more error prone regular expression instead.
                        
                        $keyRegex = implode('|', self::$preg_quoted_keys);
                
                        foreach ($clauses as $type => $sql)
                        {
                                // utf8_general_ci so use Case Insensitive matching
                                $clauses[$type] = preg_replace(
                                        '/meta_key\s*=\s*\''.$table_prefix.'('.$keyRegex.')\'/i', 
                                        "meta_key = '".SHARED_USER_TABLE_ROLES_PREFIX.'$1\'', 
                                        $clauses[$type]
                                ); 
                        }
                        
                }
                
                return $clauses;
        }
}