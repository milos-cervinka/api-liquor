<?php
class MenuManager 
{
    private array $activeModules;

    public function __construct(array $activeModules) 
    {
        $this->activeModules = $activeModules;
    }

    public function getMenu(): array 
    {
        $menu = [];
        foreach ($this->activeModules as $mod) 
        {
            $menu[] = ucfirst(strtolower($mod));
        }
        return $menu;
    }
}