<?php
/**
* Класс Flow
*
* Класс Flow предназначен для создания, редактирования и сохранения потока.
* При сохранении потока каждый раз генерируется новый контент при помощи класса Content_Generator
*
* @author Sorochan Elena
* @version 1.0
*/

class Flow {

	/**
	*
	* @var integer $id            ID потока
	* @var string $name          Название потока
	* @var integer $user_id       ID пользователя, который создал поток
	* @var integer $offer_id      ID оффера, для которого создан поток
	* @var integer $landing_id    ID шаблона лендинга, который был использован для создания контента
	* @var integer $blog_id       ID шаблона блога, который был использован для создания контента
	* @var integer $subaccount_id ID субаккаунта
	* @var string $subid1
	* @var string $subid2
	* @var string $subid3
	* @var string $subid4
	* @var string $subid5
	* @var integer $yandex_id     ID yandex метрики
	* @var integer $google_id     ID google метрики
	* @var integer $created       Дата создания потока
	* @var integer $modified      Дата редактирования потока
	* @var string $link          Ссылка на поток
	* @var string $full_link     Полная ссылка на поток STREAMS_URL + Flow::$link
	* @var integer $comebacker    Комбекер
	**/


	private $id;
	private $key;
	private $name;
	private $user_id;
	private $offer_id;
	private $landing_id;
	private $blog_id;
	private $subaccount_id;
	private $subid1;
	private $subid2;
	private $subid3;
	private $subid4;
	private $subid5;
	private $yandex_id;
	private $google_id;
	private $mail_id;
	private $created;
	private $modified;
	private $link;
	private $landing_link;
	private $full_link;
	private $comebacker;
	private $prices;
	private $pfinder_script;
	private $pfinder_id;
	private $landing_alias;
	private $blog_alias;
	private $redirect_traffic;
	private $trafficback;
	private $source;
	private $postback;
	private $need_update = false;
	private $disabled = 0;

	public function __construct($data) {
		$this->id = (isset($data["id"])) ? $data['id'] : 0;
		$this->name = (isset($data["name"])) ? $data['name']: '';
		$this->user_id = (isset($data["user_id"])) ? $data['user_id'] : 0;
		$this->offer_id = (isset($data["offer_id"])) ? $data['offer_id'] : 0;
		$this->landing_id = (isset($data["landing_id"])) ? $data['landing_id'] : 0;
		$this->blog_id = (isset($data["blog_id"])) ? $data['blog_id'] : 0;
		$this->comebacker = (isset($data["comebacker"]) && $this->blog_id ) ? $data['comebacker'] : 0;
		$this->subaccount_id = (isset($data["subaccount_id"])) ? $data['subaccount_id'] : 0;
		$this->subid1 = (isset($data["subid1"])) ? $data['subid1']: '';
		$this->subid2 = (isset($data["subid2"])) ? $data['subid2']: '';
		$this->subid3 = (isset($data["subid3"])) ? $data['subid3']: '';
		$this->subid4 = (isset($data["subid4"])) ? $data['subid4']: '';
		$this->subid5 = (isset($data["subid5"])) ? $data['subid5']: '';
		$this->yandex_id = (isset($data["yandex_id"])) ? $data['yandex_id']: '';
		$this->mail_id = (isset($data["mail_id"])) ? $data['mail_id']: '';
		$this->google_id = (isset($data["google_id"])) ? $data['google_id']: '';
		$this->created = (isset($data["created"])) ? $data['created'] : time();
		$this->modified = (isset($data["modified"]) ) ? $data['modified'] : time();
		$this->link = (isset($data["link"]) ) ? $data['link'] : 0;
		$this->pfinder_script = (isset($data["pfinder_script"])) ? $data['pfinder_script'] : "";
		$this->pfinder_id = (isset($data["pfinder_id"])) ? $data['pfinder_id'] : 0;
		$this->blog_alias = (isset($data["blog_alias"])) ? $data['blog_alias'] : "";
		$this->landing_alias = (isset($data["landing_alias"])) ? $data['landing_alias'] : "";
		$this->key = (isset($data["key"])) ? $data['key'] : "";
		$this->redirect_traffic = (isset($data["redirect_traffic"])) ? $data['redirect_traffic'] : "";
		$this->trafficback = (isset($data["trafficback"])) ? $data['trafficback'] : "";
		$this->space = (isset($data["space"])) ? $data['space'] : 0;
		$this->use_global_postback = false;
		$this->postback = $this->fetchPostback();
		$this->disabled = ($this->offer_id > 0) ? $this->fetchDisabled() : false;
	}

