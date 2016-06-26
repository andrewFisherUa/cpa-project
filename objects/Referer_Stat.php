<?php

class Referer_Stat extends Stat {

    protected function __construct($user_id, $ids = []){
        parent::__construct($user_id, $ids);
    }

    protected function fetchItems(){
      $values = [];

      $query = "SELECT DISTINCT a.referer 
                FROM order_subid as a INNER JOIN orders as b ON a.order_id = b.id
                WHERE referer != ''";

      if (!empty($this->ids)) {
        $r = [];

        if (!empty($this->ids)) {
          foreach ($this->ids as $v) {
            $r[] = "'" . $v . "'";
          }
        }

        $cond[] = "a.referer IN (" . implode(",", $r) . ")";
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

        $referer = parse_url($row['referer']);

        $this->addItem([
          "id" => $referer['host'],
          "name" => $referer['host']
        ]);
      }

      $this->ids = $r;
    }

    protected function fetchStat(){
        //
    }

    protected function getStatQuery(){
        //
    }

    protected function getOrdersQueryParameters(){
        $parameters = parent::getOrdersQueryParameters();

        $parameters[] = "t3.referer != ''";

        if (!empty($this->ids)) {
          $parameters[] = "t3.{$this->subid} IN (" . implode(",", $this->ids) . ")";
        }

        return $parameters;
    }

    protected function getOrdersQuery(){

      $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code, t3.referer as id, t1.status, t1.target_close, t1.status2, t1.target
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

            $referer = parse_url($item['id']);
            $id = $referer['host'];

            if (!isset($this->data[$id])) {
              $this->addItem([
                "id" => $id,
                "name" => $id
              ]);
            }

            // Определяем к какой категории относится заказ
            $type = $this->getType($item);

            if ($type != "trash") {
                $c = Country::getCurrencyCode($item['country_code']);
                $a = Converter::getConvert($c, "RUB", $item['amount']);
                $b = round($a['amount']);

                $this->data[$id]["amount"][$type] += $b;
                $this->data[$id]["amount"]['total'] += $b;

                $this->data[$id]['count']['total']++;
            }

            $this->data[$id]['count'][$type]++;
        }
    }
}

?>