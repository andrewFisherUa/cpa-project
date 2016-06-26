<?php

class Order {
    private $id;
    private $user_id;
    private $status;
    private $order;
    private $amount;
    private $comment;
    private $payed;
    private $webmaster_commission;
    private $commission;
    private $first_name;
    private $last_name;
    private $created;
    private $modified; // редактирование комментария
    private $changed;  // редактирование статуса
    private $ip;
    private $source;
    private $source_id;
    private $phone;
    private $country_code;
    private $target;
    private $target_time;
    private $target_close;
    private $hold;
    private $hold_time;
    private $pass;
    private $oid;
    private $refprofits = array();
    private $products = array();
    private $log = array();

    const STATUS_PROCESSING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_CANCELED = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_RETURN = 4;


    public function __construct($data = array()) {
        $this->id = ( isset($data['id']) ) ? $data['id'] : 0;
        $this->oid = ( isset($data['oid']) ) ? $data['oid'] : 0;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_PROCESSING;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : 0;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : "";
        $this->payed = (isset($data['payed'])) ? $data['payed'] : 0;
        $this->commission = (isset($data['commission'])) ? $data['commission'] : 0;
        $this->webmaster_commission = (isset($data['webmaster_commission'])) ? $data['webmaster_commission'] : 0;
        $this->first_name = (isset($data['first_name'])) ? $data['first_name'] : "";
        $this->last_name = (isset($data['last_name'])) ? $data['last_name'] : "";
        $this->phone = (isset($data['phone'])) ? $data['phone'] : "";
        $this->country_code = (isset($data['country_code'])) ? $data['country_code'] : "";
        $this->created = (isset($data['created'])) ? $data['created'] : time();
        $this->modified = (isset($data['modified'])) ? $data['modified'] : time();
        $this->changed = (isset($data['changed'])) ? $data['changed'] : time();
        $this->source = (isset($data['source'])) ? $data['source'] : "";
        $this->source_id = (isset($data['source_id'])) ? $data['source_id'] : "";
        $this->ip = (isset($data['ip'])) ? $data['ip'] : $_SERVER['REMOTE_ADDR'];
        $this->pass = (isset($data['pass'])) ? $data['pass'] : "";
        $this->target_close = (isset($data['target_close'])) ? $data['target_close'] : 0;
        $this->target = (isset($data['target'])) ? $data['target'] : 0;
        $this->target_time = (isset($data['target_time'])) ? $data['target_time'] : 0;
        $this->hold = (isset($data['hold'])) ? $data['hold'] : 1;
        $this->hold_time = (isset($data['hold_time'])) ? $data['hold_time'] : 86400;
    }

    public function getHoldTime(){
        return $this->hold_time;
    }

    public function getTargetTime(){
        return $this->target_time;
    }

