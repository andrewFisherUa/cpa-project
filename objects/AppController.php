<?php

class AppController {

	private $parameters;

	public function __construct(){

		$this->_parseUri();
	}

	private function _parseUri(){

		$parse_uri = parse_url($_SERVER["REQUEST_URI"]);
		$uri = ltrim($parse_uri["path"], '/');
		$uri = filter_var($uri, FILTER_SANITIZE_STRING);
		$uri = strip_tags($uri);

		$parameters = [];

		if (!empty($uri)) {
			$parameters = explode('/', $uri);
		}

		if (empty($parameters[0])) {
			$parameters[0] = "index";
		}

		$this->parameters = $parameters;
	}

	public function notFound(){
		global $smarty;


		die("Страница не найдена");

		//$smarty->display('404.tpl');
	}

	public function actionIndex(){
		global $smarty;

		if (!empty($_REQUEST['ref'])) {
			$smarty->assign('ref', $_REQUEST['ref']);
		}

		$smarty->display('home.tpl');
	}

	public function actionUnlogin(){

		Audit::addRecord([
		    "group" => "logot",
		    "action" => "Выход из кабинета",
		]);

		session_destroy();

		// Убираем токен "запомнить меня"
		setcookie('tn', "", time() - 1, "/");

		header('Location: /login');
	}

	public function actionAdvertiser(){
		global $smarty;

		if (!empty($_REQUEST['ref'])) {
			$smarty->assign('ref', $_REQUEST['ref']);
		}

		$smarty->display('advertiser.tpl');
	}

	public function actionRecovery(){
	    $this->render('admin/recovery', ["key" => $this->parameters[1]]);
	}

	public function actionWebmaster(){
		global $smarty;

		if (!empty($_REQUEST['ref'])) {
			$smarty->assign('ref', $_REQUEST['ref']);
		}

		$smarty->display('webmaster.tpl');
	}

	public function actionRules(){
		global $smarty;

		$smarty->display('rules.tpl');
	}

	public function actionEmail_activation(){
		global $smarty;

		$activation = $this->parameters[1];

		require_once PATH_ROOT . "/components/email_activation.php";
	}

	public function actionRegistration(){
		global $smarty;

		$role = in_array($this->parameters[1], ["webmaster", "advertiser"]) ? $this->parameters[1] : "webmaster";

		if (!empty($_REQUEST['ref'])) {
			$smarty->assign('ref', $_REQUEST['ref']);
		}

		$smarty->assign("role", $role);
		$smarty->display('admin/registration.tpl');
	}

