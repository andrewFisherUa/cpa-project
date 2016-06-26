<?php

class Webmaster_Stat extends Stat {

    protected function __construct($user_id, $ids = []){
       parent::__construct($user_id, $ids);
    }

    protected function fetchItems(){

        $query = "SELECT t1.user_id as id, t1.login as name FROM users as t1 inner join user_role as t2 on t1.user_id = t2.user_id WHERE t2.role_id = 2";

        if (!empty($this->ids)) {
            $query .= " AND t1.user_id IN (" . implode(",", $this->ids) . ")";
        }

        $stmt = $GLOBALS['DB']->query($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['name'] = $row['id'] . ': ' . $row['name'];
            $this->addItem($row);
        }
    }

    protected function getStatQuery(){
        if (!empty($this->filters["subid"])) {
            $query = "SELECT SUM(t1.`unique`) as `unique`, SUM(t1.`all`) as `all`, t2.user_id as id
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
            $query = "SELECT SUM(t1.`unique`) as `unique`, SUM(t1.`all`) as `all`, t2.user_id as id
                      FROM stat AS t1 INNER JOIN pfinder_keys AS t2 ON t1.pfinder_id = t2.pfinder_id
                      WHERE t1.date_from >= :from_time AND t1.date_to <= :to_time";
        }

        // офферы
        if (!empty($this->ids)) {
            $query .=  " AND t2.user_id IN ( " . implode(",", $this->ids) . ")";
        }

        return $this->addStatFilters($query) . " GROUP BY t2.user_id";
    }

    protected function getOrdersQueryParameters(){
        $parameters = parent::getOrdersQueryParameters();

        if (!empty($this->ids)) {
            $parameters[] = "t1.user_id IN (" . implode(",", $this->ids) . ")";
        }

        return $parameters;
    }

    protected function getOrdersQuery($parameters){

        $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code, t1.user_id as id, t1.status, t1.target_close, t1.status2, t1.target
                  FROM orders AS t1";

        if (!empty($this->filters['landing']) ||
            !empty($this->filters['blog']) ||
            !empty($this->filters['offer']) ||
            !empty($this->filters['source'])) {

            $query .= " INNER JOIN pfinder_keys as t2 ON t1.pfinder_id = t2.pfinder_id";
        }

        if (!empty($this->filters['subid'])) {
            $query .= " INNER JOIN order_subid AS os ON t1.id = os.order_id ";
        }

        return $query;
    }
}

?>