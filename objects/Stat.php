<?php

abstract class Stat {

    protected $user_id;
    protected $filters;
    protected $data;
    protected $range;

    protected $items = [];
    protected $ids = [];

    const GROUP_BY_DATE = "date"; 
    const GROUP_BY_OFFER = "offer"; 
    const GROUP_BY_STREAM = "stream"; 
    const GROUP_BY_SOURCE = "source"; 
    const GROUP_BY_LANDING = "landing"; 
    const GROUP_BY_BLOG = "blog"; 
    const GROUP_BY_SUBID1 = "subid1"; 
    const GROUP_BY_SUBID2 = "subid2"; 
    const GROUP_BY_SUBID3 = "subid3"; 
    const GROUP_BY_SUBID4 = "subid4"; 
    const GROUP_BY_SUBID5 = "subid5"; 
    const GROUP_BY_WEBMASTER = "webmaster"; 
    const GROUP_BY_COUNTRY = "country";
    const GROUP_BY_API_STREAM = "api-stream";
    const GROUP_BY_REFERER = "referer";

    public static $trash_status = [5, 16, 17, 18, 23, 24];

    /*

    18 Тест
    13 Отказ
    24 Сервис
    15 Недозвон
    14 Перезаказ
    17 Ошибочные данные
    23 Некорректные данные
    16 Некорректный телефон
    21 Доставка невозможна
    ---
    и наверное (нужно проверять)
    ---
    5 Повтор

    */

    protected function __construct($user_id, $ids = []){
        $this->user_id = $user_id;
        $this->ids = $ids;
        $this->filters = [];
    }

    abstract protected function fetchItems();
    abstract protected function getStatQuery();

    // Задание интервала вывода
    public function setRange($range = []){
        if (empty($range)) {
            // По умолчанию статистика отображается за текущий день
            $this->range['from'] = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
            $this->range['to'] = $this->range['from'] + 86400;
        } else {
            $this->range = $range;
        }
    }

    public function applyFilters($filters){

        if (!empty($filters['subid'])) {
          for ($i=1; $i<6; $i++) {
            if (!empty($filters['subid']['subid'.$i])) {
              foreach ($filters['subid']['subid'.$i] as &$v) {
                $v = "'" . $v . "'";
              }
            }
          }
        }

        if (!empty($filters['country'])) {
            foreach ($filters['country'] as &$v) {
                $v = "'" . $v . "'";
            }
        }

        $this->filters = $filters;
    }

    public function fetch(){
        $this->fetchItems();
        $this->fetchStat();
        $this->fetchOrders();

        return $this->getValues();
    }

    protected function addItem($item){
        $this->data[$item['id']] = [
            "name" => $item['name'],
            "all" => 0,
            "unique" => 0,
            "delivered_percent" => 0,
            "count" => [
                "waiting" => 0, "approved" => 0, "canceled" => 0, "trash" => 0, "total" => 0, "delivered" => 0
            ],
            "amount" => [
                "waiting" => 0, "approved" => 0, "canceled" => 0
            ],
            "k" => [
                "epc" => 0, "crs" => 0, "approve" => 0
            ]
        ];
    }

