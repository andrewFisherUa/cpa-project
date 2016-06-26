<?php /* Smarty version 2.6.22, created on 2016-06-09 17:22:01
         compiled from admin%5Coffers%5Cview.php */ ?>
<div id="page-offer-view">
  <!-- BEGIN PAGE HEADER-->
  <h3 class="page-title">
    <?php echo $this->_tpl_vars['offer']->getName(); ?>

  </h3>

  <div class="page-bar">
      <ul class="page-breadcrumb">
          <li>
            <i class="fa fa-home"></i>
            <a href="/admin">Главная</a>
            <i class="fa fa-angle-right"></i>
          </li>
          <li>
            <a href="/admin/offers/">Офферы</a>
            <i class="fa fa-angle-right"></i>
          </li>
          <li>
              <a href="#"><?php echo $this->_tpl_vars['offer']->getName(); ?>
</a>
          </li>
        </ul>

        <div class="actions btn-set pull-right">

        </div>
  </div>
  <!-- END PAGE HEADER-->

  <!-- BEGIN PAGE CONTENT-->
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        <?php echo $this->_tpl_vars['offer']->getName(); ?>

      </div>

      <div class="actions">

        <button <?php if (! $this->_tpl_vars['canBeConnected']): ?>style="display:none"<?php endif; ?> data-action="reload" class="btn default btn-sm green add-user-good" data-g_id="<?php echo $this->_tpl_vars['offer']->getId(); ?>
" data-rules="<?php echo $this->_tpl_vars['options']->get('show_rules'); ?>
"><i class="fa fa-plus"></i> Подключить</button>
        <a <?php if (! $this->_tpl_vars['is_connected'] || $this->_tpl_vars['admin']): ?>style="display:none"<?php endif; ?> href="#flows" data-toggle="tab" class="btn awesome-green btn-sm get-offer-link"><i class="icon-link"></i> Получить ссылку</a>
        <button <?php if (! $this->_tpl_vars['is_connected']): ?>style="display:none"<?php endif; ?> data-action="reload" class="btn default btn-sm default remove-user-good" data-g_id="<?php echo $this->_tpl_vars['offer']->getId(); ?>
"><i class="fa fa-times"></i> Отключить</button>
        <?php if ($this->_tpl_vars['admin']): ?>
        <a href="/admin/offers/edit/<?php echo $this->_tpl_vars['offer']->getId(); ?>
" class="btn btn-default btn-circle"><i class="fa fa-edit"></i> Редактировать</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="portlet-body">
      <div class="clearfix">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id="offer-tabs">
          <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">Информация</a></li>
          <?php if ($this->_tpl_vars['is_connected']): ?>
          <li role="presentation"><a href="#create-flow" aria-controls="create-flow" role="tab" data-toggle="tab">Создать поток</a></li>
          <li role="presentation"><a href="#flows" aria-controls="flows" role="tab" data-toggle="tab">Потоки</a></li>
          <?php endif; ?>
          <li role="presentation"><a href="#news" aria-controls="news" role="tab" data-toggle="tab">Новости</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <!-- #info tab -->
          <div role="tabpanel" class="tab-pane active" id="info">
            <div class="row" >
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-3">
                    <!-- .widget -->
                    <div class="widget prices-widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-money"></i>
                          <span class="caption-subject"> Выплаты</span>
                        </div>
                      </div>
                      <div class="widget-body">
                        <?php $_from = $this->_tpl_vars['targets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['target']):
?>
                          <h5><?php echo $this->_tpl_vars['target']['name']; ?>
</h5>
                          <ul>
                            <?php $_from = $this->_tpl_vars['target']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['value']):
?>
                            <li><span class="flag flag-<?php echo $this->_tpl_vars['code']; ?>
