<?

namespace Figeor;

require_once 'init.php';

$parsedUrl = @(Controller\AbstractController::parseURL());

$controllerName = strlen($parsedUrl['controller']) ? $parsedUrl['controller'] : 'index';
$controllerClass = __NAMESPACE__ . '\Controller\\' . ucfirst($controllerName) . 'Controller';
$controller = new $controllerClass;
$action = $parsedUrl['action'];

$response = $controller->doAction($action);

Core\System::checkLogin();
if (empty($_SESSION['userId'])) {
    $view = new Core\View('index/login.php');
    echo $view->renderToString();
    die;
}

$layout = new \Figeor\Core\View('index.php');
$layout->title = $response['title'];
$layout->mainContent = $response['main'];

$content = $layout->renderToString();

ob_end_clean();
echo $content;

exit;