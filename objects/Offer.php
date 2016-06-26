<?php

class Offer extends Goods {

	private $description;
	private $type;
	private $flows;
	private $contents;
	private $traffic_sources;
	private $stat_index = [];

	const TYPE_GENERAL = 0;
	const TYPE_GENERAL_WITH_CONFIRMATION = 2;
	const TYPE_PRIVATE = 3;

	/**
	 *
	 * @param data
	 * @param country_code
	 */
	public function __construct($data, $country_code = null){
		parent::__construct($data, $country_code);
		$this->description = (isset($data['description'])) ? $data['description'] : "";
		$this->type = (isset($data['type'])) ? $data['type'] : self::TYPE_GENERAL;
		$this->stat_index = [];
	}

	/**
	 * Достает из БД категории, к которым принадлежит товар
	 */
	protected function fetchCategories(){
		if (!empty($this->categories)) {
			return false;
		}
		$type = Categories::TYPE_OFFER_CATEGORY;
		$query = "SELECT c.id, c.name FROM categories AS c INNER JOIN goods2categories AS gc ON c.id = gc.c_id WHERE gc.g_id = :id AND c.type = :type";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":type", $type, PDO::PARAM_STR);
		$stmt->execute();
		$this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getTypeLabel(){
		switch ($this->type) {
			case self::TYPE_GENERAL : return "Общий";
			case self::TYPE_GENERAL_WITH_CONFIRMATION : return "Общий с подтверждением";
			case self::TYPE_PRIVATE : return "Приватный";
		}
	}

	public static function getTypeList(){
		return array(
			array("type" => self::TYPE_GENERAL, "label" => "Общий"),
			array("type" => self::TYPE_GENERAL_WITH_CONFIRMATION, "label" => "Общий с подтверждением"),
			array("type" => self::TYPE_PRIVATE, "label" => "Приватный"));
	}

	public function savePrivateOfferWebmasters($users = array()){
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM offer_webmaster WHERE offer_id = :id");
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->execute();

		$query = "INSERT INTO offer_webmaster(offer_id, user_id) VALUES (:offer_id, :user_id)";
		$stmt = $GLOBALS['DB']->prepare($query);
		foreach ($users as &$user_id) {
			$stmt->bindParam(":offer_id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->execute();
		}

		if ($this->getType() == self::TYPE_PRIVATE) {
			// Удалить контент со всех потоков с этим оффером
			$query  = "SELECT f_id FROM flows WHERE offer_id = ? AND user_id NOT IN (" . implode(", ", $users) . ")";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute([
				$this->id,
			]);

			while ($id = $stmt->fetchColumn()) {
				Flow::delete($id);
			}
		}
	}

	public function getPrivateOfferWebmasters(){
		$query = "SELECT t2.user_id, t2.login
		         FROM offer_webmaster AS t1 INNER JOIN users AS t2 ON t1.user_id = t2.user_id
		         WHERE t1.offer_id = :offer_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":offer_id", $this->id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Возвращает описание оффера
	 */
	public function getDescription(){
		return $this->description;
	}

	/**
	 * Возвращает тип оффера
	 */
	public function getType(){
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setDescription($param) {
		$this->description = $param;
	}

	/**
	 * Возвращает потоки, созданные для оффера
	 */
	public function getFlows(){
		$this->fetchFlows();
		return $this->flows;
	}

	/**
	 * Достает из БД потоки, связанные с оффером
	 */
	private function fetchFlows(){

	}

	/**
	 * Возвращает источники траффика, созданные для оффера
	 */
	public function getTrafficSources(){
		$this->fetchTrafficSources();
		return $this->trafic_sources;
	}

	/**
	 * Достает из БД потоки, связанные с оффером
	 */
	private function fetchTrafficSources(){
		if (!empty($this->traffic_sources)) {
			return false;
		}

		$stmt = $GLOBALS['DB']->prepare("SELECT * FROM traffic_sources");
		$stmt->execute();
		while ( $data = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$this->trafic_sources[$data['id']] = $data;
			$this->trafic_sources[$data['id']]["selected"] = false;
		}

		$stmt = $GLOBALS['DB']->prepare("SELECT t_id FROM goods2traffic WHERE g_id = :id");
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->execute();

		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->trafic_sources[$data['t_id']]["selected"] = true;
		}
	}

	/**
	 * Возвращает контент (лендинги и блоги), созданные для оффера
	 */
	public function getContents(){
		$this->fetchContents();
		return $this->contents;
	}

	/**
	 * Достает из БД основное изображение товара
	 */
	protected function fetchMainImage(){
		if (empty($this->main_image)) {
			$query = "SELECT t1.id, t1.image AS name
		          FROM goodimg AS t1 INNER JOIN goods AS t2 ON t2.id = t1.id_good
		          WHERE t2.logo = t1.id AND t2.id = :id";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			$stmt->execute();
			$this->main_image = $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}

	private function fetchContents(){
		$query = "SELECT * FROM offer_content WHERE offer_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() > 0){
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$this->content[$row['landing_id']][] = $row['blog_id'];
			}
		}
	}

	/**
	 *
	 * @param id
	 * @param country_code
	 */
	public static function getInstance($id, $country_code = null){
		$query = "SELECT id, owner AS user_id, name, offer_status as status, created, modified, longtext4 AS description, type, available_in_shop, available_in_offers, gid
		          FROM goods WHERE id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() == 0) {
			return null;
		}

		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return new self($data, $country_code);
	}

