<?php

class Categories {

    private $id;
    private $name;
    private $mainimg;
    private $topimg;
    private $sub_order;
    private $sub;
    private $shablon;
    private $cattext;
    private $title;
    private $heading;
    private $keywords;
    private $description;
    private $link;
    private $hidden;
    private $seo;
    private $css;
    private $type;

    const TYPE_SHOP_CATEGORY = "shop_category";
    const TYPE_OFFER_CATEGORY = "offer_category";

    public function __construct( $data = [] ){
        $this->id = (isset($data['id'])) ? $data['id'] : 0;
        $this->name = (isset($data['name'])) ? $data['name'] : '';
        $this->mainimg = (isset($data['mainimg'])) ? $data['mainimg'] : '';
        $this->topimg = (isset($data['topimg'])) ? $data['topimg'] : '';
        $this->sub_order = (isset($data['sub_order'])) ? $data['sub_order'] : 0;
        $this->sub = (isset($data['sub'])) ? $data['sub'] : 0;
        $this->shablon = (isset($data['shablon'])) ? $data['shablon'] : 0;
        $this->cattext = (isset($data['cattext'])) ? $data['cattext'] : '';
        $this->title = (isset($data['title'])) ? $data['title'] : '';
        $this->heading = (isset($data['heading'])) ? $data['heading'] : '';
        $this->keywords = (isset($data['keywords'])) ? $data['keywords'] : '';
        $this->description = (isset($data['description'])) ? $data['description'] : '';
        $this->link = (isset($data['link'])) ? $data['link'] : '';
        $this->hidden = (isset($data['hidden'])) ? $data['hidden'] : 0;
        $this->type = (isset($data['type'])) ? $data['type'] : "shop_category";
        $this->seo = (isset($data['seo'])) ? $data['seo'] : '';
        $this->css = (isset($data['css'])) ? $data['css'] : '';
    }

    public function getInstance($id){
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return new self($stmt->fetch(PDO::FETCH_ASSOC));
    }

    static public function getByLink( $link ){
        $stmt = $GLOBALS['DB']->prepare("SELECT * FROM categories WHERE link = :link");
        $stmt->bindParam(":link", $link, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return false;
        }
        return new self($stmt->fetch(PDO::FETCH_ASSOC));
    }

