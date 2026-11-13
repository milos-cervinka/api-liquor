<?php
require_once __DIR__ . '/config.php';
require_once ROOT_DIR . '/src/Dashboard/ModuleBuilder.php';

$builder = new ModuleBuilder(ACTIVE_MODULES);

try 
{
    $builder->sync();
} 
catch (\Throwable $th) 
{
    echo $th;
}