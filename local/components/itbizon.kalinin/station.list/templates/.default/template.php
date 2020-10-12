<div class="container">
    <div class="col-md-12">
        <h2 class="text-center">Список станций</h2>
        <hr>
        <form action="" method="post">
        <? foreach ($arResult['stations'] as $station): ?>
            <div class="form-group">
                <label for="station_<?= $station['ID'] ?>['ID']">Id <?=$station['ID']?></label>
                <input type="hidden" class="form-control" name="station_<?= $station['ID'] ?>['ID']" value="<?=$station['ID']?>">
            </div>
            <div class="form-group">
                <label for="station_<?= $station['ID'] ?>['TITLE']">Name</label>
                <input type="text" readonly class="form-control" name="station_<?= $station['ID'] ?>['NAME']" value="<?=$station['NAME']?>">
            </div>
            <div class="form-group">
                <label for="station_<?= $station['ID'] ?>['AMOUNT']">Amount</label>
                <input type="number" readonly class="form-control" name="station_<?= $station['ID'] ?>['AMOUNT']" value="<?=$station['AMOUNT']?>">
            </div>
            <div class="form-group">
                <label for="station_<?= $station['ID'] ?>['COUNT']">Count</label>
                <input type="number" readonly class="form-control" name="station_<?= $station['ID'] ?>['COUNT']" value="<?=$station['COUNT']?>">
            </div>
            <div class="form-group">
                <label for="station_<?= $station['ID'] ?>['COMMENT']">Comment</label>
                <input type="text" readonly class="form-control" name="station_<?= $station['ID'] ?>['COMMENT']" value="<?=$station['COMMENT']?>">
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="station_<?= $station['ID'] ?>['isDelete']">
                <label for="station_<?= $station['ID'] ?>['isDelete']">Удалить?</label>
            </div>
            <div class="form-group">
                <a class="btn btn-primary" href="edit/<?= $station['ID']?>/">Редактировать</a>
            </div>
            <hr />
        <? endforeach; ?>
            <button type="submit" class="btn btn-primary">Удалить выбранные станции</button>
        </form>
        <hr>
        <div class="mt-lg-5">
            <a class="btn btn-primary float-left" href="edit/0/">Создать станцию</a>
        </div>
    </div>
</div>
