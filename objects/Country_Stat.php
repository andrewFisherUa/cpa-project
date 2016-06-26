<?php

class Country_Stat extends Stat {

    protected function __construct($user_id, $ids){
       parent::__construct($user_id, $ids);
    }

    protected function fetchItems(){

        $query = "SELECT code AS id, name FROM country";

        if (!empty($this->ids)) {
            $ids = [];
            foreach ($this->ids as $id) {
                $ids[] = "'{$id}'";
            }

            $query .= " WHERE code IN (" . implode(",", $ids) . ")";
        }

        $stmt = $GLOBALS['DB']->query($query);
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
            $ids = [];

            foreach ($this->ids as $id) {
                $ids[] = "'{$id}'";
            }

            $parameters[] = "t1.country_code IN (" . implode(",", $ids) . ")";
        }

        return $parameters;
    }

    protected function getOrdersQuery($parameters){

        $query = "SELECT t1.webmaster_commission as amount, t1.id as c, t1.country_code as country_code, t1.country_code as id, t1.created, t1.status, t1.target_close, t1.status2, t1.target FROM orders as t1";

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
}

?>