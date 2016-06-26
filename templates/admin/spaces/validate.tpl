<h2 class="page-title">Подтверждение сайта</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/spaces">Источники трафика</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Подтверждение сайта</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Подтверждение сайта `<span id="space-name">{$data.name}</span>`
    </div>
  </div>
  <div class="portlet-body">
    <p>Для отправки сайта на модерацию, необходимо подтвердить право собственности на добавляемый сайт.</p>
    <p>Выберите один из способов ниже:</p>
    <p class="bold">Первый способ</p>
    <p>Скопируйте указанный ниже метатег и вставьте его на главную страницу своего сайта. Он должен находиться в разделе <code>&lt;head&gt;</code> перед первым разделом <code>&lt;body&gt;</code>.</p>
    <p><code>&lt;meta name="univer-mag-site-verification" content="938d589d1dbaafd575830d5aa4efe196" /&gt;</code></p>
    <p class="bold">Второй способ</p>
    <p>Создайте пустой HTML файл с названием 938d589d1dbaafd575830d5aa4efe196.html или же загрузите этот файл отсюда и сохраните его в корневом каталоге вашегосайта {$data.url}.</p>
    <p>Таким образом, файл должен быть доступен по адресу <a href="{$data.url}/938d589d1dbaafd575830d5aa4efe196.html">{$data.url}/938d589d1dbaafd575830d5aa4efe196.html</a></p>
    <p>После выполнения требуемых условий указанных в первом или во втором способе, нажмите на кнопку ниже.</p>

    <div>
      <a href="javascript:;" class="btn blue" id="confirm-space" data-id="{$data.id}">Подтвердить</a>
    </div>
  </div>
</div>