"></span> <?php echo $this->_tpl_vars['value']; ?>
</li>
                            <?php endforeach; endif; unset($_from); ?>
                          </ul>
                        <?php endforeach; endif; unset($_from); ?>
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-info-circle"></i>
                          <span class="caption-subject"> Инфо</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        <div class="list-row">
                          <div class="list-label">PostClick cookie:</div>
                          <div class="list-value"><?php echo $this->_tpl_vars['options']->get('postclick_cookie'); ?>
 дней</div>
                        </div>
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-folder-open"></i>
                          <span class="caption-subject"> Категория</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        <ul class="widget-list semibold cat-list">
                        <?php $_from = $this->_tpl_vars['offer']->getCategories(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
                          <li><?php echo $this->_tpl_vars['cat']['name']; ?>
</li>
                        <?php endforeach; endif; unset($_from); ?>
                        </ul>
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-globe"></i>
                          <span class="caption-subject"> Трафик</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        <?php $_from = $this->_tpl_vars['offer']->getTrafficSources(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['t']):
?>
                          <div class="list-row">
                            <div class="list-label"><?php echo $this->_tpl_vars['t']['name']; ?>
</div>
                            <div class="list-value">
                              <?php if ($this->_tpl_vars['t']['selected']): ?> <i class="fa fa-check font-green"></i><?php else: ?><i class="fa fa-times font-red"></i><?php endif; ?>
                            </div>
                          </div>
                        <?php endforeach; endif; unset($_from); ?>
                      </div>
                    </div><!-- /.widget -->

                  </div>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-8">
                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Описание</span>
                            </div>
                          </div>
                          <div class="widget-body">
                            <?php echo $this->_tpl_vars['offer']->getDescription(); ?>


                            <?php if ($this->_tpl_vars['countries']): ?>
                              <ul class="geo">
                                  <li><strong>Гео: </strong></li>
                                <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
                                  <li><span class="flag flag-<?php echo $this->_tpl_vars['c']['code']; ?>
"></span> <?php echo $this->_tpl_vars['c']['name']; ?>
</li>
                                <?php endforeach; endif; unset($_from); ?>
                              </ul>
                            <?php endif; ?>
                          </div>
                        </div><!-- /.widget -->
                      </div>
                      <div class="col-md-4">

                        <div class="offer-image thumbnail">
                          <a class="fancybox" href="<?php echo $this->_tpl_vars['offer']->getMainImagePath(); ?>
">
                            <img src="<?php echo $this->_tpl_vars['offer']->getMainImagePath(); ?>
" class="image-responsive" alt="">
                          </a>
                        </div>

                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Лендинги</span>
                            </div>
                          </div>
                          <div class="widget-body list">
                            <ul class="widget-list">
                            <?php $_from = $this->_tpl_vars['content']['landings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lp']):
?>
                              <li><a href="<?php echo $this->_tpl_vars['lp']['preview']; ?>