    static public function getByType($type) {
        $stmt = $GLOBALS['DB']->prepare("SELECT * FROM categories WHERE type = :type AND sub = 7 ORDER BY sub_order");
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);
        $stmt->execute();
        $items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new self($data);
        }
        return $items;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getMainImg(){
        return $this->mainimg;
    }

    public function getTopImg(){
        return $this->topimg;
    }

    public function getWeight(){
        return $this->sub_order;
    }

    public function getSub(){
        return $this->sub;
    }

    public function getSubOrder(){
        return $this->sub_order;
    }

    public function getShablon(){
        return $this->shablon;
    }

    public function getCattext(){
        return $this->cattext;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getHeading(){
        return $this->heading;
    }

    public function getKeywords(){
        return $this->keywords;
    }

    public function setName($param) {
        $this->name = $param;
    }

    public function setAlias($param) {
        $this->alias = $param;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getAlias(){
        return $this->link;
    }

    public function getHidden(){
        return $this->hidden;
    }

    public function getCss(){
        return $this->css;
    }

    public function getIcon(){
        return ($this->getCss() != "") ? "<i class='".$this->getCss()."'></i>" : "";
    }

    public function isHidden(){
        return (bool) $this->hidden;
    }

    public function getSeo(){
        return $this->seo;
    }

    public function getLink(){
        return get_site_url() . "/category/" . $this->getAlias();
    }

    public function getType(){
        return $this->type;
    }

    /**
    * Возвращает количество офферов в категории
    */
    public function getOffersCount(){
        $query = "SELECT count(g_id) AS total FROM goods2categories WHERE c_id = :id";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
    * Возвращает количество офферов в категории, которые видны в магазине
    */
    public function getProductsCount($country=null){
        $query = "SELECT count(g.id) AS total
                  FROM goods2categories AS gc INNER JOIN goods as g ON gc.g_id = g.id
                       INNER JOIN goods2countries AS gco ON gco.g_id = g.id
                  WHERE gc.c_id = :id";
        if (!is_null($country)) {
            $query .= " AND gco.country_code = :country";
        }
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        if (!is_null($country)) {
             $stmt->bindParam(":country", $country, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
    * Проверяет есть ли категория с таким же именем
    */
    public function nameIsAvailable() {
        $stmt = $GLOBALS['DB']->prepare("SELECT id FROM categories WHERE name = :name AND id != :id");
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->rowCount() == 0);
    }

    /**
    * Проверяет есть ли категория с такой же ссылкой
    */
    public function aliasIsAvailable() {
        $stmt = $GLOBALS['DB']->prepare("SELECT id FROM categories WHERE link = :alias AND id != :id");
        $stmt->bindParam(":link", $this->link, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->rowCount() == 0);
    }

    public function save() {
        if ( $this->id == 0 ) {
              $query = "INSERT INTO categories (name, mainimg, topimg,  sub_order, sub, shablon, cattext, title, heading, keywords, description, link, hidden, seo, css, type) VALUES (:name, :mainimg, :topimg, :sub_order, :sub, :shablon, :cattext, :title, :heading, :keywords, :description, :link, :hidden, :seo, :css, :type)";
        } else {
              $query = "UPDATE categories SET name = :name,
                                              mainimg = :mainimg,
                                              topimg = :topimg,
                                              sub_order = :sub_order,
                                              sub = :sub,
                                              shablon = :shablon,
                                              cattext = :cattext,
                                              title = :title,
                                              heading = :heading,
                                              keywords = :keywords,
                                              description = :description,
                                              link = :link,
                                              hidden = :hidden,
                                              seo = :seo,
                                              css = :css,
                                              type = :type
                                              WHERE id = :id";
        }
        $stmt = $GLOBALS['DB']->prepare($query);

        if ( $this->id != 0 ) {
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':mainimg', $this->mainimg, PDO::PARAM_STR);
        $stmt->bindParam(':topimg', $this->topimg, PDO::PARAM_STR);
        $stmt->bindParam(':sub_order', $this->sub_order, PDO::PARAM_INT);
        $stmt->bindParam(':sub', $this->sub, PDO::PARAM_INT);
        $stmt->bindParam(':shablon', $this->shablon, PDO::PARAM_STR);
        $stmt->bindParam(':cattext', $this->cattext, PDO::PARAM_STR);
        $stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindParam(':heading', $this->heading, PDO::PARAM_STR);
        $stmt->bindParam(':keywords', $this->keywords, PDO::PARAM_STR);
        $stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
        $stmt->bindParam(':link', $this->link, PDO::PARAM_STR);
        $stmt->bindParam(':hidden', $this->hidden, PDO::PARAM_INT);
        $stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
        $stmt->bindParam(':seo', $this->seo, PDO::PARAM_STR);
        $stmt->bindParam(':css', $this->css, PDO::PARAM_STR);
        $stmt->execute();

        if ( $this->id == 0 ) {
            $this->id = $GLOBALS['DB']->lastInsertId();
        }
    }

    public static function getAll() {
        $stmt = $GLOBALS['DB']->query("SELECT * FROM categories WHERE sub = 7 ORDER BY sub_order");
        $items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new self($data);
        }
        return $items;
    }

    public static function getAllVisible() {
        $stmt = $GLOBALS['DB']->query("SELECT * FROM categories WHERE sub = 7 AND hidden = 0 ORDER BY sub_order");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function getMain() {
        $query = "SELECT * FROM `categories` where link = '' LIMIT 0, 1";

        return $GLOBALS["DB"]->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    static public function delete( $id ) {
        $stmt = $GLOBALS['DB']->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    static public function selectById( $id ){
        $query = "SELECT * FROM categories WHERE id = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static public function selectByLink( $link ){
        $query = "SELECT * FROM categories WHERE link = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static public function selectBySubLink($id, $link ){
        $query = "SELECT * FROM categories WHERE sub = ? and link = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $id,
            $link
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static public function getFirst($sub){
        $query = "SELECT id FROM categories WHERE sub = ? ORDER BY sub_order, name LIMIT 0,1";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $sub
        ]);

        return $stmt->fetchColumn();
    }

    static public function getSubMenus($id){
        $query = "SELECT * FROM categories WHERE sub = ? ORDER BY sub_order, name";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $sub
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function getSubCount($id){
        $query = "SELECT COUNT(id) FROM categories WHERE sub = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $sub
        ]);

        return $stmt->fetchColumn();
    }

    static public function getAllParent($id){
        $items = [];

        for ($i=0; $i++; $id != 0) {
            $items[$i] = Categories::selectById($id);
            $id = $items[$i]['sub'];
        }
    }   

    static public function getGoodsCount($menu){
        $query = "SELECT COUNT(id) as count FROM goods WHERE id_menu = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $menu
        ]);

        return $stmt->fetchColumn();
    }
}