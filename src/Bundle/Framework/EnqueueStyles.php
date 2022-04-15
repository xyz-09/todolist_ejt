<?php


namespace TodoListApp\Bundle\Framework;

use TodoListApp\Bundle\RegisterInterface;

/*
Class to register css scripts
*/


class EnqueueStyles implements RegisterInterface
{
  private $plugin_name;
  private $version;
  private $adminURL;

  public function __construct()
  {
  }

  public function register($plugin_name, $version, $path): void
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->path = $path;
    $this->adminURL = WP_PLUGIN_URL . '/' .  $this->plugin_name . '/assets/css';

    if(!is_user_logged_in()) return;
    
    add_action('admin_enqueue_scripts', [$this, 'setAdminStyles']);
    add_action('wp_enqueue_scripts', [$this, 'setStyles']);
  }


  function setAdminStyles()
  {
    $screen = get_current_screen();
    if (!in_array($screen->id, array('toplevel_page_' . $this->plugin_name))) return;
    
    $this->setStyles();
  }


  function setStyles()
  {
    wp_enqueue_style($this->plugin_name, $this->adminURL . '/todolist_ejt-admin.css?ver=' . $this->version, array(), $this->version, 'all');
  }
}
