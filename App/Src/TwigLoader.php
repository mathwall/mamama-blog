<?php

namespace App\Src;

use Exception;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigLoader {
    static private $twig;

    static public function getTwig() {
        if(self::$twig == null) {
            self::$twig = new Twig_Environment(new Twig_Loader_Filesystem(__ROOT_DIR__ . "/App/Views/"));
        }
        return self::$twig;
    }

    static public function render($template, $context) {
        try {
            echo self::getTwig()->render($template, $context);
        } catch (Exception $exception) {
            die("Page Non trouvee : " . $exception->getMessage());
        }
    }
}
