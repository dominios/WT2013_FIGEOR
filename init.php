<?

namespace Figeor;

/* init */
ob_start();
header('Content-Type: text/html; charset=utf-8');
session_start();
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
spl_autoload_register('\Figeor\class_autoloader', true);
\Figeor\Core\System::getInstance();

function class_autoloader($class_name) {

    if (class_exists($class_name)) {
        return;
    }

    /* tieto zlozky nacita cele */
    $autoloadDirectories = array();
    $autoloadDirectories[] = 'core';
    $autoloadDirectories[] = 'models';

    foreach ($autoloadDirectories as $dir) {
        $dh = \opendir($dir);
        if ($dh) {
            while (false !== ($file = readdir($dh))) {
                if (!is_dir($dir . '/' . $file)) {
                    include_once $dir . '/' . $file;
                    ;
                }
            }
            closedir($dh);
        }
    }

    /* rucne jednotlive fily */
    include_once 'controllers/AbstractController.php';

    // opat test, ak uz nacitalo z tych zloziek
    if (class_exists($class_name)) {
        return;
    }

    $class = str_replace(__NAMESPACE__ . '\\', '', $class_name);

    if (preg_match('/^(.)*Controller$/', $class)) {
        $class = str_replace('Controller\\', '', $class);
        $file = 'controllers/' . $class . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
    return;
}