	public function actionAdmin(){
		global $smarty;

		$_REQUEST["r"] = !empty($this->parameters[1]) ? $this->parameters[1] : "home";
		$_REQUEST["k"] = !empty($this->parameters[2]) ? $this->parameters[2] : "";
		$_REQUEST["b"] = !empty($this->parameters[3]) ? $this->parameters[3] : "";
		$_REQUEST["c"] = !empty($this->parameters[4]) ? $this->parameters[4] : "";

  		if ( $this->isLoggedIn() ) {

		    // Проверяем может ли пользователь просматривать заправшиваемую страницу
		  	$filter = new Filter;

		    $email = $filter->sanitize($_SESSION["user"]["email"], ["string", "striptags"]);
		    $u = Privileged_User::getByEmail($email);

		    $perm = "view";

		    foreach (["r", "k", "b", "c"] as $k) {
		      if (!empty($_REQUEST[$k])) {
		        $perm .= "_" . $_REQUEST[$k];
		      }
		    }

		    if ($u->hasPrivilege($perm) == false){
		        $component = 'access_denied';

			    Audit::addRecord([
			    	"group" => "view_page",
			        "action" => "Попытка просмотра страницы " . $_SERVER["REQUEST_URI"] . ". Отказано в доступе",
			    ]);

		    } else {
		      	$component = $_REQUEST['r'];

		      	Audit::addRecord([
		        	"group" => "view_page",
		        	"action" => "Просмотр страницы " . $_SERVER["REQUEST_URI"],
		      	]);
		    }

		    $isAdmin = User::isAdmin();
		    $isPartner = User::isPartner();
		    $isSupport = User::isSupport();

		    $smarty->assign("admin", $isAdmin);

		    $template_variables = [
		    	"smarty" => $smarty,
		    	"components_path" => PATH . DS . "components" . DS . "admin",
		    	"component" => $component,
		    	"role" => $_SESSION['user']['role_name'],
		    	"admin" => $isAdmin,
		    	"isAdmin" => $isAdmin,
		    	"isBoss" => User::isBoss(),
		    	"isSupport" => $isSupport,
		    	"isPartner" => $isPartner,
		    	"user" => $_SESSION['user'],
		    	"news" => [
		    		"items" => $news,
		    		"count" => count($news)
		    	],
		    	"was_admin" => $_SESSION['was_admin'],
		    	"navigation" => Menu::get_nav_links()
		    ];

		    $balance = [];

		    if ($isAdmin) {
		    	$template_variables["balance"] = [
		    		"list" => Balance::getAll(0),
		    		"default" => new DefaultBalance(0)
		    	];

		    	$notifications = get_admin_notifications();
		    	$template_variables["notifications"] = [
		    		"items" => $notifications,
		    		"count" => count($notifications)
		    	];

		    	$template_variables["bad_orders_count"] = get_bad_orders_count();
		    }

		    if ($isPartner) {
		    	$template_variables["balance"] = [
		    		"list" => Balance::getAll(User::get_current_user_id()),
		    		"default" => new DefaultBalance(User::get_current_user_id())
		    	];
		    }

		    if ($isSupport) {
		      	$notifications = get_support_notifications();
		    	$template_variables["notifications"] = [
		    		"items" => $notifications,
		    		"count" => count($notifications)
		    	];
		    }

		    $uid = ($isAdmin || $isSupport) ? 0 : User::get_current_user_id();
		    $m = getUnreadTicketMessages($uid);

		    $template_variables["tickets"] = [
		    	"items" => $m,
		    	"count" => count($m)
		    ];

		    $this->render('admin/main', $template_variables);
		} else {
			header('Location: /login');
		}
	}

	public function render($template, $data){
		extract($data);

		$template_path = $_SERVER['DOCUMENT_ROOT'] . '/templates/';

		require_once $template_path . $template . ".php";
	}