    protected function fetchStat(){        

        if (!empty($this->filters["order_type"]) && !in_array("stream", $this->filters["order_type"])) {
            return false;
        }

        $query = $this->getStatQuery();

        $stmt = $GLOBALS['DB']->prepare($query);

        $stmt->bindParam(":from_time", $this->range['from'], PDO::PARAM_INT);
        $stmt->bindParam(":to_time", $this->range['to'], PDO::PARAM_INT);
        if ($this->user_id > 0) {
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        }

        $r = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($this->data[$row['id']])) {
                $this->data[$row['id']]['unique'] = $row['unique'];
                $this->data[$row['id']]['all'] = $row['all'];
            }
        }
    }

    // Применение фильтров к запросу
    protected function addStatFilters($query) {
        $cond = [];

        // офферы
        if (!empty($this->filters['webmaster'])) {
            $cond[] = "t2.user_id IN ( " . implode(",", $this->filters['webmaster']) . ")";
        }

        // потоки
        if (!empty($this->filters['offer'])) {
            $cond[] = "t2.offer_id IN (" . implode(",", $this->filters['offer']) . ")";
        }

        // потоки
        if (!empty($this->filters['stream'])) {
            $cond[] = "t2.stream_id IN (" . implode(",", $this->filters['stream']) . ")";
        }

        // лендинги
        if (!empty($this->filters['landing'])) {
            $cond[] = "t2.landing_id IN (" . implode(",", $this->filters['landing']) . ")";
        }

        // блоги
        if (!empty($this->filters['blog'])) {
            $cond[] = "t2.blog_id IN (" . implode(",", $this->filters['blog']) . ")";
        }

        // source_id
        if (!empty($this->filters['source'])) {
            $cond[] = "t2.traffic_source_id IN (" . implode(",", $this->filters['source']) . ")";
        }

        if (!empty($cond)) {
            $query .= " AND " . implode(" AND ", $cond);
        }        

        return $query;
    }

    protected function getOrdersQueryParameters(){
        $parameters = [
            "t1.created >= :from_time",
            "t1.created <= :to_time",
        ];

        if ($this->user_id > 0) {
            $parameters[] = "t1.user_id = :user_id";
        }

        if (!empty($this->filters['country'])) {
            $parameters[] = "t1.country_code IN ( " . implode(",", $this->filters['country']) . ")";
        }

        if (!empty($this->filters['webmaster'])) {
            $parameters[] = "t1.user_id IN ( " . implode(",", $this->filters['webmaster']) . ")";
        }

        if (!empty($this->filters['offer'])) {
            $parameters[] = "t2.offer_id IN (" . implode(",", $this->filters['offer']) . ")";
        }

        // потоки
        if (!empty($this->filters['stream'])) {
            $parameters[] = "t1.source_id IN (" . implode(",", $this->filters['stream']) . ")";
        }

        // блоги
        if (!empty($this->filters['blog'])) {
            $parameters[] = "t2.blog_id IN (" . implode(",", $this->filters['blog']) . ")";
        }

        // лендинги
        if (!empty($this->filters['landing'])) {
            $parameters[] = "t2.landing_id IN (" . implode(",", $this->filters['landing']) . ")";
        }

        // source_id
        if (!empty($this->filters['source'])) {
            $parameters[] = "t3.traffic_source_id IN (" . implode(",", $this->filters['source']) . ")";
        }

        // order_type - тип заказа (api / stream)
        if (!empty($this->filters['order_type'])) {

            $a = [];
            
            foreach ($this->filters['order_type'] as $v) {
                $a[] = "'" . $v . "'";
            }

            $parameters[] = "t1.source IN (" . implode(",", $a) . ")";
        }

        if (!empty($this->filters['subid'])) {
            // SUBID
            for ($i=1;$i<6;$i++) {
                if (!empty($this->filters['subid']['subid'.$i])) {
                    $parameters[] = "os.subid{$i} IN (" . implode(",", $this->filters['subid']['subid'.$i]) . ")";
                }
            }
        }

        return $parameters;
    }


    protected function getType($order){
        $s = $order['status'];
        $s2 = $order['status2'];
        $tc = $order['target_close'];
        $t = $order['target'];

        if ($s == Order::STATUS_PROCESSING) {
            return "waiting";
        }

        if ($s == Order::STATUS_DELIVERED || $s == Order::STATUS_CONFIRMED) {
            return ($tc == 1) ? "approved" : "waiting";
        }

        if ($s == Order::STATUS_RETURN && $t == 1 && $tc == 1) {
            return "approved";
        }

        if ($s == Order::STATUS_CANCELED || ($s == Order::STATUS_RETURN && $t != 1)) {
            return (in_array($s2, self::$trash_status)) ? "trash" : "canceled";
        }
    }

    protected function fetchOrdersQuery(){
        $query = $this->getOrdersQuery();
        $parameters = $this->getOrdersQueryParameters();

        if (!empty($parameters)) {
            $query .= " WHERE " . implode(" AND ", $parameters);
        }        

        $stmt = $GLOBALS['DB']->prepare($query);

        $stmt->bindParam(":from_time", $this->range['from'], PDO::PARAM_INT);
        $stmt->bindParam(":to_time", $this->range['to'], PDO::PARAM_INT);

        if ($this->user_id > 0) {
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Функция считает количество заказов и зароботок вебмастера
    protected function fetchOrders(){

        $data = $this->fetchOrdersQuery();

        foreach ($data as $item) {

            $id = $item['id'];

            if (!isset($this->data[$id])) {
                continue;
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

    private function _convert(){
        foreach ($this->data["_convert"] as $id => $item) {
            foreach ($item as $currency => $item2) {
                foreach ($item2 as $type=>$amount) {

                    if (!isset($this->data[$id]['amount'][$type])) {
                        $this->data[$id]['amount'][$type] = 0;
                    }

                    if ($amount > 0) {
                        $a = Converter::getConvert($currency, "RUB", $amount);

                        $this->data[$id]['amount'][$type] += $a['amount'];
                        $this->data[$id]["amount"]['total'] += $a['amount'];
                    }
                }
            }
        }

        unset($this->data["_convert"]);
    }

    private function getValues(){

        $this->_convert();

        foreach ($this->data as &$item) {
            $item['amount']['approved'] = round($item['amount']['approved']);
            $item['amount']['waiting'] = round($item['amount']['waiting']);
            $item['amount']['canceled'] = round($item['amount']['canceled']);

            $item['delivered_percent'] = round(($item['count']['delivered']/$item['count']['approved'])*100);

            $item['k']['epc'] = ( $item['unique'] == 0 ) ? 0 : $item['amount']['approved'] / $item['unique'];
            $item['k']['crs'] = ( $item['unique'] == 0 ) ? 0 : ($item['count']['total'] / $item['unique'])*100;
            $item['k']['approve'] = ( $item['count']['total'] == 0 ) ? 0 : ($item['count']['approved'] / $item['count']['total']) * 100;
            $item['k']['epc'] = round($item['k']['epc'], 2);
            $item['k']['crs'] = round($item['k']['crs'], 2);
            $item['k']['approve'] = ceil($item['k']['approve']);
        }

        return $this->data;
    }

    public static function get($user_id, $group_by = "date", $ids = []){
        switch ($group_by) {
            case self::GROUP_BY_DATE  : return new Date_Stat($user_id);
            case self::GROUP_BY_OFFER  : return new Offer_Stat($user_id, $ids);
            case self::GROUP_BY_STREAM  : return new Stream_Stat($user_id, $ids);
            case self::GROUP_BY_LANDING  : return new Landing_Stat($user_id, $ids);
            case self::GROUP_BY_SOURCE  : return new Source_Stat($user_id, $ids);
            case self::GROUP_BY_BLOG  : return new Blog_Stat($user_id, $ids);
            case self::GROUP_BY_SUBID1  : return new Subid_Stat($user_id, "subid1", $ids);
            case self::GROUP_BY_SUBID2  : return new Subid_Stat($user_id, "subid2", $ids);
            case self::GROUP_BY_SUBID3  : return new Subid_Stat($user_id, "subid3", $ids);
            case self::GROUP_BY_SUBID4  : return new Subid_Stat($user_id, "subid4", $ids);
            case self::GROUP_BY_SUBID5  : return new Subid_Stat($user_id, "subid5", $ids);
            case self::GROUP_BY_WEBMASTER  : return new Webmaster_Stat($user_id, $ids);
            case self::GROUP_BY_COUNTRY  : return new Country_Stat($user_id, $ids);
            case self::GROUP_BY_API_STREAM  : return new Api_Stream_Stat($user_id, $ids);
            case self::GROUP_BY_REFERER  : return new Referer_Stat($user_id, $ids);
        }
    }

}

?>