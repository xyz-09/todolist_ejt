<?php

use TodoListApp\Initialiaze;

if (!defined('WPINC')) {
	die;
}
/**
 * @link              #
 * @since             1.0.0
 * @package           todolist_ejt
 *
 * @wordpress-plugin
 * Plugin Name:       EJT TodoList 
 * Description:       EJT TodoList - use [todoList] to display tasksTable on front
 * Version:           1.0.1
 * Author:            ejapp
 * Text Domain:       todolist_ejt
 * Domain Path: 	  /languages
 */

require_once  plugin_dir_path(__FILE__)  . 'vendor/autoload.php';

add_action('plugins_loaded', function () {

	$pluginName = plugin_basename(dirname(__FILE__));
	$pluginVersion = '1.0.0';

	Initialiaze::register($pluginName, $pluginVersion);
	
	load_plugin_textdomain( 'todolist_ejt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
});
