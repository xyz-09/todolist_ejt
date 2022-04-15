<?php

namespace TodoListApp\Bundle\Framework;

use TodoListApp\Bundle\RegisterInterface;

/*
Class to register js scripts
*/

class EnqueueScripts implements RegisterInterface
{
  private $plugin_name;
  private $version;
  private $adminURL;
  private $js_object_name = "todosSettings";
  private $js_variables;

  public function __construct()
  {
  }

  public function register($plugin_name, $version, $path): void
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->path = $path;
    $this->restEndPoint = '/wp-json/' . $this->plugin_name . '/v2';

    $this->adminURL = WP_PLUGIN_URL . '/' .  $this->plugin_name . '/assets/js';

    if (!is_user_logged_in()) return;

    $this->js_variables = [
      'nonce' => wp_create_nonce('wp_rest'),
      'mainEndpoint' => $this->restEndPoint
    ];

    add_action('admin_enqueue_scripts', [$this, 'setAdminScripts']);
    add_action('wp_enqueue_scripts', [$this, 'setScripts']);
  }

  function addMainScripts()
  {
    wp_enqueue_script('vue', 'https://unpkg.com/vue@3.2.33/dist/vue.global.prod.js', array('jquery'), '3.2.33', true);
   
    wp_enqueue_script($this->plugin_name, $this->adminURL . '/todolist_ejt-admin.js?ver=' . $this->version, array('vue'), $this->version, true);
  }

  function setAdminScripts()
  {
    $screen = get_current_screen();

    if (!in_array($screen->id, array('toplevel_page_' . $this->plugin_name))) return;

    $this->addMainScripts();

    wp_localize_script(
      $this->plugin_name,
      $this->js_object_name,
      array_merge(
        $this->js_variables,
        ['currentUser' => get_current_user_id()]
      )
    );
  }

  function setScripts()
  {

    $this->addMainScripts();

    wp_localize_script(
      $this->plugin_name,
      $this->js_object_name,
      $this->js_variables
    );
  }
}
