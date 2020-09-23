<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('SITE_ID', 's1');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$drivers = [
    [
        'CODE' => 'DRIVER1',
        'NAME' => 'Diego Sanches'
    ],
    [
        'CODE' => 'DRIVER2',
        'NAME' => 'Rick Rodriges'
    ],
    [
        'CODE' => 'DRIVER3',
        'NAME' => 'Morti'
    ],
    [
        'CODE' => 'DRIVER4',
        'NAME' => 'Paris Hilton'
    ],
];

$cars = [
    [
        'CODE' => '1',
        'NAME' => 'S834PG'
    ],
    [
        'CODE' => '2',
        'NAME' => 'KO461DD'
    ],
    [
        'CODE' => '3',
        'NAME' => 'SP959EC'
    ],
    [
        'CODE' => '4',
        'NAME' => 'VB2MVL'
    ],
];

$guids = [
    [
        'CODE' => 'GUID1',
        'NAME' => 'Tatyana Sovkova'
    ],
    [
        'CODE' => 'GUID2',
        'NAME' => 'Denis Savich'
    ],
    [
        'CODE' => 'GUID3',
        'NAME' => 'Angela Devis'
    ],
    [
        'CODE' => 'GUID4',
        'NAME' => 'John Lennon'
    ],
];

$data = [
    [
        'DATE'          => '01.09.2020',
        'RES_NUMBER'    => 'TESTAG200901-1',
        'DESC'          => 'Test service',
        'UNITE'         => 'UNIT1',
        'BEGIN'         => '09:25',
        'END'           => '12:00',
        'ROUTE'         => 'Some description of route 1',
        'RES_NAME'      => 'Petrov Petr 007',
        'PAX'           => 1,
        'AGENCY'        => '7Travel',
        'DRIVER_CODE'   => 'DRIVER1',
        'CAR_NUMBER'    => '',
        'GUIDE'         => '',
        'COMMENT'       => 'Some description',
        'NOTICE'        => 'Some notice',
        'DRIVER_NOTICE' => 'Im too drunk',
        'UMSATZ'        => 0,
        'INKASSO'       => 0,
        'WORKTIME'      => 0,
        'PAYMENT'       => 0,
    ],
    [
        'DATE'          => '03.09.2020',
        'RES_NUMBER'    => 'TESTAG200903-1',
        'DESC'          => 'Test service',
        'UNITE'         => 'UNIT1',
        'BEGIN'         => '11:17',
        'END'           => '14:40',
        'ROUTE'         => 'Some description of route 2',
        'RES_NAME'      => 'Testov Test',
        'PAX'           => 2,
        'AGENCY'        => '7Travel',
        'DRIVER_CODE'   => '',
        'CAR_NUMBER'    => '2',
        'GUIDE'         => 'GUID3',
        'COMMENT'       => '',
        'NOTICE'        => 'Some notice',
        'DRIVER_NOTICE' => '',
        'UMSATZ'        => 0,
        'INKASSO'       => 0,
        'WORKTIME'      => 0,
        'PAYMENT'       => 0,
    ],
    [
        'DATE'          => '03.09.2020',
        'RES_NUMBER'    => 'TESTAG200903-2',
        'DESC'          => 'Test service',
        'UNITE'         => 'UNIT1',
        'BEGIN'         => '16:30',
        'END'           => '',
        'ROUTE'         => 'Some description of route 3',
        'RES_NAME'      => 'Ivanova Darya',
        'PAX'           => 1,
        'AGENCY'        => '7Travel',
        'DRIVER_CODE'   => '',
        'CAR_NUMBER'    => '',
        'GUIDE'         => 'Denis',
        'COMMENT'       => '',
        'NOTICE'        => '',
        'DRIVER_NOTICE' => '',
        'UMSATZ'        => 0,
        'INKASSO'       => 0,
        'WORKTIME'      => 0,
        'PAYMENT'       => 0,
    ],
    [
        'DATE'          => '10.09.2020',
        'RES_NUMBER'    => 'TESTAG200910-1',
        'DESC'          => 'Test service',
        'UNITE'         => 'UNIT1',
        'BEGIN'         => '07:15',
        'END'           => '',
        'ROUTE'         => '',
        'RES_NAME'      => 'Test',
        'PAX'           => 3,
        'AGENCY'        => '7Travel',
        'DRIVER_CODE'   => 'DRIVER3',
        'CAR_NUMBER'    => '1',
        'GUIDE'         => '',
        'COMMENT'       => 'Some description',
        'NOTICE'        => '',
        'DRIVER_NOTICE' => '',
        'UMSATZ'        => 0,
        'INKASSO'       => 0,
        'WORKTIME'      => 0,
        'PAYMENT'       => 0,
    ],
];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Account: Provider</title>
</head>
<style>
    .toggle-control .edit-control {
        display: none;
    }
