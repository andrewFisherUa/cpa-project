<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Глобальный постбек</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/flows">Потоки</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Глобальный постбек</a>
        </li>
      </ul>
</div>


<div class="portlet light">
  <div class="portlet-body">
    <div class="note note-info note-postback">
      <p>Глобальный постбек будет срабатывать для всех ваших потоков.</p>

      <p>При настроенном постбеке в каком-то из потоков, он имеет приоритет над глобальным. В такой ситуации срабатывать будет постбек, указанные в потоке.</p>

      <p>Параметр PostBack URL будет полезен, если Вам необходимо в автоматическом режиме получать информацию о статусах заказов. Например в случае если Вы ведете учет конверсий в сторонней системе статистики или отслеживаете конверсии на источниках трафика.</p>

      <p>PostBack запрос будет отправлен на указанный адрес методом GET. Для передачи параметров в запрос, Вы можете использовать макросы указанные ниже.</p>

      <p>Пример PostBack ссылки для передачи статуса заказа: <code>http://example.com/myscript.php?order={order_id}&amp;date={lead_date}&amp;status={status}</code> В момент перехода макросы <code>{order_id}</code>, <code>{lead_date}</code> и <code>{status}</code> будут заменены на соответствующие значения.</p>
    </div>

    <form action="/admin/flows/" method="post" id="postback-form">
      <div class="alert alert-danger" style="display:none"></div>
      <input type="hidden" name="user_id" value="<?=$user_id;?>">
      <div class="form-group">
        <input type="text" name="postback_url" class="form-control" value="<?=$postback->getUrl();?>">
      </div>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="send_on_create" <?php if ($postback->sendOnCreate()) echo "checked";?>> Отправлять запрос при создании заказа
        </label>
      </div>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="send_on_confirm" <?php if ($postback->sendOnConfirm()) echo "checked";?>> Отправлять запрос при подтверждении заказа
        </label>
      </div>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="send_on_cancel" <?php if ($postback->sendOnCancel()) echo "checked";?>> Отправлять запрос при отмене заказа
        </label>
      </div>

      <div>
        <button class="btn blue" id="save-postback">Сохранить</button>
        <button class="btn btn-default" id="reset-postback-form">Очистить</button>
      </div>
    </form>

    <div class="postback-table-inner">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Макрос</th>
            <th>Значение</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{lead1_time}</td>
            <td>время поступления заказов (unix timestamp)</td>
          </tr>
          <tr>
            <td>{lead2_time}</td>
            <td>время совершения целевого действия (unix timestamp)</td>
          </tr>
          <tr>
            <td>{sub1}</td>
            <td>SUBID1</td>
          </tr>
          <tr>
            <td>{sub2}</td>
            <td>SUBID2</td>
          </tr>
          <tr>
            <td>{sub3}</td>
            <td>SUBID3</td>
          </tr>
          <tr>
            <td>{order_id}</td>
            <td>номер заказа</td>
          </tr>
          <tr>
            <td>{status}</td>
            <td>статус конверсии, передаваемые параметры: "new" (в ожидании), "approved" (подтверждено), "declined" (отменено)</td>
          </tr>
          <tr>
            <td>{offer_id}</td>
            <td>ID оффера в системе</td>
          </tr>
          <tr>
            <td>{offer_name}</td>
            <td>Название оффера</td>
          </tr>
          <tr>
            <td>{link_id}</td>
            <td>ID потока в системе</td>
          </tr>
          <tr>
            <td>{link_name}</td>
            <td>Название потока в системе</td>
          </tr>
          <tr>
            <td>{webtotal}</td>
            <td>Прибыль вебмастера</td>
          </tr>
          <tr>
            <td>{currency}</td>
            <td>Валюта</td>
          </tr>
        </tbody>
      </table>
    </div>

    <a href="/admin/flows" class="btn btn-default">&larr; Назад</a>
  </div>
</div>