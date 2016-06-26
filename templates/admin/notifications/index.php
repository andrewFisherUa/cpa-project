<h2 class="page-title">Уведомления</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Уведомления</a>
    </li>
  </ul>
</div>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">Уведомления</div>
      </div>
      <div class="portlet-body">

        <div class="table-container">
          <table class="table table-striped table-bordered table-condensed table-hover" id="datatable_notifications">
          <thead>
          <tr role="row" class="heading">
            <th width="5%">
              ID
            </th>
            <th width="12%">
              Дата
            </th>            
            <th width="28%">
              Группа
            </th>
            <th width="28%">
              Сообщение
            </th>
            <th width="15%">
              Тип
            </th>
            <th width="15%">
              Статус
            </th>
            <th width="10%">
              Детали
            </th>
          </tr>
          <tr role="row" class="filter">
            <td>
              <input type="text" class="form-control form-filter input-sm" name="id">
            </td>
            <td>
              <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                <input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="От">
                <span class="input-group-btn">
                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
              <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                <input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="До">
                <span class="input-group-btn">
                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
            </td> 
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <button class="btn btn-sm green" id="apply-filters">Поиск</button>
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
  </div>
</div>
<!-- END PAGE CONTENT-->

<!-- Modal -->
<div class="modal fade" id="audit-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Детали</h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>