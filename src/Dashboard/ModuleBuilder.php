<?php
class ModuleBuilder 
{
    private array $activeModules;
    private string $templateDir;

    public function __construct(array $activeModules) 
    {
        $this->activeModules = $activeModules;
        $this->templateDir = ROOT_DIR . '/src/Dashboard/Templates';
    }

    public function sync(): void 
    {
        $coreDirs = [
            ROOT_DIR . '/dashboard/Model',
            ROOT_DIR . '/dashboard/Controller',
            ROOT_DIR . '/dashboard/View/Sites'
        ];
        //https://stackoverflow.com/questions/136899/suppress-error-with-operator-in-php
        foreach ($coreDirs as $dir) 
        {
            if (!is_dir($dir)) 
            {
                @mkdir($dir, 0777, true);
            }
        }

        foreach ($this->activeModules as $module) 
        {
            if (strtolower($module) === 'dashboard') 
            {
                continue;
            }
            $this->buildModule(strtolower($module));
        }
    }

    private function buildModule(string $module): void 
    {
        $modUc = ucfirst($module);
        $viewDir = ROOT_DIR . "/dashboard/View/Sites/{$module}";

        $paths = [
            'model'      => ROOT_DIR . "/dashboard/Model/{$module}Model.php",
            'controller' => ROOT_DIR . "/dashboard/Controller/{$module}Controller.php",
            'viewDir'    => $viewDir,
            'viewHtml'   => $viewDir . "/{$module}.html",
            'viewCss'    => $viewDir . "/{$module}.css",
            'viewJs'     => $viewDir . "/{$module}.js"
        ];

        if (!is_dir($paths['viewDir'])) 
        {
            @mkdir($paths['viewDir'], 0777, true);
        }
        // AI => Tohle tvoreni souboru jsem nedokazal absolutne seradit spravne
        $modelTpl = file_exists($this->templateDir . '/model.php.stub') 
            ? file_get_contents($this->templateDir . '/model.php.stub') 
            : "<?php\nrequire_once __DIR__ . '/../../config.php';\n\nclass {{MODULE_UC}}Model \n{\n    public function getData(): array \n    {\n        return ['message' => 'Data z modulu {{MODULE_UC}} úspěšně načtena.'];\n    }\n}\n";

        $controllerTpl = file_exists($this->templateDir . '/controller.php.stub') 
            ? file_get_contents($this->templateDir . '/controller.php.stub') 
            : "<?php\nheader('Content-Type: application/json');\nrequire_once __DIR__ . '/../Model/{{MODULE_LC}}Model.php';\n\n\$model = new {{MODULE_UC}}Model();\necho json_encode(\$model->getData());\n";

        $viewHtmlTpl = file_exists($this->templateDir . '/view.html.stub') 
            ? file_get_contents($this->templateDir . '/view.html.stub') 
            : "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n<title>{{MODULE_UC}}</title>\n<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n<link href=\"../../CSS/dashboard.css\" rel=\"stylesheet\">\n<link href=\"{{MODULE_LC}}.css\" rel=\"stylesheet\">\n<script src=\"../../Components/loader.js\" defer></script>\n<script src=\"../../Components/componentConf.js\" defer></script>\n<script src=\"{{MODULE_LC}}.js\" defer></script>\n</head>\n<body>\n<main-header></main-header>\n<div class=\"container-fluid\">\n<div class=\"row\">\n<main-sidebar></main-sidebar>\n<main class=\"col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4\">\n<h1>{{MODULE_UC}}</h1>\n<div class=\"card p-3 mt-3\">\n<p id=\"output\">Načítání dat...</p>\n</div>\n</main>\n</div>\n</div>\n<script src=\"https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js\"></script>\n</body>\n</html>";

        $viewCssTpl = file_exists($this->templateDir . '/view.css.stub') 
            ? file_get_contents($this->templateDir . '/view.css.stub') 
            : "h1 {\n    color: #333;\n}\n";

        $viewJsTpl = file_exists($this->templateDir . '/view.js.stub') 
            ? file_get_contents($this->templateDir . '/view.js.stub') 
            : "document.addEventListener('DOMContentLoaded', async () => {\n    try {\n        const res = await fetch('/Web/API/dashboard/Controller/{{MODULE_LC}}Controller.php');\n        const data = await res.json();\n        const output = document.getElementById('output');\n        if (output) output.innerText = data.message;\n    } catch (e) {\n        console.error('Chyba API', e);\n    }\n});\n";

        if (!file_exists($paths['model'])) file_put_contents($paths['model'], str_replace('{{MODULE_UC}}', $modUc, $modelTpl));
        if (!file_exists($paths['controller'])) file_put_contents($paths['controller'], str_replace(['{{MODULE_UC}}', '{{MODULE_LC}}'], [$modUc, $module], $controllerTpl));
        if (!file_exists($paths['viewHtml'])) file_put_contents($paths['viewHtml'], str_replace(['{{MODULE_UC}}', '{{MODULE_LC}}'], [$modUc, $module], $viewHtmlTpl));
        if (!file_exists($paths['viewCss'])) file_put_contents($paths['viewCss'], str_replace('{{MODULE_UC}}', $modUc, $viewCssTpl));
        if (!file_exists($paths['viewJs'])) file_put_contents($paths['viewJs'], str_replace(['{{MODULE_UC}}', '{{MODULE_LC}}'], [$modUc, $module], $viewJsTpl));
    }
}