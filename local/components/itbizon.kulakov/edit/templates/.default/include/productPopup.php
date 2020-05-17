<br>
<div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
        <h4 class="modal-title">Добавление товара</h4>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
        <form method="POST" action="<?= $path ?>" id="editProduct">

            <div class="form-group">
                <label for="product_title">Название</label>
                <input type="text" name="product_title" id="product_title" class="form-control is-valid">
            </div>

            <div class="form-group">
                <label for="product_value">Стоимость</label>
                <input type="text" name="product_value" id="product_value" class="form-control is-valid">
            </div>

            <div class="form-group">
                <label for="product_count">Количество</label>
                <input type="text" name="product_count" id="product_count" class="form-control is-valid">
            </div>

            <div class="form-group">
                <label for="product_comment">Комментарий</label>
                <textarea name="product_comment" id="product_comment" class="form-control"></textarea>
            </div>
            <input type="hidden" name="editProduct" value="<?= $productID; ?>">
            <input type="hidden" name="invoiceID" value="<?= $invoiceID; ?>">
        </form>
    </div>
</div>