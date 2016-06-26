<?php

class Payment {

  private $id;
  private $user_id;
  private $status;
  private $created;
  private $approved;
  private $approved_by;
  private $type;
  private $currency;
  private $comment;
  private $wallet;
  private $amount;
  private $balance_before;
  private $balance_after;
  private $log; // PaymentLog object

  const STATUS_APPROVED = "approved";
  const STATUS_MODERATION = "moderation";
  const STATUS_CANCELED = "canceled";

  const TYPE_ACCOUNT = "account";
  const TYPE_REFERAL = "referal";
  const TYPE_ALL = "all";

  public function __construct($data){
    $this->id = isset($data['payment_id']) ? $data['payment_id'] : 0;
    $this->user_id = isset($data['user_id']) ? $data['user_id'] : 0;
    $this->status = isset($data['status']) ? $data['status'] : self::STATUS_MODERATION;
    $this->approved_by = isset($data['approved_by']) ? $data['approved_by'] : 0;
    $this->type = isset($data['type']) ? $data['type'] : 0;
    $this->currency = isset($data['currency']) ? $data['currency'] : "";
    $this->comment = isset($data['comment']) ? $data['comment'] : "";
    $this->created = isset($data['created']) ? $data['created'] : time();
    $this->approved = isset($data['approved']) ? $data['approved'] : 0;
    $this->amount = isset($data['amount']) ? $data['amount'] : 0;
    $this->balance_before = isset($data['balance_before']) ? $data['balance_before'] : 0;
    $this->balance_after = isset($data['balance_after']) ? $data['balance_after'] : 0;
    $this->wallet = isset($data['wallet']) ? $data['wallet'] : "";
  }

  public static function getInstance($id) {
    $query = "SELECT * FROM payments WHERE payment_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([$id]);
    return new self($stmt->fetch(PDO::FETCH_ASSOC));
  }

  public function getId(){
    return $this->id;
  }

  public function getUserId(){
    return $this->user_id;
  }

  public function getStatus(){
    return $this->status;
  }

  public function getCreated(){
    return $this->created;
  }

  public function wasApproved(){
    return $this->status == self::STATUS_APPROVED;
  }

  public function getApproved(){
    return $this->approved;
  }

  public function getApprovedBy(){
    return $this->approved_by;
  }

  public function getType(){
    return $this->type;
  }

  public function getBalanceBefore(){
    return $this->balance_before;
  }

  public function getBalanceAfter(){
    return $this->balance_after;
  }

  public function getStatusAlias(){
    switch ($this->status) {
      case self::STATUS_APPROVED : return 'Одобрена';
      case self::STATUS_MODERATION : return 'На модерации';
      case self::STATUS_CANCELED : return 'Отклонена';
    }
  }

  public function getCurrency(){
    return $this->currency;
  }

  public function getComment(){
    return $this->comment;
  }

  public function getWallet(){
    return $this->wallet;
  }

  public function getAmount(){
    return $this->amount;
  }

  public static function getAll($user_id = null) {
    if (is_null($user_id)) {
      $query = "select * from payments where status = 'approved' OR status = 'moderation' order by status desc, created desc";
      $stmt = $GLOBALS['DB']->query($query);
    } else {
      $query = "SELECT * FROM payments WHERE user_id = ? ORDER BY created DESC";
      $stmt = $GLOBALS['DB']->prepare($query);
      $stmt->execute([
        $user_id
      ]);
    }

    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $items[] = new self($row);
    }

