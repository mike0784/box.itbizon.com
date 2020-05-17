<div class="container">
    <form action="" method="post">
        <input type="hidden" name="ID" value="<?= $arResult['SHOP']['ID'] ?>">
        <label for="TITLE">Title</label>
        <input type="text" name="TITLE" value="<?= $arResult['SHOP']['TITLE'] ?>"><br />
        <label for="AMOUNT">Amount</label>
        <input type="number" name="AMOUNT" value="<?= $arResult['SHOP']['AMOUNT'] ?>"><br />
        <label for="COUNT">Count</label>
        <input type="number" name="COUNT" value="<?= $arResult['SHOP']['COUNT'] ?>"><br />
        <label for="COMMENT">Comment</label>
        <input type="text" name="COMMENT" value="<?= $arResult['SHOP']['COMMENT'] ?>">
        <input type="submit">
    </form>
    <hr />

    <?
    $filter = ["=SHOP_ID"=> $arResult['SHOP']['ID']];

    $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
    $nav->allowAllRecords(true)
        ->setPageSize(5)
        ->initFromUri();


    $carsList = \Itbizon\Meleshev\AutoTable::getList(
        array(
            "filter" => $filter,
            "count_total" => true,
            "offset" => 1,
            "limit" => 5,
        )
    );

    $nav->setRecordCount($carsList->getCount());

    while($car = $carsList->fetch())
    {
        //var_dump($car);
        $title = $car['MARK'];
        echo $title;
    }
    ?>

    <?$APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $nav,
            "SEF_MODE" => "Y",
        ),
        "bizon:meleshev.edit"
    );
    ?>

</div>
