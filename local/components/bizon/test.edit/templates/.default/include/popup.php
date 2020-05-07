<br>
<div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
        <h4 class="modal-title">Создать штраф или бонус</h4>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
        <form method="POST" action="<?= $path ?>" id="createFines">
            <input type="hidden" name="ID" value="<?= $fine->getId() ?>">
            <div class="form-group">
                <label for="TITLE">Тайтл</label>
                <input type="text" name="TITLE" id="TITLE" class="form-control is-valid"
                       value="<?= $fine->getTitle() ?>">
            </div>

            <div class="form-group">
                <label for="VALUE">Размер штрафа или бонуса</label>
                <input type="number" step="0.01" name="VALUE" id="VALUE"
                       class="form-control is-valid"
                       value="<?= $fine->getValue() ?>"
                >
            </div>

            <div class="form-group">
                <label for="TARGET_ID">На кого</label>
                <select name="TARGET_ID" id="TARGET_ID"
                        class="form-control is-valid">
                    <? foreach ($users as $key => $user): ?>
                        <? if ($user['ID'] == $fine->getTarget()->getId()): ?>
                            <option selected value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
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
                    <? foreach ($users as $key => $user): ?>
                        <? if ($user['ID'] == $fine->getCreator()->getId()): ?>
                            <option selected value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
                        <? else: ?>
                            <option value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
                        <? endif; ?>
                    <? endforeach; ?>
                </select>
            </div>

            <div class="form - group">
                <label for="COMMENT">Комментарий</label>
                <textarea name="COMMENT" id="COMMENT"
                          class="form-control is-valid"><?= $fine->getComment() ?></textarea>
            </div>
        </form>
    </div>
</div>