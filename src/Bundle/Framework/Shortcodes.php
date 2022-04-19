<?php

namespace TodoListApp\Bundle\Framework;

use TodoListApp\Bundle\RegisterInterface;

/*
Class to register Shortcodes
*/


class Shortcodes implements RegisterInterface
{
    private $plugin_name;
    private $version;
    private $path;

    public function __construct()
    {
    }

    public function register($plugin_name, $version, $path): void
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->path = $path;

        add_shortcode('todoList', [$this, 'todoList']);
    }

    /**
     * TodoList shortcode
     * @param Array $links
     * 
     * @return String
     */
    function todoList()
    {
        if(!is_user_logged_in()) return __('The list is available only for logged-in users.', 'todolist_ejt');
        
        ob_start();
        include $this->path . '/views/get_list-todolist_ejt-display.php';
        $file_content = ob_get_contents();
        ob_end_clean();

        return $this->strTag($file_content);
    }

    /**
     * Hack for ob_end_clean for vue parse HTML
     * 
     * @param String $xmlstr
     * 
     * @return String
     */
    function strTag($xmlstr)
    {
        $str = str_replace('U+0022', '"', $xmlstr);
        $str = str_replace('U+0027', "'", $str);
        $str = str_replace('U+003C', '<', $str);
        $str = str_replace('&#8243;', '"', $str);

        return $str;
    }
}
