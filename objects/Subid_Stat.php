<?php

class Subid_Stat extends Stat {

    private $subid;

    protected function __construct($user_id, $subid, $ids = []){
        parent::__construct($user_id, $ids);
        $this->subid = $subid;
    }

    protected function fetchItems(){
      $values = [];
      $cond = [
        "a.date_from >= " . $this->range['from'],
        "a.date_to <= " . $this->range['to'],
      ];

      $query = "SELECT DISTINCT a.subid_val as name
                FROM subid_stat as a ";

      if ($this->user_id > 0) {
        $query .= " INNER JOIN pfinder_keys as b ON a.pfinder_id = b.pfinder_id";
      }

      $query .= " WHERE a.subid_name = '{$this->subid}' AND a.subid_val != ''";

      if (!empty($this->ids)) {
        $r = [];

        if (!empty($this->ids)) {
          foreach ($this->ids as $v) {
            $r[] = "'" . $v . "'";
          }
        }

        $cond[] = "a.subid_val IN (" . implode(",", $r) . ")";
      }

      if ($this->user_id > 0) {
          $cond[] = "b.user_id = " . $this->user_id;
      }

      if (!empty($cond)) {
          $query .= " AND " . implode(" AND ", $cond);
      }

      $stmt = $GLOBALS['DB']->query($query);
      $stmt->execute();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $name = $row['name'];

        $this->addItem([
          "id" => $name,
          "name" => urldecode($name)
        ]);
      }

      $this->ids = $r;
    }

    protected function getStatQuery(){
      $query = "SELECT SUM(t1.`unique`) as `unique`, SUM(t1.`all`) as `all`, t1.subid_val as id
                FROM subid_stat AS t1 INNER JOIN pfinder_keys AS t2 ON t1.pfinder_id = t2.pfinder_id
                WHERE t1.date_from >= :from_time AND t1.date_to <= :to_time AND t1.subid_name = '{$this->subid}' AND t1.subid_val != ''";

      $cond = [];

      if ($this->user_id > 0) {
            $query .= " AND t2.user_id = :user_id";
      }

      // офферы
      if (!empty($this->ids)) {
          $query .=  " AND t1.subid_val IN ( " . implode(",", $this->ids) . ")";
      }

      return $this->addStatFilters($query) . " GROUP BY t1.subid_val";
    }

    protected function getOrdersQueryParameters(){
        $parameters = parent::getOrdersQueryParameters();

        $parameters[] = "t1.source = 'stream'";
        $parameters[] = "t3.{$this->subid} != ''";

        if (!empty($this->ids)) {
          $parameters[] = "t3.{$this->subid} IN (" . implode(",", $this->ids) . ")";
        }

        return $parameters;
    }

    protected function getOrdersQuery(){
      $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code, t3.{$this->subid} as id, t1.status, t1.target_close, t1.status2, t1.target
                FROM orders AS t1 INNER JOIN  order_subid AS t3 ON t1.id = t3.order_id";

      if (!empty($this->filters['landing']) ||
          !empty($this->filters['blog']) ||
          !empty($this->filters['offer']) ||
          !empty($this->filters['source'])) {

          $query .= " INNER JOIN pfinder_keys as t2 ON t1.pfinder_id = t2.pfinder_id";
      }

      return $query;
    }

    // Функция считает количество заказов и зароботок вебмастера
    protected function fetchOrders(){
        $data = $this->fetchOrdersQuery();

        foreach ($data as $item) {

            $id = $item['id'];

            if (!isset($this->data[$id])) {
              $this->addItem([
                "id" => $id,
                "name" => $id
              ]);
            }

            // Определяем к какой категории относится заказ
            $type = $this->getType($item);
            $c = Country::getCurrencyCode($item['country_code']);

            if ($type != "trash") {                
                $this->data["_convert"][$id][$c][$type] += $item['amount'];
                $this->data[$id]['count']['total']++;
            }

            if ($item['status'] == Order::STATUS_DELIVERED) {
                $this->data[$id]['count']['delivered']++;
            }

            $this->data[$id]['count'][$type]++;
        }
    }
}

?>