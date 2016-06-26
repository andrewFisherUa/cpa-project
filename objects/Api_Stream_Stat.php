<?php

class Api_Stream_Stat extends Stat {

    protected function __construct($user_id, $ids = []){
       parent::__construct($user_id, $ids);
    }

    protected function fetchItems(){
        $query = "SELECT id, name FROM api_streams";

        $cond = [];

        if (!empty($this->ids)) {
            $cond[] = "id IN (" . implode(",", $this->ids) . ")";
        }

        if ($this->user_id != 0) {
            $cond[] = "user_id = " . $this->user_id;
        }

        if (!empty($cond)) {
            $query .= " WHERE " . implode(" AND ", $cond);
        }

        $stmt = $GLOBALS['DB']->query($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->addItem($row);
        }
    }

    protected function fetchStat(){
        //
    }

    protected function getStatQuery(){
        //
    }

    protected function getOrdersQueryParameters(){
        $parameters = parent::getOrdersQueryParameters();

        if (!empty($this->ids)) {
            $parameters[] = "t1.source_id IN (" . implode(",", $this->ids) . ")";
        }

        $parameters[] = "t1.source = 'api'";

        return $parameters;
    }

    protected function getOrdersQuery(){

        $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code, t1.source_id as id, t1.status, t1.target_close, t1.status2, t1.target
                 FROM orders as t1 INNER JOIN api_streams as t2 ON t1.source_id = t2.id";

        if (!empty($this->filters['subid'])) {
            $query .= " INNER JOIN order_subid AS os ON t1.id = os.order_id ";
        }

        return $query;
    }
}

?>