<h2 class="page-title">F.A.Q.</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="/admin"><i class="fa fa-home"></i></a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">F.A.Q.</a>
    </li>
  </ul>
</div>

<div class="portlet light" id="page-faq">
  <div class="portlet-body">
      <div id="faq-articles">
        <section>
          {foreach from=$faq item=f name=fi}
          <h3>{$f.rubric.name}</h3>
            {foreach from=$f.articles item=article}
              <div class="panel">
                  <div class="panel-heading">
                      <h4 class="panel-title">
                          <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion{$f.rubric.rubric_id}" href="#article-{$article.article_id}">
                      {$article.title} </a>
                      </h4>
                  </div>
                  <div id="article-{$article.article_id}" class="panel-collapse collapse">
                      <div class="panel-body">
                           {$article.content}
                      </div>
                  </div>
              </div>
              {/foreach}
          {/foreach}
        </section>
      </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" role="faqModal" aria-labelledby="faqModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="faqModalLabel">Добавить статью в раздел F.A.Q</h4>
      </div>
      <div class="modal-body">
            <div id="editor"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div>