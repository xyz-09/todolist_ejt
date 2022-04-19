<?php

namespace TodoListApp\Controller\Admin;

use TodoListApp\Bundle\RegisterInterface;
use TodoListApp\Controller\RestApiContoller;

/*
Class to register wordpress Admin pages
*/

class AdminController implements RegisterInterface
{
    private $plugin_name;
    private $page_name = "EJT Tasks List";
    private $version;
    private $path;
    private $name;
    private $apiv;
    private $controller;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu_pages']);

      //  remove_action('template_redirect', 'rest_output_link_header', 11);
      //  remove_action('wp_head', 'rest_output_link_wp_head', 10);
      //  remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    }

    public function register($plugin_name, $version, $path): void
    {
        $this->plugin_name = $plugin_name;
        $this->page_name = __('EJT Tasks List', 'todolist_ejt');;
        $this->version = $version;
        $this->path = $path;

        $this->name = ucfirst(__($this->plugin_name));
        $this->controller = new RestApiContoller();
    }

    /**
     * Add menu Page
     * 
     * @since 1.0.0
     */

    public function add_menu_pages()
    {

        $this->add_main_page($this->page_name);
    }

    private function add_main_page($name)
    {
        $view_hook_list = add_menu_page(
            __($name, $this->plugin_name), //Page title
            __($name, $this->plugin_name), //Menu title
            'manage_options', //capability
            $this->plugin_name, //menu_slug
            array($this, 'render_template'), //function
            'dashicons-editor-ol', //icon url
            6
        );

        $this->views[$view_hook_list] = 'get_list';
    }

    /**
     * Renders the given template if it's readable.
     *
     * @param string $template
     * 
     * @return
     */
    public function render_template()
    {
        $view = current_filter();
        $current_view = $this->views[$view];
        $template_path = $this->path . '/views/' . $current_view . '-todolist_ejt-admin-display.php';

        if (!is_readable($template_path)) {
            return;
        }

        include $template_path;
        return;
    }
}