" target="_blank"><i class="fa fa-external-link"></i> <?php echo $this->_tpl_vars['lp']['name']; ?>
</a></li>
                            <?php endforeach; endif; unset($_from); ?>
                            </ul>
                          </div>
                        </div><!-- /.widget -->

                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Блоги</span>
                            </div>
                          </div>
                          <div class="widget-body list">
                            <ul class="widget-list">
                            <?php $_from = $this->_tpl_vars['content']['blogs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['b']):
?>
                              <li><a href="<?php echo $this->_tpl_vars['b']['preview']; ?>
" target="_blank"><i class="fa fa-external-link"></i> <?php echo $this->_tpl_vars['b']['name']; ?>
</a></li>
                            <?php endforeach; endif; unset($_from); ?>
                            </ul>
                          </div>
                        </div><!-- /.widget -->
                      </div>
                    </div>
                  </div>
                </div><!-- /.row -->
              </div>
          </div>
          </div>
          <!-- /.#info -->
           <?php if ($this->_tpl_vars['is_connected']): ?>
          <!-- #create-flow tab -->
          <div role="tabpanel" class="tab-pane" id="create-flow">
            <div class="col-md-10">

              <div class="alert alert-danger" style="display:none"></div>
              <div class="alert alert-success" style="display:none"></div>

              <div class="form-container">
                <?php echo $this->_tpl_vars['flow_form']; ?>

               </div>
            </div>
          </div>
          <!-- /.#create-flow -->
          <!-- #flows tab -->
          <div role="tabpanel" class="tab-pane" id="flows">
            <?php if (! $this->_tpl_vars['flows']): ?>
              <div class="alert alert-danger">
                Вы еще не создали ни одного потока, для получения ссылки на оффер нажмите <a href="#create-flow" data-toggle="tab">Создать поток</a>.
              </div>
            <?php endif; ?>
            <div class="table-container">
              <table class="table table-striped table-bordered table-hover" id="datatable_flows" data-uid="<?php echo $this->_tpl_vars['owner']; ?>
" data-oid="<?php echo $this->_tpl_vars['offer']->getId(); ?>
">
                <thead>
                  <tr role="row" class="heading">
                    <th width="30%">Название потока</th>
                    <th width="15%">Дата изменения</th>
                    <th width="15%">Источники</th>
                    <th width="25%">Ссылка</th>
                    <th width="15%">Действия</th>
                  </tr>
                  <tr role="row" class="filter">
                    <td>
                      <select class="form-control select2me table-filter input-sm" data-placeholder="Select..." name="flow_id">
                        <option value="0">Название потока</option>
                        <?php $_from = $this->_tpl_vars['flows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['flow']):
?>
                          <option value="<?php echo $this->_tpl_vars['flow']['f_id']; ?>
"><?php echo $this->_tpl_vars['flow']['name']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                      </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                      <!--<button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск</button>-->
                      <button class="btn btn-sm red reset-filters"><i class="fa fa-times"></i> Отмена</button>
                    </td>
                </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
          <!-- /.#flows -->
          <?php endif; ?>
          <!-- #news tab -->
          <div role="tabpanel" class="tab-pane" id="news">
            <div id="news-wrapper">
              <!--
              <?php if (! empty ( $this->_tpl_vars['offer_news']['0'] )): ?>
                <?php $_from = $this->_tpl_vars['offer_news']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                  <div class="portlet light bordered">
                    <div class="portlet-title">
                      <div class="caption">
                          <span class="type"> <?php echo $this->_tpl_vars['v']['type_icon']; ?>
 </span> <?php echo $this->_tpl_vars['v']['name']; ?>

                      </div>
                      <div class="actions">
                          <?php echo $this->_tpl_vars['v']['date']; ?>

                      </div>
                    </div>
                    <div class="portlet-body">
                      <?php echo $this->_tpl_vars['v']['text']; ?>

                    </div>
                  </div>
                <?php endforeach; endif; unset($_from); ?>
              <?php else: ?>
            -->

                <p class="note note-info"><?php echo '<?php'; ?>
 echo '1212';<?php echo '?>'; ?>
</p>
              <!--<?php endif; ?>-->
            </div>
          </div>
          <!-- /.#news -->
        </div>
      </div>
    </div>
  </div>


  <!-- END PAGE CONTENT-->

  <!-- Modal -->
  <div class="modal fade" id="rulesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Добавить оффер</h4>
        </div>
        <div class="modal-body">
          <p class="note note-success">
            Добавив данный оффер, вы автоматически подтверждаете, что согласны с нижеприведёнными правилами.
          </p>
          <div id="rules-wrap"></div>
        </div>
        <div class="modal-footer">
          <a href="javascript:;" class="btn blue" id="add-offer">Добавить</a>
          <a href="javascript:;" class="btn btn-default" data-dismiss="modal">Отмена</a>
        </div>
      </div>
    </div>
  </div>

</div>


<!-- Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="linkModalLabel">Сгенерированный поток</h4>
            </div>
            <div class="modal-body">
              <form class="form-inline" id="flow_link_wrap">
                <div class="alert alert-info text-center" style="display:none">Ссылка скопирована в буфер обмена!</div>
                <div class="form-group">
                  <label for="" class="control-label">Ссылка на поток: </label>
                </div>
                <br>
                <div class="form-group">
                  <input type="hidden" class="form-control" id="flow_link" value="">
                  <input type="text" style="min-width: 300px;" class="form-control" id="flow_full_link" readonly>
                </div>
                <a href="javascript:;" class="btn green" id="copy-link-btn">Копировать</a>
              </form>
            </div>
        </div>
    </div>
</div>