{if $data.sent_by_support }
	<p>Уважаемый {$data.username}!</p>
	<p>На Ваш тикет #{$data.ticket_id} с темой "{$data.subject}" был дан ответ службой поддержки: </p>
	<p>{$data.message}</p>
	<p>Для ответа на него, пожалуйста, перейдите по этой <a href="{$admin_url}/tickets/{$data.ticket_id}">ссылке</a>.</p>
	<p>Пожалуйста, не отвечайте на данное сообщение по email - ответ не будет нами получен и обработан.</p>
	<p>С уважением,</p>
	<p>Служба поддержки Univer-Mag</p>
{else}
	<p>Пользователь {$data.username} добавил новое сообщение к тикету #{$data.ticket_id} "{$data.subject}" :</p>
	<p>{$data.message}</p>
	<p>Для ответа на него, пожалуйста, перейдите по этой <a href="{$admin_url}/tickets/{$data.ticket_id}">ссылке</a>.</p>
{/if}