<?php


namespace TodoListApp\Bundle;


interface RegisterInterface
{
    public function register($plugin_name, $version, $path): void;
}
