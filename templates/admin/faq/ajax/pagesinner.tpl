<tr id="details-{$new.id}" class="details">
    <td class="details" colspan="5">
        <div class="form">
            <div class="form-group">
                <label for="text{$new.id}" class="control-label">Текст страницы:</label>
                <div>
                    <textarea id="text{$new.id}" style="height:400px;width:95%" class="form-control">{$new.text}</textarea>
                </div>
            </div>
            <div class="form-group">
                <input class="btn red" type="button" value="Сохранить" onclick="SavePagesT({$new.id})" title="Сохранить" /></td>
            </div>
        </div>
    </td>
</tr>