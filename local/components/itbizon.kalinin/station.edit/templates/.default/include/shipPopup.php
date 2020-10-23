<br>
<div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
        <h4 class="modal-title">Добавление Корабля</h4>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
        <form method="POST" action="<?= $path ?>" id="editShip">

            <div class="form-group">
                <label for="NAME">Название</label>
                <input type="text" name="NAME" id="NAME" class="form-control is-valid" required>
            </div>

            <div class="form-group">
                <label for="VALUE">Стоимость ($)</label>
                <input type="number" name="VALUE" id="VALUE" class="form-control is-valid" data-decimals="2" step=".01" required>
            </div>

            <div class="form-group">
                <label for="MATERIALS">Материалы</label>
                <input type="text" name="MATERIALS" id="MATERIALS" class="form-control is-valid" required>
            </div>

            <div class="form-group">
                <label for="IS_RELEASED">Выпущен?</label>
                <input type="checkbox" class="form-check-input" name="IS_RELEASED">
            </div>

            <div class="form-group">
                <label for="COMMENT">Комментарий</label>
                <textarea name="COMMENT" id="COMMENT" class="form-control"></textarea>
            </div>
            <input type="hidden" name="editShip" value="<?= $ShipID; ?>">
            <input type="hidden" name="STATION_ID" value="<?= $StationID; ?>">
        </form>
    </div>
</div>