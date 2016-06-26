<h2 class="page-title">Аудит</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Аудит</a>
    </li>
  </ul>
</div>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-body">
        <div class="table-container table-responsive">
          <table class="table table-striped table-bordered table-condensed" id="user-audit-table">
            <thead>
              <tr class="heading">
                <th width="7%">UID</th>
                <th width="15%">Login</th>
                <th width="10%">IP</th>
                <th width="20%">Местоположение</th>
                <th width="13%">Время</th>
                <th width="7%"></th>
              </tr> 
              <tr role="row" class="filter">
                <td>
                  <input type="text" class="form-control form-filter input-sm" name="user_id">
                </td>
                <td>
                  <input type="text" class="form-control form-filter input-sm" name="login">
                </td>
                <td>
                  <input type="text" class="form-control form-filter input-sm" name="ip"></td>
                </td>
                <td>
                  <select class="form-control form-filter input-sm" name="country_name">
                    <option value=""></option>
                    <?php foreach ($countries as $c) : ?>
                      <option value=<?php echo $c;?>><?php echo $c;?></option>
                    <?php endforeach;?>
                  </select>
                </td>
                <td></td>
                <td>
                  <button class="btn btn-sm green" id="apply-filters">Поиск</button>
                </td>
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