	/**
	 * Возвращает список всех офферов
	 */
	public static function getAll(){
		$query = "SELECT id, owner AS user_id, name, offer_status AS status, created, modified, longtext4 AS description, type, available_in_shop, available_in_offers, priority
		          FROM goods ORDER BY offer_status ASC, priority DESC, id DESC";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute();

		$items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        	$items[] = new self($data);
        }
        return $items;
	}

	public static function getByUID($user_id = null){
		if ( is_null($user_id)) {
			return self::getAll();
		}

        $roles = array_column( Privileged_User::get_roles( $user_id ), "role_name" );

        if ( Privileged_User::has_role( $user_id, "webmaster" ) ) {
            // Офферы пользователя
            $query = "SELECT g.id, g.name, g.owner AS user_id, g.featured, g.longtext4 AS description, g.offer_status AS status, g.type, available_in_shop, available_in_offers
                  FROM users2goods AS ug INNER JOIN goods AS g ON g.id = ug.g_id
                  WHERE ug.u_id = :user_id
                  ORDER BY g.id DESC";
        }

        if ( Privileged_User::has_role( $user_id, "advertiser" ) ) {
        	// Офферы рекламодателя
            $query = "SELECT id, name, featured, owner AS user_id, longtext4 AS description, offer_status as status, type, available_in_shop, available_in_offers
                  FROM goods WHERE g.owner = :user_id
                  ORDER BY id DESC";
        }

        if (empty($query)) {
        	return false;
        }

        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        	$temp = new self($data);
        	if ($temp->isAvailableToUser($user_id)) {
        		$items[] = $temp;
        	}
        }
        return $items;
	}

	public static function updStatus($id, $status){
		$stmt = $GLOBALS['DB']->prepare("UPDATE goods SET offer_status = :status WHERE id = :id");
		$stmt->bindParam(":status", $status, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();

		if ($status == "disabled") {
			// Удалить контент со всех потоков с этим оффером
			$stmt = $GLOBALS['DB']->prepare("SELECT f_id FROM flows WHERE offer_id = :offer_id");
			$stmt->bindParam(":offer_id", $id, PDO::PARAM_INT);
			$stmt->execute();
			while ($id = $stmt->fetchColumn()) {
				Flow::delete($id);
			}
		}
	}

	/**
	 * Сохранение оффера
	 */
	public function save(){
		if ($this->id == 0) {
			$query = "INSERT INTO goods(name, type, offer_status, longtext4, owner, created, modified, available_in_shop, available_in_offers, gid) VALUES (:name, :type, :status, :description, :user_id, :created, :modified, :available_in_shop, :available_in_offers, :gid)";
		} else {
			$query = "UPDATE goods SET name = :name, type = :type, offer_status = :status, longtext4 = :description, owner = :user_id, modified = :modified, available_in_shop = :available_in_shop, available_in_offers = :available_in_offers, gid = :gid WHERE id = :id";
		}

		$stmt = $GLOBALS['DB']->prepare($query);

		if ($this->id == 0) {
			$stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
		} else {
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		}

		$stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
		$stmt->bindParam(":type", $this->type, PDO::PARAM_INT);
		$stmt->bindParam(":status", $this->status, PDO::PARAM_STR);
		$stmt->bindParam(":description", $this->description, PDO::PARAM_STR);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":gid", $this->gid, PDO::PARAM_INT);
		$stmt->bindParam(":modified", $this->modified, PDO::PARAM_INT);
		$stmt->bindParam(":available_in_shop", $this->available_in_shop, PDO::PARAM_INT);
		$stmt->bindParam(":available_in_offers", $this->available_in_offers, PDO::PARAM_INT);
		$stmt->execute();

		if ($this->id == 0) {
			$this->id = $GLOBALS['DB']->lastInsertId();
		}
	}

	public function saveMainImage(){
		$main_image = $this->getMainImage();

		if ($main_image["id"] == 0) {
			$query = "INSERT INTO goodimg (id_good, image) VALUES (:id, :name)";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":name", $main_image["name"], PDO::PARAM_INT);
			$stmt->execute();

			$main_image["id"] = $GLOBALS["DB"]->lastInsertId();

			$query = "UPDATE goods SET logo = :logo_id WHERE id = :id";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":logo_id", $main_image["id"], PDO::PARAM_INT);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}

	public function connectedBy($user_id){
		$query = "SELECT u_id FROM users2goods WHERE u_id = :u_id AND g_id = :g_id ";
	    $stmt = $GLOBALS['DB']->prepare($query);
	    $stmt->bindParam(':u_id', $user_id, PDO::PARAM_INT);
	    $stmt->bindParam(':g_id', $this->id, PDO::PARAM_INT);
	    $stmt->execute();
	    return ($stmt->rowCount() > 0);
	}

	public function isAvailableToUser($user_id){

		if (User::isAdmin($user_id)) {
			return true;
		}

		if (!$this->isAvailableInOffers()) {
	        return false;
	    }

	    if ($this->getStatus() == self::STATUS_ACTIVE || $this->getStatus() == self::STATUS_DISABLED) {

	    	if ($this->getType() == self::TYPE_PRIVATE) {
		    	return in_array($user_id, array_column($this->getPrivateOfferWebmasters(), "user_id"));
		    }

		    if ($this->getType() == self::TYPE_GENERAL_WITH_CONFIRMATION ) {
				return $this->getOption("available_webmasters") == 1;
			}

			if ($this->getType() == self::TYPE_GENERAL) {
				return true;
			}
	    }

	    return false;
	}

	public function canBeConnectedBy($user_id){
		$temp = false;
		if ( User::has_role($user_id, 2) && !$this->connectedBy($user_id)) {
			$temp = true;
		}

		if ($this->getType() == self::TYPE_PRIVATE ) {
			$temp &= in_array($user_id, array_column($this->getPrivateOfferWebmasters(), "user_id"));
		}

		if ($this->getType() == self::TYPE_GENERAL_WITH_CONFIRMATION ) {
			$temp &= $this->getOption("available_webmasters") == 1;
		}

		return $temp;
	}

	public static function getContent( $offer_id, $type = "both" ) {
        if ( $type == "both" ) {
            $query = "SELECT landing_id, blog_id FROM offer_content WHERE offer_id = :offer_id ORDER BY landing_id";
        } else {
            $query = "SELECT DISTINCT t2.* FROM offer_content AS t1 INNER JOIN content AS t2 ON t1.{$type}_id = t2.c_id WHERE t1.offer_id = :offer_id ORDER BY t2.name";
        }

        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->execute( array( ":offer_id" => $offer_id ) );
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function getFiltered($items, $filters){
    	$filtered = array();
		for ($i=0; $i < count($items); $i++) {
			if (isset($filters['id']) && $items[$i]->getId() != $filters['id'] ) {
				continue;
			}
			if (isset($filters['country_code']) && !$items[$i]->inCountry($filters['country_code'])) {
		      continue;
		    }

		    if (isset($filters['target']) && !$items[$i]->hasTarget($filters['target'], $filters['country_code'])) {
		      continue;
		    }

		    if (isset($filters['category']) && !$items[$i]->hasCategory($filters['category'])) {
		        continue;
		    }

		    if (isset($filters['status'])){
		    	if (is_array($filters['status'])) {
		    		if (!in_array($items[$i]->getStatus(), $filters['status'])) continue;
		    	} else if (!($items[$i]->getStatus() == $filters['status'])) {
				    continue;
		    	}
		    }

		    if (isset($filters['available_in_offers']) && !($items[$i]->isAvailableInOffers())) {
		    	continue;
		    }

		    if (isset($filters['available_to_user']) && !($items[$i]->isAvailableToUser($filters['available_to_user']))) {
		    	continue;
		    }

		    $filtered[] = $items[$i];
		}

		return $filtered;
	}

	public static function getRules($id = 0){
		$query = "SELECT offer_id AS id, `text` FROM offer_connection_rules WHERE offer_id = ?";
	    if ($id != 0) {
	        $query .= " OR offer_id = 0";
	    }

	    $stmt = $GLOBALS['DB']->prepare($query);
	    $stmt->execute([$id]);

	    if ($stmt->rowCount() == 0) {
	        $response['text'] = "";
	    } else {
	    	if ($id == 0) {
	    		return $stmt->fetchColumn(1);
	    	}

	        $items = [];
	        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	            $items[$row['id']] = $row['text'];
	        }

	        return isset($items[$id]) ? $items[$id] : $items[0];
	    }
	}

	public function clearTargets(){
		$stmt = $GLOBALS['DB']->prepare("DELETE from goods2targets WHERE g_id = ?");
		$stmt->execute([$this->id]);
	}

	private function fetchStatIndex(){

		$this->stat_index = [
			"cr" => "н/д",
			"epc" => "н/д",
		];

		$query = "SELECT * FROM offer_stat WHERE offer_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->id
		]);

		if ($stmt->rowCount() > 0) {
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			switch ($data["epc_mode"]) {
				case "specific" : $this->stat_index["epc"] = $data["specific_epc"]; break;
				case "stat" : $this->stat_index["epc"] = $data["stat_epc"]; break;
				default : $this->stat_index["epc"] = "н/д";
			}

			switch ($data["cr_mode"]) {
				case "specific" : $this->stat_index["cr"] = $data["specific_cr"]; break;
				case "stat" : $this->stat_index["cr"] = $data["stat_cr"]; break;
				default : $this->stat_index["cr"] = "н/д";
			}

		}
	}

	public function getCr(){
		if (empty($this->stat_index)) {
			$this->fetchStatIndex();
		}

		return $this->stat_index['cr'];
	}

	public function getEpc(){
		if (empty($this->stat_index)) {
			$this->fetchStatIndex();
		}

		return $this->stat_index['epc'];
	}
}

?>
