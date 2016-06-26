<div style="z-index:1000" id="divcal"></div>
<div id="messagediv" class="alertbox"></div>

{if $admin}
    <form name="fdel" method="post"><input type="hidden" id="delid" name="delid" value="0" /></form>
    <div class="body">

    <h2 class="page-title">Настройки партнера</h2>
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <a href="/admin"><i class="fa fa-home"></i></a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Настройки партнера</a>
        </li>
      </ul>
    </div>

    <div class="well-sm well">
        <form action="" method="post" class="form form-inline">
            <div class="form-body">
                <div class="form-group">
                    <label for="bemail" class="control-label">Email службы поддержки: </label>
                    <input type="text" id="bemail" name="bemail" value="{$props.email}" class="form-control">
                </div>
                <input value="Сохранить" class="btn blue" onclick="changeSupportEmail()">
            </div>
        </form>
    </div>


    <div class="table-toolbar">
        <a href="javascript:void(0)" onclick="addPartners()" class="btn green"><i class="fa fa-plus"></i> ДОБАВИТЬ ДОМЕН</a>
    </div>
 {/if}

<table class="table table-hover dataTable" id="partners-table">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>Домен</th>
            <th>Балланс</th>
            <th>Статус</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$vips item=vip name=vi}
        <tr id="row-{$vip.user_id}">
            <td><span class="row-details row-details-close" onclick="getPartnersInfo({$vip.user_id})"></span></td>
            <td>{$smarty.foreach.vi.iteration}</td>
            <td>
                {if $admin}
                    <input type="text" id="inpcat{$vip.user_id}" value="{$vip.domen}" class="inp1 form-control" onkeydown="onChInp1({$vip.user_id})" />
                {else}
                    Настройки домена {$vip.domen}.univer-mag.com
                {/if}
            </td>
            <td>
                {if $vip.askb==1&&$admin}
                    <div style="float:left; margin-right:5px;"><a href="javascript:UpdBallance({$vip.user_id})" style="font-size:12px;color:red; font-weight:900">{$vip.ballance} / {$vip.fullballance}</a></div>
                {else}
                    <div style="float:left; margin-right:5px;font-size:12px;">{$vip.ballance} / {$vip.fullballance}</div>
                {/if}
            </td>
            <td>
                {$vip.statusText}
            </td>
            <td class="row-actions">
            {if $admin}
              <span id="ims{$vip.user_id}" class="glyphicon glyphicon-floppy-disk" onclick="SavePartners({$vip.user_id})" style="visibility: hidden"></span>
              <span class="glyphicon glyphicon-edit" onclick="getPartnersInfo({$vip.user_id})"></span>
              <span class="glyphicon glyphicon-trash" onclick="DelPartners({$vip.user_id})"></span>
            {/if}
            <div class="items" id="cat{$vip.user_id}" style="display:none;"></div>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>

{if $vipsc!=0}
<script type="text/javascript">
     getPartnersInfo({$vipsc});
</script>
{/if}





