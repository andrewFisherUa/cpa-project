<?php

class Date_Stat extends Stat {

    private $interval = [];

    protected function __construct($user_id){
       parent::__construct($user_id, $ids);
    }

    protected function fetchItems(){
        $date = new DateTime(date("d-m-Y", $this->range['from']), new DateTimeZone('Europe/Kiev'));
        $start = $date->format('U');

        while ($start < $this->range['to']) {
          $this->addItem([
            "id" => $start,
            "name" => $start
          ]);

          $this->interval[] = $start;
          $start = $date->add(new DateInterval('P1D'))->format('U');
        }

        $this->interval[] = $start;
    }

    protected function getStatQuery(){
        if (!empty($this->filters["subid"])) {
            $query = "SELECT SUM(t1.`unique`) as `unique`, SUM(t1.`all`) as `all`, t1.date_from as id
                      FROM subid_stat AS t1 INNER JOIN pfinder_keys AS t2 ON t1.pfinder_id = t2.pfinder_id
                      WHERE t1.date_from >= :from_time AND t1.date_to <= :to_time";

            $cond = [];

            // SUBID
            if (!empty($this->filters['subid'])) {
                for ($i=1;$i<6;$i++) {
                    if (!empty($this->filters['subid']['subid'.$i])) {
                        $cond[] = "(t1.subid_name = 'subid{$i}' AND t1.subid_val IN (" . implode(",", $this->filters['subid']['subid'.$i]) . "))";
                    }
                }
            }

            $query .= " AND " . implode(" AND ", $cond);

        } else {
            $query = "SELECT SUM(t1.`unique`) as `unique`, SUM(t1.`all`) as `all`, t1.date_from as id
                      FROM stat AS t1 INNER JOIN pfinder_keys AS t2 ON t1.pfinder_id = t2.pfinder_id
                      WHERE t1.date_from >= :from_time AND t1.date_to <= :to_time";
        }

        if ($this->user_id > 0) {
            $query .= " AND t2.user_id = :user_id";
        }

        return $this->addStatFilters($query) . " GROUP BY t1.date_from ORDER BY t1.date_from";
    }

    protected function getOrdersQueryParameters(){
        $parameters = parent::getOrdersQueryParameters();

        if (!empty($this->ids)) {
            $ids = [];
            foreach ($this->ids as $id) {
                $ids[] = "'{$id}'";
            }

            $parameters[] = "t1.country_code IN (" . implode(",", $ids) . ")";
        }

        return $parameters;
    }

    protected function getOrdersQuery(){

        $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code, t1.created, t1.status, t1.target_close, t1.status2, t1.target FROM orders as t1";

        if (!empty($this->filters['landing']) ||
            !empty($this->filters['blog']) ||
            !empty($this->filters['offer']) ||
            !empty($this->filters['source'])) {

            $query .= " INNER JOIN pfinder_keys as t2 ON t1.pfinder_id = t2.pfinder_id ";
        }

        if (!empty($this->filters['subid'])) {
            $query .= " INNER JOIN order_subid AS os ON t1.id = os.order_id ";
        }

        return $query;
    }

    protected function fetchOrdersQuery(){
        $data = parent::fetchOrdersQuery();

        $sorted = [];

        foreach ($data as $item) {
            for ($i=1; $i < count($this->interval); $i++) {
                if ($item['created'] < $this->interval[$i]) {
                    $item['id'] = $this->interval[$i-1];
                    $sorted[] = $item;
                    break;
                }
            }
        }

        return $sorted;
    }
}

?>