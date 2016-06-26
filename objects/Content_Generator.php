<?php

/**
* Content_Generator
*
* Класс предназначен для создания контента на основании потока ( обьект типа Flow )
*
* @author Sorochan Elena
* @version 1.0
*/

abstract class Content_Generator {

	/**
    * @var integer $id
    * @var string $link            Свойство класса $link содержит ссылку на поток. Ссылка складывается из user_id и названия папки ( $folder )
    *
    * @var string $folder          Содержит название папки с контентом
    * @var Flow $flow              Ссылка на обьект типа Flow, на основании которого генерируется контент
    * @var string $template_link   Ссылка на шаблон
    * @var string $template_folder Ссылка на папку с шаблоном
    * @var string $offers_url      Содержить путь к папке с потоком
    * @var Smarty $smarty          Ссылка на Smarty
    * @var string $assets_folder   Путь к папке со стилями, картинками и js
    * @var string $domen           Домен на котором будут отображаться потоки
    */

	public $id;
	public $link;
	public $flow;
	public $template_link;
	public $template_folder;
	public $content_path;
	public $template_url;
	public $user_content_path;
	public $folder;
	public $smarty;
	public $assets_folder;
	public $domen;
	public $alias;
	protected $template_data;

	private static $chars = "GHIJKLMNOPQRSTUVWXYZghijklmnopqrstuvwxyz";

	/**
	* Конструктор класса
	*
	* Принимает обьект типа Flow
	*
	* @return void
	*/
	public function __construct($flow, $alias = null) {
		$this->flow = $flow;
		$this->alias = $alias;
		$this->domen = get_site_url();
		$this->user_content_path = "{$_SERVER['DOCUMENT_ROOT']}/streams/{$this->flow->getUserId()}/";
		$this->template_data = array();
		$this->smarty = new Smarty;
	}

	private function create_link(){
		if (empty($this->alias)) {
			// Создание короткой ссылки
			$encoded_fid = base_convert($this->flow->getId(), 10, 16);
			$encoded_cid = base_convert($this->id, 10, 16);
			$index = rand(0, strlen(self::$chars));
			$devider = self::$chars[$index];
			$this->folder = $encoded_fid . $encoded_cid;
			$this->link = $this->flow->getUserId() . "/" . $this->folder;
		} else {
			// Использование псевдонима
			$this->folder = $this->alias;
			$this->link = $this->flow->getUserId() . "/" . $this->alias;
		}
	}


	/**
	* Функция генерирует контент
	*
	* Определяет данные, необходимые для подключения картинок, css и js к странице.
	*
	* @return void
	**/
	public function generate() {
		$this->create_link();
		$this->content_path = $this->user_content_path . $this->folder;
		$this->createFolders();
		$this->template_link = "file:" . $this->template_folder . Content::get_link( $this->id ) . '/index.tpl';
		$this->template_data['metrics'] = $this->get_metrics();
		$this->template_data['global_url'] = "{$this->domen}/content/assets/general";
		$this->template_data['template_url'] = $this->template_url;
		$this->smarty->assign('metrics', $this->get_metrics());
		$this->saveRedirect();
		$this->saveComplete();
		$this->saveTrafficback();
	}

	protected function createFolders(){
		// Создаем каталог с user_id
		if (!file_exists($this->user_content_path)) {
			mkdir($this->user_content_path);
		}

		// Создаем каталог для контента
		if ( file_exists($this->content_path) ) {
			foreach (glob($this->content_path . '/*') as $file)
			unlink($file);
		} else {
			mkdir($this->content_path);
		}
	}

	protected function saveRedirect(){
		$output_file = fopen("{$this->content_path}/redirect.html" , "w");
		flock($output_file, LOCK_EX);
		fwrite($output_file, '<meta http-equiv="refresh" content="0; url='.$this->flow->getRedirectTrafficLink().'"/>');
		fclose($output_file);
	}

	protected function saveComplete(){
		$this->smarty->assign('data', $this->template_data);
		$output_file = fopen("{$this->content_path}/complete.html" , "w");
		flock($output_file, LOCK_EX);
		fwrite($output_file, $this->smarty->fetch("file:{$_SERVER['DOCUMENT_ROOT']}/content/complete/index.tpl"));
		fclose($output_file);
	}

	protected function saveTrafficback(){
		if ($this->flow->hasTrafficback()) {
			$output_file = fopen("{$this->content_path}/index.html" , "w");
			flock($output_file, LOCK_EX);
			fwrite($output_file, '<meta http-equiv="refresh" content="0; url='.$this->flow->getTrafficback().'"/>');
			fclose($output_file);
		} else {
			unlink("{$this->content_path}/index.html");
		}
	}

	/**
	* Сохранение сгенерированного контента
	*
	* Функция создает директорию с названием Flow::user_id и директорию для контента с названием $path.
	* В директорию $path в файл index.html записывается сгенерированный контент
	*
	* @return void
	**/
	protected function save_content($name = "index") {
		// Записываем сгенерированный контент

		$output_file = fopen($this->content_path . "/{$name}.html" , "w");

		flock($output_file, LOCK_EX);
		fwrite($output_file, $this->html);
		fclose($output_file);
	}

	/**
	* Cоздает код метрики для контента
	*
	* @return string
	*/
	protected function get_metrics() {
		$metrics= "";

		if ( $this->flow->getYandexId() ) {
			$metrics .=
'<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter' . $this->flow->getYandexId() . ' = new Ya.Metrika({id:' . $this->flow->getYandexId() . ',
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/' . $this->flow->getYandexId() . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->';
		}

		if ( $this->flow->getGoogleId() ) {
			$metrics .=
"<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', '".$this->flow->getGoogleId()."', 'auto');
  ga('send', 'pageview');
</script>";
		}

		if ( $this->flow->getMailId() ) {
			$metrics .= '
<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
var _tmr = window._tmr || (window._tmr = []);
_tmr.push({id: "' . $this->flow->getMailId() . '", type: "pageView", start: (new Date()).getTime()});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "topmailru-code");
</script><noscript><div style="position:absolute;left:-10000px;">
<img src="//top-fwz1.mail.ru/counter?id=' . $this->flow->getMailId() . ';js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
</div></noscript>
<!-- //Rating@Mail.ru counter -->';
		}

		$metrics .= "<script src='{$this->flow->getPfinderScript()}'></script>";
		return $metrics;
	}

    private function get_pfinder_script() {
        $hash    = md5( date( 'Y-m-d' ).'pathfinder'.date( 'Y-m-d' ) );
        $site_id = $this->flow->getId();
        $user_id = $this->flow->getUserId();
        $url = 'http://google.com.ua';

        $url_api = "http://umag.p-finder.org/api_connect/?action=get_script";
        if( $curl = curl_init() ) {
            curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 60 );
            curl_setopt( $curl, CURLOPT_URL, $url_api );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl, CURLOPT_POST, true );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, "hash=$hash&site_id=$site_id&user_id=$user_id&url=$url" );
            $out = curl_exec( $curl );
            curl_close( $curl );
            return $out;
        }
        return false;
    }

}

?>