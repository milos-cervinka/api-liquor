<?php
class DirectoryChecker 
{
    private array $directories;

    public function __construct(array $directories) 
    {
        $this->directories = $directories;
    }

    public function verifyAndCreate(): void 
    {
        foreach ($this->directories as $dir) 
        {
            if (!is_dir($dir)) 
            {
                mkdir($dir, 0777, true);
                chmod($dir, 0777);
            }
        }
    }
}