	public function actionCheck(){
		global $smarty;

		if(!empty($_POST['alogin'])){

		  $filter = new Filter();

		  $login = $filter->sanitize($_POST['alogin'], ["string", "striptags"]);
		  $password = $filter->sanitize($_POST['apassword'], ["string", "striptags"]);

		  $userExists = User::check($login, $password);

		  if ($userExists && $userExists['status'] == User::STATUS_ACTIVE) {

		    $key = 'UMKEY1204' . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];
		    $_SESSION['umkey'] = md5($key . session_id());

		    $_SESSION['was_admin'] = FALSE;
		    $_SESSION['adlog'] = 1;
		    $_SESSION['user'] = $userExists;

		    $isAdmin = User::isAdmin();
		    $_SESSION['admin'] = $isAdmin;
		    $_SESSION['partner'] = !$isAdmin;

		    if ($_SESSION['user']['role_name'] == "webmaster" || $_SESSION['user']['role_name'] == "advertiser"){
		      $user_id = User::get_current_user_id();
		      $_SESSION['user'] = Partner::get_by_id($user_id);
		      $_SESSION['user']['hasShop'] = FALSE;
		      $_SESSION['partner'] = $_SESSION['user'];
		    }

		    // Сохранение записи о входе
		    Audit::addRecord([
		      "group" => "login",
		      "action" => "Вход в кабинет",
		    ]);

		    Visit::addRecord(
		      $GLOBALS['DB'],
		      [ "user_id" => User::get_current_user_id(),
		        "ip" => $_SERVER['REMOTE_ADDR'],
		        "user_agent" => $_SERVER['HTTP_USER_AGENT']]);

		    $remember = $filter->sanitize($_POST["remember"], "int!");
		    if ($remember == 1) {

		      // save remember me token
		      $token = crypt('UMKEY1204' . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $userExists["email"], blowfishSalt());
		      $expires = time() + 9999999;
		      $query = "INSERT INTO auth_tokens (token, user_id, expires) VALUES (:token, :user_id, :expires)";
		      $stmt = $GLOBALS["DB"]->prepare($query);
		      $stmt->bindParam(":token", $token, PDO::PARAM_STR);
		      $stmt->bindParam(":user_id", $userExists["user_id"], PDO::PARAM_INT);
		      $stmt->bindParam(":expires", $expires, $expires);
		      $stmt->execute();

		      setcookie('tn', $token, $expires, "/");
		    }

		    header("Location: /admin/home");

		  } else {

		    // Сохранение записи об ошибке входа
		    Audit::addRecord([
		      "group" => "login_fail",
		      "action" => "Ошибка при входе в кабинет",
		      "details" => [
		        "email" => $login
		      ]
		    ]);

		    header("Location: /login");
		  }
		} else {
			header("Location: /login");
		}
	}

	public function actionLogin(){
		global $smarty;

		if ($this->isLoggedIn()){
			header('Location: /admin/home');
		} else {
			$smarty->display( 'admin/login.tpl' );
		}
	}

	public function actionAjax(){
		global $smarty;

		require_once PATH_AJAX . DS . $this->parameters[1] . '.php';
	}

	public function getContent(){
		$method_name = "action" . ucfirst($this->parameters[0]);

		if (method_exists($this, $method_name)) {
			$this->$method_name();
		} else {
			$this->notFound();
		}
	}

	public function isLoggedIn(){

	  	if ($_SESSION['adlog'] == 1){

			// Проверка ключа сессии, если ключ не совпадает перенаправляем на авторизацию
			$key = 'UMKEY1204' . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];
			if ($_SESSION['umkey'] == md5($key . session_id())){
				return TRUE;
			}

	    } else {
	    	// Не авторизован

		    if (!empty($_COOKIE['tn'])) {
		    	$filter = new Filter;
				// Если есть токен "запомнить меня", проверяем пользователя
				$tn = $filter->sanitize($_COOKIE['tn'], ["string", "striptags"]);

				// Проверяем токен в базе
				$query = "SELECT u.*, r.*
				        FROM users as u INNER JOIN user_role as ur ON u.user_id = ur.user_id
				                        INNER JOIN roles as r ON ur.role_id = r.role_id
				                        INNER JOIN auth_tokens AS a ON u.user_id = a.user_id
				        WHERE a.token = ?";

				$stmt = $GLOBALS['DB']->prepare($query);
				$stmt->execute([
					$tn
				]);

		      	if ($stmt->rowCount() > 0) {
		        	$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

			        if ($tn == crypt('UMKEY1204' . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $user_data["email"], $tn)) {

						$key = 'UMKEY1204' . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];
						$_SESSION['umkey'] = md5($key . session_id());

						$_SESSION['was_admin'] = false;
						$_SESSION['adlog'] = 1;
						$_SESSION['user'] = $user_data;

						$isAdmin = User::isAdmin();
						$_SESSION['admin'] = $isAdmin;
						$_SESSION['partner'] = !$isAdmin;

						if ($_SESSION['user']['role_name'] == "webmaster" || $_SESSION['user']['role_name'] == "advertiser"){
							$user_id = User::get_current_user_id();
							$_SESSION['user'] = Partners::get_by_id($user_id);
							$_SESSION['user']['hasShop'] = Partners::hasShop($user_id);
							$_SESSION['partner'] = $_SESSION['user'];
						}

						return TRUE;
			        }
		      	}
		    }
		}

	  	return FALSE;
	}
}

?>
