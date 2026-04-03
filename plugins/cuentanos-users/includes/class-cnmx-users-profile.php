<?php
/**
 * User Profile Management
 */

if (!defined('ABSPATH')) exit;

class CNMX_Users_Profile {
    
    public function __construct() {
        add_action('init', [$this, 'add_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_query_vars']);
    }
    
    public function add_rewrite_rules() {
        add_rewrite_rule('^editar-perfil/?$', 'index.php?cnmx_page=editar-perfil', 'top');
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'cnmx_page';
        return $vars;
    }
}
