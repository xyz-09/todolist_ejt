<?php


namespace TodoListApp;

use TodoListApp\Bundle\Framework\EnqueueScripts;
use TodoListApp\Bundle\Framework\EnqueueStyles;
use TodoListApp\Controller\Admin\AdminController;
use TodoListApp\Controller\FrontEnd\FrontEndController;
use TodoListApp\Bundle\Framework\Shortcodes;

final class Initialiaze
{
    /**
     * Plugin name is dynamic/ depends on folder name in pluginDir and is added in register method
     */
    public function __construct()
    {
    }

    private static function init(): array
    {
        return [
            EnqueueScripts::class,
            EnqueueStyles::class,
            AdminController::class,
            FrontEndController::class,
            Shortcodes::class
        ];
    }

    public static function register($pluginName, $pluginVersion): void
    {
        foreach (self::init() as $class) {
            $service = new $class();
            if (method_exists($service, 'register')) {
                $service->register($pluginName, $pluginVersion, plugin_dir_path(__FILE__) . '../');
            }
        }
    }
}