    return $items;
  }

  public static function valid($data){

    $errors = [];

    // Check if user exists
    $query = "SELECT user_id FROM users WHERE user_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $data['user_id']
    ]);

    if ($stmt->rowCount() == 0) {
      $errors[] = "Incorrect user_id. User not found";
    }

    if (empty($data['wallet'])) {
      $errors[] = "Incorrect wallet id";
    }

    if (!empty($errors)) {
      return $errors;
    }

    return true;
  }

  public static function ask($data){
    if (self::valid($data) === true) {
      $data['status'] = self::STATUS_MODERATION;
      $data['approved_by'] = 0;
      $data['approved'] = 0;
      $p = new self($data);
      return $p->save();
    }

    return false;
  }

  private function save(){
    $q = "INSERT INTO payments (type, amount, user_id, status, comment, created, approved, approved_by, currency, wallet)
          VALUES (:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10)";
    $stmt = $GLOBALS['DB']->prepare($q);
    $stmt->bindParam(":p1", $this->type, PDO::PARAM_INT);
    $stmt->bindParam(":p2", $this->amount, PDO::PARAM_INT);
    $stmt->bindParam(":p3", $this->user_id, PDO::PARAM_INT);
    $stmt->bindParam(":p4", $this->status, PDO::PARAM_STR);
    $stmt->bindParam(":p5", $this->comment, PDO::PARAM_STR);
    $stmt->bindParam(":p6", $this->created, PDO::PARAM_INT);
    $stmt->bindParam(":p7", $this->approved, PDO::PARAM_INT);
    $stmt->bindParam(":p8", $this->approved_by, PDO::PARAM_INT);
    $stmt->bindParam(":p9", $this->currency, PDO::PARAM_STR);
    $stmt->bindParam(":p10", $this->wallet, PDO::PARAM_STR);
    return $stmt->execute();
  }

  public static function addWallet($user_id, $wallet_id, $currency = "RUB"){
    // check if wallet already exists
    $query = "SELECT wallet_id FROM user_wallet WHERE user_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $user_id
    ]);

    if ($stmt->rowCount() > 0) {
      $wallets = $stmt->fetchAll(PDO::FETCH_COLUMN);
      if (in_array($wallet_id, $wallets)) {
        return FALSE;
      }

      $main = 0;
    } else {
      $main = 1;
    }

    $query = "INSERT INTO user_wallet (user_id, wallet, currency, created, main) VALUES (:p1, :p2, :p3, ".time().", :p4)";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->bindParam(":p1", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":p2", $wallet_id, PDO::PARAM_STR);
    $stmt->bindParam(":p3", $currency, PDO::PARAM_STR);
    $stmt->bindParam(":p4", $main, PDO::PARAM_INT);
    return $stmt->execute();
  }

  public static function getWallets($user_id = null) {
    if (is_null($user_id)) {
      return $GLOBALS['DB']->query('SELECT * FROM user_wallet')->fetchAll(PDO::FETCH_ASSOC);
    }

    $query = "SELECT * FROM user_wallet WHERE user_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $user_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function deleteWallet($user_id, $wallet_id){
    $query = "DELETE FROM user_wallet WHERE user_id = :p1 AND wallet = :p2";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->bindParam(":p1", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":p2", $wallet_id, PDO::PARAM_STR);
    return $stmt->execute();
  }

  private function closeOrders() {

    $time = time();

    $query = "SELECT SUM(webmaster_commission) as amount, country_code
              FROM pass
              WHERE closed = 0 AND user_id = ? AND created < " . $time . "
              GROUP BY country_code";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([$this->user_id]);

    if ($stmt->rowCount()) {

      $GLOBALS['DB']->exec("UPDATE pass SET closed = 1, closed_time = " . $time . " WHERE user_id = " . $this->user_id . " AND created < " . $time);

      $query = "UPDATE accounts SET balance = balance + :amount WHERE user_id = :user_id AND type = :type";
      $stmt2 = $GLOBALS['DB']->prepare($query);

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['amount'] > 0) {
          $stmt2->bindParam(":amount", $row['amount'], PDO::PARAM_INT);
          $stmt2->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
          $stmt2->bindParam(":type", $row['country_code'], PDO::PARAM_STR);
          $stmt2->execute();
        }

        Audit::addRecord([
          "group" => "payment",
          "subgroup" => "close_orders",
          "action" => "Закрытие заказов",
          "details" => [
            "payment_id" => $this->id,
            "amount" => $row['amount'],
            "country" => $row['country_code'],
          ]
        ]);

      }
    }
  }

  private function closeRefprofits(){
    $time = time();

    $query = "UPDATE order_refprofit
             SET closed = 1, changed = {$time}
             WHERE hold = 0 AND closed = 0 AND user_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $this->user_id
    ]);

    $query = "SELECT SUM(t1.amount) as amount, t2.country_code
              FROM order_refprofit AS t1 INNER JOIN orders AS t2 ON t1.order_id = t2.id
              WHERE t1.user_id = ? AND t1.closed = 1 AND t1.changed = {$time}
              GROUP BY t2.country_code";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $this->user_id
    ]);

    if ($stmt->rowCount() > 0) {
      $query = "UPDATE accounts SET balance = balance + ? WHERE user_id = ? AND type = ?";
      $u_stmt = $GLOBALS["DB"]->prepare($query);

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $u_stmt->execute([
          $row['amount'],
          $this->user_id,
          $row['country_code']
        ]);

        Audit::addRecord([
          "group" => "payment",
          "subgroup" => "close_refprofits",
          "action" => "Закрытие реферальных начислений",
          "details" => [
            "payment_id" => $this->id,
            "amount" => $row['amount'],
            "country" => $row['country_code'],
          ]
        ]);
      }
    }
  }

  private function canBeApproved(){

    if ($this->status == self::STATUS_APPROVED) {
      return false;
    }

    $this->closeRefprofits();
    $this->closeOrders();

    // Получение баланса
    $balance = [];
    $default_currency = "RUB";

    $query = "SELECT t1.balance, t2.currency_code, t1.`default`
              FROM accounts as t1 INNER JOIN country as t2 ON t1.`type` = t2.code
              WHERE t1.user_id = " . $this->user_id;

    $stmt = $GLOBALS['DB']->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $balance[$row['currency_code']] = $row['balance'];

      if ($row['default'] == 1) {
        $default_currency = $row['currency_code'];
      }
    }

    // Проверяем хватит ли для выплаты сконвертированного баланса
    $converted_balance = 0;
    $details = [];

    foreach ($balance as $currency => $amount) {
      $a = (int) $amount;
      $temp = Converter::getConvert($currency, $this->currency, $a, $default_currency);
      $converted_balance += $temp['amount'];

      $details[$currency . $this->currency] = $a . $currency . " => " . $temp['amount'] . $this->currency . ". Курс: " . $temp['rate'];
    }

    if ($balance[$this->currency] >= $this->amount) {
      // Средств для выплаты достаточно

      $this->balance_before = $converted_balance;
      $this->balance_after = $this->balance_before - $this->amount;

      return true;
    }

    if ($converted_balance >= $this->amount) {
      // Средств достаточно. Сконвертировать все балансы в валюту выплаты. Закрыть pass по всем валютам
      $country_code = Country::getCode($this->currency);

      // Обнуляем личные балансы
      $stmt = $GLOBALS['DB']->exec("UPDATE accounts SET balance = 0 WHERE user_id = " . $this->user_id);

      // Перечисляем средства на личный баланс
      $query = "UPDATE accounts SET balance = :balance
                WHERE type = :type AND user_id = :user_id";
      $stmt = $GLOBALS['DB']->prepare($query);
      $stmt->bindParam(":balance", $converted_balance, PDO::PARAM_INT);
      $stmt->bindParam(":type", $country_code, PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
      $stmt->execute();

      $details["user_currency"] = $default_currency;
      $details["converted"] = $converted_balance;
      $details["payment_currency"] = $this->currency;
      $details["payment_id"] = $this->id;

      Audit::addRecord([
        "group" => "payment",
        "subgroup" => "convert",
        "action" => "Перевод баланса в валюту выплаты",
        "details" => $details
      ]);

      $this->balance_before = $converted_balance;
      $this->balance_after = $this->balance_before - $this->amount;

      return true;
    }

    return false;
  }

  public static function approve($id){

    $p = self::getInstance($id);

    if ($p->canBeApproved()) {

      // Снимаем средства с баланса
      $type = Country::getCode($p->getCurrency());

      $query = "UPDATE accounts SET balance = balance - :amount
                WHERE `type` = :type AND user_id = :user_id";
      $stmt = $GLOBALS['DB']->prepare($query);
      $stmt->bindParam(":type", $type, PDO::PARAM_STR);
      $stmt->bindParam(":amount", $p->getAmount(), PDO::PARAM_INT);
      $stmt->bindParam(":user_id", $p->getUserId(), PDO::PARAM_INT);
      $stmt->execute();

      $query = "UPDATE payments SET
                status = '" . self::STATUS_APPROVED . "',
                approved = " . time() . ",
                approved_by = " . User::get_current_user_id() . ",
                balance_before = ?,
                balance_after = ?
                WHERE payment_id = ?";
      $stmt = $GLOBALS['DB']->prepare($query);
      $stmt->execute([
        $p->getBalanceBefore(),
        $p->getBalanceAfter(),
        $p->getId()
      ]);

      Audit::addRecord([
        "group" => "payment",
        "subgroup" => "approve",
        "action" => "Одобрение выплаты `{$p->getId()}`",
        "priority" => Audit::HIGH_PRIORITY,
        "details" => [
          "payment_id" => $p->getId(),
          "amount" => $p->getAmount(),
        ]
      ]);

      return true;
    }

    return false;
  }

  public static function cancel($id){
    $query = "UPDATE payments SET
              status = '" . self::STATUS_CANCELED . "',
              approved = " . time() . ",
              approved_by = " . User::get_current_user_id() . "
              WHERE payment_id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $r = $stmt->execute([$id]);

    return $r;
  }

  public static function addComment($id, $comment){
    $query = "UPDATE payments SET comment = :comment WHERE payment_id = :id";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

}