	private function fetchDisabled(){

		$query = "SELECT t1.id, t1.type, t1.offer_status as status, t2.user_id
				  FROM goods as t1 LEFT JOIN offer_webmaster AS t2 ON t1.id = t2.offer_id
				  WHERE t1.id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->offer_id
		]);

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($data[0]['status'] != Offer::STATUS_ACTIVE) {
			return true;
		}

		if ($data[0]['type'] == Offer::TYPE_PRIVATE) {
			foreach ($data as $a) {
				if ($a['user_id'] == $this->user_id) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	private function fetchPostback(){
		// Проверить есть ли постбек для этого потока
		$p = Postback::getInstance($this->user_id, $this->id);
		$url = $p->getUrl();
		if (!empty($url)) {
			$this->use_global_postback = false;
			return $this->postback = $p;
		} else {
			$p1 = Postback::getInstance($this->user_id);
			$url = $p1->getUrl();
			if (!empty($url)) {
				$this->use_global_postback = true;
			}
			return $this->postback = $p1;
		}
	}

	public function hasMetrics() {
		return !empty($this->yandex_id) || !empty($this->google_id) || !empty($this->mail_id);
	}

	public function setPostback($data = []) {
		$this->use_global_postback = isset($data['use_global_postback']) ? $data['use_global_postback'] : true;

		if ($this->use_global_postback) {
			Postback::remove($this->user_id, $this->id);
			$this->postback = Postback::getInstance($this->user_id);
		} else {
			$this->postback = $data['url'];
			$data['stream_id'] = $this->id;
			$this->postback = Postback::create($data);
		}
	}


	public function getPostback(){
		return $this->postback;
	}

	public function useGlobalPostback(){
		return $this->use_global_postback;
	}

	public function setName($param){
		$this->name = $param;
	}

	public function setSpace($param){
		$this->space = $param;
	}

	public function setUserId($param){
		$this->user_id = $param;
	}

	public function setOfferId($param){
		if ($this->id == 0) {
			$this->offer_id = $param;
		}
	}

	public function setLandingId($param){
		$this->landing_id = $param;
	}

	public function setBlogId($param){
		$this->blog_id = $param;
	}

	public function setSubaccountId($param){
		$this->subaccount_id = $param;
	}

	public function setSubid($key = 1, $value) {
		$var = "subid{$key}";
		$this->$var = $value;
	}

	public function setSubid1($param){
		$this->subid1 = $param;
	}

	public function setSubid2($param){
		$this->subid2 = $param;
	}

	public function setSubid3($param){
		$this->subid3 = $param;
	}

	public function setSubid4($param){
		$this->subid4 = $param;
	}

	public function setSubid5($param){
		$this->subid5 = $param;
	}

	public function setYandexId($param){
		$this->yandex_id = $param;
	}

	public function setGoogleId($param){
		$this->google_id = $param;
	}

	public function setMailId($param){
		$this->mail_id = $param;
	}

	public function setLink($param){
		$this->link = $param;
	}

	public function setComebacker($param){
		$this->comebacker = $param;
	}

	public function setRedirectTraffic($param){
		if (empty($param)) {
			//$this->redirect_traffic = SITE_URL . "?ref={$this->user_id}";
			$this->redirect_traffic = 'http://google.com';
		} else {
			$this->redirect_traffic = $param;
		}
	}

	public function setTrafficback($param){
		$this->trafficback = $param;
	}

	public function getPrices(){
		$this->fetchPrices();
		return $this->prices;
	}

	private function fetchPrices(){
		if (empty($this->prices)) {
			$this->prices = new Flow_Prices($this->id, $this->offer_id);
		}
	}

	/**
	* Возвращает полную ссылку на поток
	*
	* @return void
	**/
	public function getFullLink(){
		$params = [];

		for ($i=1; $i<6; $i++) {
			$var = "subid{$i}";
			if (!empty($this->$var)) {
				$params[] = $var . "=" . $this->$var;
			}
		}

		if (!empty($params)) {
			return STREAMS_URL . "/" . $this->link . "?" . implode("&", $params);
		}

		return $this->full_link = STREAMS_URL . "/" . $this->link;
	}

	public function getLandingFolderName(){
		if (!empty($this->landing_alias)) {
			return $this->landing_alias;
		}
		return base_convert($this->id, 10, 16) . base_convert($this->landing_id, 10, 16);
	}

	public function getBlogFolderName(){
		if (!empty($this->blog_alias)) {
			return $this->blog_alias;
		}
		return base_convert($this->id, 10, 16) . base_convert($this->blog_id, 10, 16);
	}

	public function getStreamUrl(){
		return STREAMS_URL . "/";
	}

	public function getName(){
		return $this->name;
	}

	public function getSpace(){
		return $this->space;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getId(){
		return $this->id;
	}

	public function getLandingId(){
		return $this->landing_id;
	}

	public function getLandingAlias(){
		return $this->landing_alias;
	}

	public function getBlogAlias(){
		return $this->blog_alias;
	}

	public function getBlogId(){
		return $this->blog_id;
	}

	public function getYandexId(){
		return $this->yandex_id;
	}

	public function getGoogleId(){
		return $this->google_id;
	}

	public function getMailId(){
		return $this->mail_id;
	}

	public function getLink(){
		return $this->link;
	}

	public function hasComebacker() {
		return $this->comebacker == 1;
	}

	public function getOfferId(){
		return $this->offer_id;
	}

	public function getSubaccountId(){
		return $this->subaccount_id;
	}

	public function getSubId($num){
		$var = "subid{$num}";
		return $this->$var;
	}

	public function getModified($format = false){
		if ($format) {
			return  date("Y-m-d H:i:s", $this->modified);
		}
		return $this->modified;
	}

	public function getKey(){
		return $this->key;
	}

	public function getPfinderScript(){
		if ($this->id != 0 && $this->pfinder_script == "") {
			$query = "SELECT pfinder_script FROM flows WHERE f_id = ?";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute(array($this->id));
			$this->pfinder_script = $stmt->fetchColumn();
		}
		return $this->pfinder_script;
	}

	public function hasRedirectTraffic(){
		return $this->redirect_traffic != "";
	}

	public function getRedirectTrafficLink(){
		return $this->redirect_traffic;
	}

	public function hasTrafficback(){
		return $this->trafficback != "";
	}

	public function getTrafficback(){
		return $this->trafficback;
	}

	public function isDisabled(){
		$this->fetchDisabled();
		return $this->disabled;
	}

    /**
    * Сохранение потока
    *
    * Сохраняет поток в базу данных и вызывает функцию Flow::save_link() для формирования ссылки потока
    *
    * @return void
    **/
	public function save() {
		if ($this->isDisabled()) {
			return false;
		}

		$this->need_update = $this->needUpdate();

		if ( $this->id == 0 ) {
			$query = "INSERT INTO flows ( user_id, name, offer_id, landing_id, blog_id, subaccount_id, subid1, subid2, subid3, subid4,	subid5,	yandex_id,	google_id,	created, modified, comebacker, landing_alias, blog_alias, redirect_traffic, trafficback, space, mail_id)
			          VALUES ( :user_id, :name, :offer_id, :landing_id, :blog_id, :subaccount_id, :subid1, :subid2, :subid3, :subid4,	:subid5, :yandex_id,	:google_id,	:created, :modified, :comebacker, :landing_alias, :blog_alias, :redirect_traffic, :trafficback, :space, :mail_id)";
		} else {
			$query = "UPDATE flows SET  user_id = :user_id,
										name = :name,
										offer_id = :offer_id,
										landing_id = :landing_id,
										blog_id = :blog_id,
										subaccount_id = :subaccount_id,
										subid1 = :subid1,
										subid2 = :subid2,
										subid3 = :subid3,
										subid4 = :subid4,
										subid5 = :subid5,
										yandex_id = :yandex_id,
										google_id = :google_id,
										mail_id = :mail_id,
										modified = :modified,
										comebacker = :comebacker,
										landing_alias = :landing_alias,
										blog_alias = :blog_alias,
										redirect_traffic = :redirect_traffic,
										trafficback = :trafficback,
										space = :space
					   					WHERE f_id = :f_id";
		}

		$stmt = $GLOBALS["DB"]->prepare( $query );
		if ( $this->id != 0 ) {
			$stmt->bindParam( ':f_id', $this->id, PDO::PARAM_INT );
		} else {
			$stmt->bindParam( ':created', $this->created, PDO::PARAM_INT );
		}
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(':offer_id', $this->offer_id, PDO::PARAM_INT);
		$stmt->bindParam(':landing_id', $this->landing_id, PDO::PARAM_INT);
		$stmt->bindParam(':blog_id', $this->blog_id, PDO::PARAM_INT);
		$stmt->bindParam(':subaccount_id', $this->subaccount_id, PDO::PARAM_INT);
		$stmt->bindParam(':subid1', $this->subid1, PDO::PARAM_STR);
		$stmt->bindParam(':subid2', $this->subid2, PDO::PARAM_STR);
		$stmt->bindParam(':subid3', $this->subid3, PDO::PARAM_STR);
		$stmt->bindParam(':subid4', $this->subid4, PDO::PARAM_STR);
		$stmt->bindParam(':subid5', $this->subid5, PDO::PARAM_STR);
		$stmt->bindParam(':yandex_id', $this->yandex_id, PDO::PARAM_STR);
		$stmt->bindParam(':mail_id', $this->mail_id, PDO::PARAM_STR);
		$stmt->bindParam(':google_id', $this->google_id, PDO::PARAM_STR);
		$stmt->bindParam(':modified', $this->modified, PDO::PARAM_INT);
		$stmt->bindParam(':comebacker', $this->comebacker, PDO::PARAM_INT);
		$stmt->bindParam(':landing_alias', $this->landing_alias, PDO::PARAM_STR);
		$stmt->bindParam(':blog_alias', $this->blog_alias, PDO::PARAM_STR);
		$stmt->bindParam(':redirect_traffic', $this->redirect_traffic, PDO::PARAM_STR);
		$stmt->bindParam(':trafficback', $this->trafficback, PDO::PARAM_STR);
		$stmt->bindParam(':space', $this->space, PDO::PARAM_INT);
		$stmt->execute();
		if ( $this->id == 0 ) {
			$this->id = $GLOBALS["DB"]->lastInsertID();
			$this->saveKey();
		}
	}

	private function needUpdate(){
		if ($this->id == 0) {
			return true;
		}

		$query = "SELECT landing_id, blog_id, subid1, subid2, subid3, subid4, subid5, space FROM flows WHERE f_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([$this->id]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		$changed = ($data['landing_id'] != $this->landing_id ||
					$data['blog_id'] != $this->blog_id ||
					$data['space'] != $this->space);

		return $changed;
	}

	private function createPfinderKey(){
		$response = take_script(array(
			"site_id" => $this->id,
			"user_id" => $this->user_id,
			"url" => STREAMS_URL . "/" . $this->link,
			"goal_url" => STREAMS_URL . "/" . $this->link . "/complete.html"
		));

		if ($response == "error") {
			return false;
		}

		$response = json_decode($response);
		$this->pfinder_script = "http://octtraces.com/script.php?id=" . $response->id;
		$this->pfinder_id = $response->id;
		$this->savePfinderScript();

		// Записывает новый pfinder ключ
		$query = "INSERT INTO pfinder_keys(pfinder_id, stream_id, landing_id, blog_id, subid1, subid2, subid3, subid4, subid5, traffic_source_id, offer_id, user_id, created)
				  VALUES (:id, :stream_id, :l_id, :b_id, :s1, :s2, :s3, :s4, :s5, :ts_id, :offer_id, :user_id, " . time() . ")";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $response->id, PDO::PARAM_INT);
		$stmt->bindParam(":stream_id", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":l_id", $this->landing_id, PDO::PARAM_INT);
		$stmt->bindParam(":b_id", $this->blog_id, PDO::PARAM_INT);
		$stmt->bindParam(":s1", $this->subid1, PDO::PARAM_STR);
		$stmt->bindParam(":s2", $this->subid2, PDO::PARAM_STR);
		$stmt->bindParam(":s3", $this->subid3, PDO::PARAM_STR);
		$stmt->bindParam(":s4", $this->subid4, PDO::PARAM_STR);
		$stmt->bindParam(":s5", $this->subid5, PDO::PARAM_STR);
		$stmt->bindParam(":ts_id", $this->space, PDO::PARAM_INT);
		$stmt->bindParam(":offer_id", $this->offer_id, PDO::PARAM_INT);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->execute();
	}

	private function savePfinderScript(){
		$query = "UPDATE flows SET pfinder_script = :script, pfinder_id = :pid WHERE f_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":script", $this->pfinder_script, PDO::PARAM_STR);
		$stmt->bindParam(":pid", $this->pfinder_id, PDO::PARAM_INT);
        $r = $stmt->execute();

	}

	private function saveKey(){
		$this->key = md5($this->id);
		$query = "UPDATE flows SET `key` = :key WHERE f_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":key", $this->key, PDO::PARAM_STR);
        $stmt->execute();
	}

	public function savePrices(){
		if ($this->isDisabled()) {
			return false;
		}

		$this->generateLink();

		if ($this->need_update) {
			$this->createPfinderKey();
		}

		$this->generateContent();

		if ($this->id == 0) {
			return false;
		}

		$this->getPrices()->save();
	}

	/**
	* Генерирует ссылку на поток
	*
	* Создает обьекты типа Landing_Generator для создания лендинга и Blog_Generator для создания блога
	* Возвращает ссылку на созданный контент. Ссылка будет указывать на блог, если он был выбран, или на лендинг если блог не выбран.
	*
	* @return string
	**/
	private function generateContent() {
		$stmt = $GLOBALS['DB']->prepare("UPDATE flows SET link = :l WHERE f_id = :i");
		$stmt->bindParam(":l", $this->link, PDO::PARAM_INT);
		$stmt->bindParam(":i", $this->id, PDO::PARAM_INT);
		$stmt->execute();

		$query = "SELECT offer_status FROM goods WHERE id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute(array($this->offer_id));
		if ($stmt->fetchColumn() == "disabled") {
			return false;
		}

		$landing = new Landing_Generator($this, $this->landing_alias);
		$url = $landing->generate();
		if ($this->blog_id) {
			$blog = new Blog_Generator($this, $url, $this->blog_alias);
			$blog->generate();
		}
	}

	// Быстрое обновление лендингов и блогов, например после изменения цен
	public static function updatePrices($data = []){

		// Обновляем цены
		$query = "UPDATE flow_prices
				  SET price = :price,
				  	  recommended = :price,
				  	  profit = :profit
				  WHERE country_code = :country_code AND
				  		target_id = :target_id AND 
				  		offer_id = :offer_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		foreach ($data as &$a) {
			$stmt->bindParam(":price", $a["price"], PDO::PARAM_INT);
			$stmt->bindParam(":profit", $a["profit"], PDO::PARAM_INT);
			$stmt->bindParam(":country_code", $a["country_code"], PDO::PARAM_STR);
			$stmt->bindParam(":target_id", $a["target_id"], PDO::PARAM_INT);
			$stmt->bindParam(":offer_id", $a["offer_id"], PDO::PARAM_INT);
			$stmt->execute();
		}

		// Обновляем потоки
		$query = "SELECT f.f_id as id, f.*
				  FROM flows as f INNER JOIN goods as g ON f.offer_id = g.id
				  WHERE g.offer_status != 'disabled'";
		$stmt = $GLOBALS['DB']->query($query);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = new self($row);

			$landing = new Landing_Generator($item, $item->getLandingAlias());
			$url = $landing->generate();
			if ($item->getBlogId()) {
				$blog = new Blog_Generator($item, $url, $item->getBlogAlias());
				$blog->generate();
			}
		}
	}

	private function generateLink(){
		if (empty($this->landing_alias)) {
			// Создание короткой ссылки
			$encoded_fid = base_convert($this->id, 10, 16);
			$encoded_cid = base_convert($this->landing_id, 10, 16);
			$this->landing_link = $this->user_id . "/" . $encoded_fid . $encoded_cid;
		} else {
			// Использование псевдонима
			$this->landing_link = $this->user_id . "/" . $this->landing_alias;
		}

		if ($this->blog_id) {
			if (empty($this->blog_alias)) {
				// Создание короткой ссылки
				$encoded_fid = base_convert($this->id, 10, 16);
				$encoded_cid = base_convert($this->blog_id, 10, 16);
				return $this->link = $this->user_id . "/" . $encoded_fid . $encoded_cid;
			} else {
				// Использование псевдонима
				return $this->link = $this->user_id . "/" . $this->blog_alias;
			}
		}

		return $this->link = $this->landing_link;
	}

	public function setLandingAlias($alias){
		return $this->landing_alias = self::checkAlias($alias, $this->id, $this->user_id);
	}

	public function setBlogAlias($alias){
		return $this->blog_alias = self::checkAlias($alias, $this->id, $this->user_id);
	}

	public static function checkAlias($alias, $flow_id, $user_id){
		$query = "SELECT f_id FROM flows WHERE f_id != :f_id AND user_id = :user_id AND (landing_alias = :alias OR blog_alias = :alias)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":flow_id", $flow_id, PDO::PARAM_INT);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":alias", $alias, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			return $alias;
		}
		return null;
	}

	/**
	* Проверка существования потока с названием Flow::$name
	*
	* Функия проверяет наличие потока с Flow::$name и Flow::$user_id, чтоб предотвратить сохранение потоков с одинаковым именем
	* Возвращает false если поток Flow::$name не был найден, в другом случае возвращает true
	*
	* @return boolean
	**/
	public function nameExists() {
		$query = "SELECT * FROM flows WHERE user_id = :user_id AND name = :name AND f_id != :id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam( ':name', $this->name, PDO::PARAM_STR );
		$stmt->bindParam( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt->bindParam( ':id', $this->id, PDO::PARAM_INT );
		$stmt->execute();
		if ( $stmt->rowCount() == 0 ) return false;
		return true;
	}

	/**
	* Удаление потока по ID
	*
	* Возвращает true в случае успешного удаления, иначе - false
	*
	* @param integer $id ID потока
	*
	* @return boolean
	*/
	public static function delete($id, $delete_all = false) {
		$item = self::getInstance($id);
		// Удаление лендинга
		$path = "{$_SERVER['DOCUMENT_ROOT']}/streams/{$item->getUserId()}/{$item->getLandingFolderName()}";
    	foreach(glob("{$path}/*") as $file) {
    		if (basename($file) != "redirect.html") {
    			unlink($file);
    		}
		}

		// Удаление блога
		$path = "{$_SERVER['DOCUMENT_ROOT']}/streams/{$item->getUserId()}/{$item->getBlogFolderName()}";
    	foreach(glob("{$path}/*") as $file) {
    		if (basename($file) != "redirect.html") {
    			unlink($file);
    		}
		}

		if ($delete_all) {
			$stmt = $GLOBALS['DB']->prepare("DELETE FROM flows WHERE f_id = :id");
			return $stmt->execute(array(':id'=>$id));
		}
	}

	/**
	* Создание нового субаккаунта
	*
	* Функция проверяет наличие субаккаунта $name у пользователя $user_id.
	* Если имя субаккаунта свободно, он создается
	*
	* @param integer $user_id ID пользователя
	* @param string $name     Название субаккаунта
	*
	* @return boolean
	**/
	public static function addSubaccount($user_id, $name){
		if (self::getUserSubaccount($user_id, $name) == false) {
			$query = "INSERT INTO subaccounts (user_id,name) VALUES(:user_id,:name)";
			$stmt = $GLOBALS['DB']->prepare( $query );
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->bindParam(":name", $name, PDO::PARAM_STR);
			return $stmt->execute();
		}
		return false;
	}

	/**
	* Функция получения субаккаунта
	*
	* Функция возвращает субаккаунт $name пользователя $user_id, или false если субаккаунт не найден.
	*
	* @param integer $user_id ID пользователя
	* @param string $name     Название субаккаунта
	*
	* @return boolean|array
	**/
	private static function getUserSubaccount($user_id, $name) {
		$query = "SELECT * FROM subaccounts WHERE user_id = :user_id AND name = :name";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":name", $name, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			return $stmt->fetch( PDO::FETCH_ASSOC);
		}
		return false;
	}

	/**
	* Возвращает все субаккаунты пользователя
	*
	* @param integer $user_id ID пользователя
	*
	* @return boolean|array
	**/
	public static function getUserSubaccounts($user_id) {
		$query = "SELECT * FROM subaccounts WHERE user_id = :user_id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->execute(array(":user_id" => $user_id));
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	* Возвращает потоки пользователя
	*
	* Если указан $offer_id, функция возвращает потоки пользователя $user_id для оффера $offer_id
	*
	* @param integer $user_id        ID пользователя
	* @param integer|false $offer_id ID оффера
	*
	* @return boolean|array
	**/
	public static function getByUID($user_id, $offer_id = false) {
		$query = "SELECT * FROM flows WHERE user_id = :user_id ";
		if ( $offer_id ) {
			$query .= " AND offer_id = :offer_id";
		}
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam( ":user_id", $user_id, PDO::PARAM_INT );
		if ( $offer_id ) {
			$stmt->bindParam( ":offer_id", $offer_id, PDO::PARAM_INT );
		}
		$stmt->execute();
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	* Возвращает обьект Flow с указанным ID
	*
	* @param integer $id ID потока
	*
	* @return Flow
	**/
	public static function getInstance($id) {
		$query = "SELECT flows.f_id as id, flows.* FROM flows WHERE f_id = :id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->execute(array( ":id" => $id));
		if ($stmt->rowCount() == 0) {
			return null;
		}
		return new self($stmt->fetch(PDO::FETCH_ASSOC));
	}

	public static function getByKey($key) {
		$stmt = $GLOBALS['DB']->prepare("SELECT flows.f_id as id, flows.* FROM flows WHERE `key` = :key");
		$stmt->bindParam(':key', $key, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			return new self($stmt->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Выборка потоков согласно фильтрам
	*
	* @param array $filters Фильтры
	*
	* @return array
	**/
	public static function getAll($filters = array()) {
		$query = "SELECT *, f_id as id FROM flows";

		if ( count( $filters ) > 0 ) {
			$cond = array();
			foreach ($filters as $field=>$value) {
				$cond[] = "{$field} = :{$field}";
			}
			$query .= " WHERE " . implode(" AND ", $cond );
		}

		$query .= " ORDER BY modified DESC";
		$stmt = $GLOBALS['DB']->prepare($query);
		foreach ($filters as $field=>&$value) {
			$type = (is_int($value)) ? PDO::PARAM_INT : PRO_PARAM_STR;
			$stmt->bindParam(":{$field}", $value, $type);
		}

		$stmt->execute();
		$items = array();
		while ($data = $stmt->fetch( PDO::FETCH_ASSOC )) {
			$items[] = new self($data);
		}
		return $items;
	}

}