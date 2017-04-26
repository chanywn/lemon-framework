<?php

class import
{
    public static function module($moduleName)
    {
        require_once __DIR__ . '/razor/' . $moduleName . '.php';
    }
}