</style>
<body>
<div class="container-fluid">
    <header>

    </header>
    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
        <div class="btn-group" role="group" aria-label="First group">
            <a class="btn btn-primary active" href="#" role="button" >Guide & Verkehrsservice</a>
            <a class="btn btn-primary" href="#" role="button" >Information</a>
            <a class="btn btn-primary" href="#" role="button" >Managers control</a>
            <a id="btnGroupDrop1" class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Abbreviations</a>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                <a class="dropdown-item" href="#" role="button">Agency</a>
                <a class="dropdown-item" href="#" role="button">Driver/Guide</a>
                <a class="dropdown-item" href="#" role="button">Car</a>
            </div>
        </div>
        <div class="btn-group">
            <a class="btn btn-secondary" href="#" role="button">Ivanov Ivan :: Provider</a>
            <a class="btn btn-danger" href="#" role="button">Logout</a>
        </div>
    </div>
    <article class="p-1">
        <div class="content-header row">
            <div class="col col-auto">
                <h5>Guide & Verkehrsservice</h5>
            </div>
            <div class="col">
                <span class="content-menu btn-group">
                    <a class="btn btn-success" href="#" role="button">Add</a>
                    <a class="btn btn-primary" href="#" role="button">Copy</a>
                    <a class="btn btn-warning" href="#" role="button">Save</a>
                    <a class="btn btn-danger" href="#" role="button">Delete</a>
                </span>
            </div>
        </div>
        <div class="content-search">
            <form>
                <div class="row p-1">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <input class="form-control form-control-sm" type="text" placeholder="Enter text">
                            </div>
                            <div class="col">
                                <input class="form-control form-control-sm" type="date" value="2020-09-01">
                            </div>
                            <div class="col">
                                <input class="form-control form-control-sm" type="date" value="2020-09-30">
                            </div>
                            <div class="col btn-group">
                                <a class="btn btn-primary" href="#" role="button">Search</a>
                                <a class="btn btn-secondary" href="#" role="button">Yesterday</a>
                                <a class="btn btn-secondary" href="#" role="button">Today</a>
                                <a class="btn btn-secondary" href="#" role="button">Tomorrow</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">

                    </div>
                </div>
            </form>
        </div>
        <div class="content p-1">
            <table class="table table-sm table-striped table-bordered table-hover" style="font-size:12px">
                <thead class="thead-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>Date</th>
                    <th>Reservation №</th>
                    <th>Service description</th>
                    <th>Unite</th>
                    <th>Begin/End</th>
                    <th>Route</th>
                    <th>Reservation name</th>
                    <th>PAX</th>
                    <th>Agency</th>
                    <th>Driver</th>
                    <th>Car number</th>
                    <th>Guide</th>
                    <th>Comment</th>
                    <th>Notice</th>
                    <th>Driver notice</th>
                    <th>Umsaltz</th>
                    <th>Inkasso</th>
                    <th>Work time</th>
                    <th>Payment</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <? foreach($data as $index => $item) : ?>
                    <tr>
                        <td><input type="checkbox"></td></td>
                        <td><?= $item['DATE'] ?></td>
                        <td><?= $item['RES_NUMBER'] ?></td>
                        <td><?= $item['DESC'] ?></td>
                        <td><?= $item['UNITE'] ?></td>
                        <td><input class="form-control forn-control-sm" type="time" value="<?= $item['BEGIN'] ?>"><input class="form-control forn-control-sm" type="time" value="<?= $item['END'] ?>"></td>
                        <td><?= $item['ROUTE'] ?></td>
                        <td><?= $item['RES_NAME'] ?></td>
                        <td><?= $item['PAX'] ?></td>
                        <td><?= $item['AGENCY'] ?></td>
                        <td>
                            <select class="form control form-control-sm">
                                <option value=""></option>
                                <? foreach($drivers as $driver) : ?>
                                    <option value="<?= $driver['CODE'] ?>" <?= ($item['DRIVER_CODE'] == $driver['CODE']) ? 'selected' : '' ?> ><?= $driver['NAME'] ?></option>
                                <? endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form control form-control-sm">
                                <option value=""></option>
                                <? foreach($cars as $car) : ?>
                                    <option value="<?= $car['CODE'] ?>" <?= ($item['CAR_NUMBER'] == $car['CODE']) ? 'selected' : '' ?> ><?= $car['NAME'] ?></option>
                                <? endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form control form-control-sm">
                                <option value=""></option>
                                <? foreach($guids as $guid) : ?>
                                    <option value="<?= $guid['CODE'] ?>" <?= ($item['GUIDE'] == $guid['CODE']) ? 'selected' : '' ?> ><?= $guid['NAME'] ?></option>
                                <? endforeach; ?>
                            </select>
                        </td>
                        <td><textarea><?= $item['COMMENT'] ?></textarea></td>
                        <td><textarea><?= $item['NOTICE'] ?></textarea></td>
                        <td><textarea><?= $item['DRIVER_NOTICE'] ?></textarea></td>
                        <td><input class="form-control form-control-sm" type="number" value="<?= $item['UMSATZ'] ?>"></td>
                        <td><input class="form-control form-control-sm" type="number" value="<?= $item['INKASSO'] ?>"></td>
                        <td><input class="form-control form-control-sm" type="number" value="<?= $item['WORKTIME'] ?>"></td>
                        <td><input class="form-control form-control-sm" type="number" value="<?= $item['PAYMENT'] ?>"></td>
                        <td>
                            <a id="btnGroupDropItem<?= $index ?>" class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Info</a>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDropItem<?= $index ?>">
                                <a class="dropdown-item" href="#" role="button">History</a>
                                <a class="dropdown-item" href="#" role="button">Files</a>
                            </div>
                        </td>
                    </tr>
                <? endforeach; ?>
                </tbody>
                <tfoot class="thead-dark">
                <tr class="text-center">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </article>
    <footer class="p-1 text-center">
        &copy 2020 7TRAVEL
    </footer>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.view-control', function(e) {
            let block =  $(this).closest('.toggle-control');
            let viewBlock = $(this);
            let editBlock = block.find('.edit-control');
            viewBlock.hide();
            editBlock.show();
        });
        $(document).on('change', '.edit-control-input', function(e) {
            let block =  $(this).closest('.toggle-control');
            let editBlock = $(this);
            let viewBlock = block.find('.edit-control');
            viewBlock.show();
            editBlock.hide();
        });
    });
</script>
</body>
</html>
