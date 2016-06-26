<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Блоги</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Блоги</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- START NOTIFICATIONS -->
{if $message}
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-{$message.class_name}">
        {$message.text}
      </div>
    </div>
  </div>
{/if}
<!-- END NOTIFICATIONS -->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Блоги
    </div>
    <div class="actions">
      <a href="/admin/groups" class="btn blue-hoki">Группы</a>
      <a href="/admin/landings" class="btn green-meadow">Лендинги</a>
      <a href="/admin/blogs/new/" target="_blank" class="btn yellow-crusta add-item"><i class="fa fa-plus"></i> Новый блог</a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container">
      <table class="table table-striped table-bordered table-hover" id="datatable_content" data-content-type="blogs">
      <thead>
        <tr role="row" class="heading">
          <th width="7%">
             #
          </th>
          <th width="30%">
             Название
          </th>
          <th width="23%">
             Лендинг
          </th>
          <th width="20%">
            Папка
          </th>
          <th width="10%">
            Превью
          </th>
          <th width="10%">
            Действия
          </th>
        </tr>
        <tr role="row" class="filter">
          <td>
            <input type="text" class="form-control form-filter input-sm" name="id" placeholder="ID">
          </td>
          <td>
            <select class="form-control form-filter select2me" name="name">
              <option value="-1">Название</option>
              {foreach from=$content item=a}
                <option value="{$a.name}">{$a.name}</option>
              {/foreach}
            </select>
          </td>   
          <td>
            <select class="form-control form-filter select2me" name="landing">
              <option value="-1">Лендинг</option>
              {foreach from=$landings item=a}
                <option value="{$a.c_id}">{$a.name}</option>
              {/foreach}
            </select>
          </td> 
          <td>
            <select class="form-control form-filter select2me" name="link">
              <option value="-1">Папка</option>
              {foreach from=$content item=a}
                <option value="{$a.link}">{$a.link}</option>
              {/foreach}
            </select>
          </td> 
          <td></td>      
          <td rowspan="1" colspan="1">
            <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> Отмена</button>
          </td>
        </tr>
      </thead>
      <tbody>
      </tbody>
      </table>
    </div>
  </div>
</div>
<!-- End: life time stats -->