    public static function getInstance($id) {
        $stmt = $GLOBALS['DB']->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 0){
            return null;
        }
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return new self($data);
    }

    public static function getAll($filters = array()){
        $allowed_filters = array("id" => "id = :id",
                                 "oid" => "oid = :oid",
                                 "user_id" => "user_id = :user_id",
                                 "amount_from" => "amount > :amount_from",
                                 "amount_to" => "amount < :amount_to",
                                 "commission_from" => "commission > :commission_from",
                                 "commission_to" => "commission < :commission_to",
                                 "date_from" => "created > :date_from",
                                 "date_to" => "created < :date_to",
                                 "status" => "status = :status",
                                 "source" => "source = :source");
        $query = "SELECT * FROM orders";
        $where = array();

        foreach ($allowed_filters as $key=>$val) {
            if (isset($filters[$key])) {
                $where[$key] = $val;
            }
        }

        if (count($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " ORDER BY id DESC";

        $stmt = $GLOBALS['DB']->prepare($query);
        if (count($where)) {
            $keys = array_keys($where);
            foreach ($keys as &$key) {
                $paramType = (is_string($filters[$key])) ? PDO::PARAM_STR : PDO::PARAM_INT;
                $stmt->bindParam(":{$key}", $filters[$key], $paramType);
            }
        }
        $stmt->execute();
        $items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new self($data);
        }
        return $items;
    }

    public function getProducts(){
        if (empty($this->products)) {
            $query = "SELECT * FROM order_goods WHERE order_id = :id";
            $stmt = $GLOBALS['DB']->prepare($query);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $this->products;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function getCountryCode(){
        return $this->country_code;
    }

    public function getCommission(){
        return $this->commission;
    }


    public function getWebmasterCommission(){
        return $this->webmaster_commission;
    }

    public function getSource(){
        return $this->source;
    }

     public function getSourceId(){
        return $this->source_id;
    }

    public function getId(){
        return $this->id;
    }

    public function getOid(){
        return $this->oid;
    }

    public function getCreated($format=false){
        if ( $format == false ) return $this->created;
        return date("d/m/Y H:i", $this->created);
    }

    public function wasModified(){
        return $this->modified != $this->created;
    }

    public function wasChanged(){
        return $this->changed != $this->created;
    }

    public function getModified($format=false){
        if ( $format == false ) return $this->modified;
        return date("d/m/Y H:i", $this->modified);
    }

    public function getChanged($format=false){
        if ( $format == false ) return $this->changed;
        return date("d/m/Y H:i", $this->changed);
    }

    public function getAmount(){
        return $this->amount;
    }

    public function getFirstName(){
        return $this->first_name;
    }

    public function getLastName(){
        return $this->last_name;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getSourceDetails(){
        if ($this->source == "user-account") {
            $who = $this->getOwner();
            $label = "`{$who->getLogin()}` ({$who->getRoleName()})";
        }

        return $label;
    }

    public function getOwner(){
        return User::getInstance($this->user_id);
    }

    public function getCurrency(){
        return Country::getCurrency($this->country_code);
    }

    public function addProduct($product) {
        $this->products[] = $product;
    }

    public function addProducts($products = array()) {
        $this->products = $products;
    }

    public function getStatusLabel(){
        foreach (self::getStatusList() as $item) {
            if ($item["status"] == $this->status) {
                return $item["label"];
            }
        }
    }

    public function getStatusAlias(){
        switch ($this->status) {
            case self::STATUS_PROCESSING : return 'processing';
            case self::STATUS_CONFIRMED : return 'confirmed';
            case self::STATUS_DELIVERED : return 'delivered';
            case self::STATUS_CANCELED : return 'canceled';
            case self::STATUS_RETURN : return 'return';
        }
    }

    public static function updateComment($id, $comment){
        $modified = time();
        $query = "UPDATE orders SET comment = :comment, modified = :modified WHERE id = :id";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":modified", $modified, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getComment(){
        return $this->comment;
    }

    public function hasComment(){
        return $this->comment != "";
    }

    public function getProductsCount(){
        return count($this->getProducts());
    }

    public function getStatus(){
        return $this->status;
    }

    public function getIp(){
        return $this->ip;
    }

    public static function getStatusList(){
        return array(
            array( "status" => self::STATUS_PROCESSING, "label" => "В обработке"),
            array( "status" => self::STATUS_CONFIRMED, "label" => "Подтвержден"),
            array( "status" => self::STATUS_DELIVERED, "label" => "Забран"),
            array( "status" => self::STATUS_CANCELED, "label" => "Аннулирован"),
            array( "status" => self::STATUS_RETURN, "label" => "Возврат"));
    }

    private function calcProfits(){
        $amount = 0;
        foreach ($this->products as &$product) {
            $product["total_amount"] = $product["price"] * $product["qty"];
            $this->amount += $product["total_amount"];
            $options = Product::getInstance($product["good_id"])->getOptions();
            $profit_level1 = $options->get('refprofit_level1', $this->country_code);
            $profit_level2 = $options->get('refprofit_level2', $this->country_code);
            $profit_level3 = $options->get('refprofit_level3', $this->country_code);

            if ($options->get('refprofit_type', $this->country_code) == "percent") {
                $product["commission"] = $product["total_amount"]*$product["commission"]/100;
                $product["webmaster_commission"] = $product["total_amount"]*$product["webmaster_commission"]/100;
                $this->commission += $product["commission"];
                $this->webmaster_commission += $product["webmaster_commission"];
                $this->refprofits[1]["amount"] += $product["total_amount"]*$profit_level1/100;
                $this->refprofits[2]["amount"] += $product["total_amount"]*$profit_level2/100;
                $this->refprofits[3]["amount"] += $product["total_amount"]*$profit_level3/100;
            } else {
                $product["commission"] *= $product["qty"];
                $product["webmaster_commission"] *= $product["qty"];
                $this->commission += $product["commission"];
                $this->webmaster_commission += $product["webmaster_commission"];
                $this->refprofits[1]["amount"] += $profit_level1*$product["qty"];
                $this->refprofits[2]["amount"] += $profit_level2*$product["qty"];
                $this->refprofits[3]["amount"] += $profit_level3*$product["qty"];
            }
        }
    }

    private function sendEmail(){
        if ($this->email != "") {
            //$mail->sendmail($_SERVER['HTTP_HOST'], 'robot@'.$_SERVER['HTTP_HOST'], $_POST['email'], 'Заказ на сайте '.$_SERVER['HTTP_HOST'], $order);
        }
    }


    public function save(){
        if ($this->id != 0) {
            return false;
        }

        $this->calcProfits();

        $query = "INSERT INTO orders (status, amount, comment, webmaster_commission, commission, first_name, last_name, phone, email, source, source_id, country_code, user_id, created, modified, changed, ip, target, target_time, target_close, hold, hold_time, pass)
                  VALUES (:status, :amount, :comment, :webmaster_commission, :commission, :first_name, :last_name, :phone, :email,:source, :source_id, :country_code, :user_id, :created, :modified, :changed, :ip, :target, :target_time, :target_close, :hold, :hold_time, :pass) ";

        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(':created', $this->created, PDO::PARAM_INT);
        $stmt->bindParam(':modified', $this->modified, PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindParam(':changed', $this->changed, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $this->amount, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $this->comment, PDO::PARAM_STR);
        $stmt->bindParam(':webmaster_commission', $this->webmaster_commission, PDO::PARAM_INT);
        $stmt->bindParam(':commission', $this->commission, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $this->first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $this->last_name, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':source', $this->source, PDO::PARAM_STR);
        $stmt->bindParam(':source_id', $this->source_id, PDO::PARAM_STR);
        $stmt->bindParam(':country_code', $this->country_code, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $this->ip, PDO::PARAM_STR);
        $stmt->bindParam(':target', $this->target, PDO::PARAM_INT);
        $stmt->bindParam(':target_time', $this->target_time, PDO::PARAM_INT);
        $stmt->bindParam(':target_close', $this->target_close, PDO::PARAM_INT);
        $stmt->bindParam(':hold', $this->hold, PDO::PARAM_INT);
        $stmt->bindParam(':hold_time', $this->hold_time, PDO::PARAM_INT);
        $stmt->bindParam(':pass', $this->pass, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $this->id = $GLOBALS["DB"]->lastInsertID();
            $this->saveProducts();
            $this->saveRefprofits();
            return true;
        }
        return false;
    }

    private function saveRefprofits(){
        $this->refprofits[1]["user_id"] = (int) Partners::getReferer($this->user_id);
        $this->refprofits[2]["user_id"] = (int) Partners::getReferer($this->refprofits[1]["user_id"]);
        $this->refprofits[3]["user_id"] = (int) Partners::getReferer($this->refprofits[2]["user_id"]);

        $query = "INSERT INTO order_refprofit (order_id, user_id, level, amount) VALUES (:order_id, :user_id, :level, :amount)";
        $stmt = $GLOBALS['DB']->prepare($query);
        foreach ($this->refprofits as $level=>&$item) {
            if ($item['user_id'] > 0) {
                $stmt->bindParam(":order_id", $this->id, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $item['user_id'], PDO::PARAM_INT);
                $stmt->bindParam(":amount", $item['amount'], PDO::PARAM_INT);
                $stmt->bindParam(":level", $level, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    private function saveProducts(){
        if (empty($this->products)) {
            return false;
        }
        $query = "INSERT INTO order_goods (order_id, good_id, price, price_id, qty, product_name, country_code, total_amount, commission, webmaster_commission, target_id, product_owner)
                  VALUES (:order_id, :good_id, :price, :price_id, :qty, :product_name, :country_code, :total_amount, :commission, :webmaster_commission, :target_id, :product_owner) ";
        $stmt = $GLOBALS['DB']->prepare($query);

        foreach ($this->products as &$product) {
            $stmt->bindParam(':order_id', $this->id , PDO::PARAM_INT);
            $stmt->bindParam(':good_id', $product["good_id"], PDO::PARAM_INT);
            $stmt->bindParam(':product_name', $product["product_name"], PDO::PARAM_INT);
            $stmt->bindParam(':price', $product["price"], PDO::PARAM_INT);
            $stmt->bindParam(':price_id', $product["price_id"], PDO::PARAM_STR);
            $stmt->bindParam(':qty', $product["qty"], PDO::PARAM_INT);
            $stmt->bindParam(':total_amount', $product["total_amount"], PDO::PARAM_INT);
            $stmt->bindParam(':commission', $product["commission"], PDO::PARAM_INT);
            $stmt->bindParam(':webmaster_commission', $product["webmaster_commission"], PDO::PARAM_INT);
            $stmt->bindParam(':target_id', $product["target_id"], PDO::PARAM_INT);
            $stmt->bindParam(':country_code', $product["country_code"], PDO::PARAM_STR);
            $stmt->bindParam(':product_owner', $product["product_owner"], PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    private function fetchLog(){
        $query = "SELECT * FROM orders_logs WHERE order_id = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $this->oid
        ]);

        $this->log = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLog(){
        if (empty($this->log)) {
            $this->fetchLog();
        }

        return $this->log;
    }
}