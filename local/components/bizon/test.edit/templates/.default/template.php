<div class="container">
    <div class="col-md-8">
        <ul class="list-group">
            <li class="list-group-item">TITLE: <?= $arResult['FINE']->getTitle() ?></li>
            <li class="list-group-item">VALUE: <?= $arResult['FINE']->getValue() ?></li>
            <li class="list-group-item">TARGET: <?= $arResult['FINE']->getTarget()->getName() ?></li>
            <li class="list-group-item">CREATOR: <?= $arResult['FINE']->getCreator()->getName() ?></li>
            <li class="list-group-item">COMMENT: <?= $arResult['FINE']->getComment() ?></li>
        </ul>
        <br>
        <button type="button" class="btn btn-primary" id="showPopup" data-toggle="modal" data-target="#popup">
            Редактировать
        </button>
        <a href="/local/test" class="btn btn-primary float-right">Назад</a>
    </div>
</div>

<div class="modal" id="popup">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Создать штраф или бонус</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" action="<?= $arResult['PATH'] ?>" id="editFine">
                    <input type="hidden" name="ID" value="<?= $arResult['FINE']->getId() ?>">

                    <div class="form-group">
                        <label for="TITLE">Тайтл</label>
                        <input type="text" name="TITLE" id="TITLE" class="form-control is-valid"
                               value="<?= $arResult['FINE']->getTitle() ?>">
                    </div>

                    <div class="form-group">
                        <label for="VALUE">Размер штрафа или бонуса</label>
                        <input type="number" step="0.01" name="VALUE" id="VALUE" class="form-control is-valid"
                               value="<?= $arResult['FINE']->getValue()/100 ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="TARGET_ID">На кого</label>
                        <select name="TARGET_ID" id="TARGET_ID"
                                class="form-control is-valid">
                            <? foreach ($arResult['USERS'] as $key => $user): ?>
                                <? if ($user['ID'] == $arResult['FINE']->getTarget()->getId()): ?>
                                    <option value="<?= $user['ID'] ?>" selected><?= $user['NAME'] ?></option>
                                <? else: ?>
                                    <option value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
                                <? endif; ?>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="CREATOR_ID">Кто</label>
                        <select name="CREATOR_ID" id="CREATOR_ID"
                                class="form-control is-valid">
                            <? foreach ($arResult['USERS'] as $key => $user): ?>
                                <? if ($user['ID'] == $arResult['FINE']->getCreator()->getId()): ?>
                                    <option value="<?= $user['ID'] ?>" selected><?= $user['NAME'] ?></option>
                                <? else: ?>
                                    <option value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
                                <? endif; ?>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="form - group">
                        <label for="COMMENT">Комментарий</label>
                        <textarea name="COMMENT" id="COMMENT"
                                  class="form-control is-valid"><?= $arResult['FINE']->getComment() ?></textarea>
                    </div>
                    <br>

                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>