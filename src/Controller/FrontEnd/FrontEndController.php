<?php

namespace TodoListApp\Controller\FrontEnd;

use TodoListApp\Bundle\RegisterInterface;
use TodoListApp\Controller\RestApiContoller;

/*
Class for FrontEnd
*/

class FrontEndController implements RegisterInterface
{
    private $plugin_name;
    private $version;
    private $path;

    public function __construct()
    {
    }

    public function register($plugin_name, $version, $path): void
    {
        if (!is_admin()) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;

            new RestApiContoller();
        }
    }
}
