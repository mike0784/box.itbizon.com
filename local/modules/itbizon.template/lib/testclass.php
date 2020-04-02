<?php

namespace Itbizon\Template;

use Bitrix\Main\UserTable;
use Itbizon\Template\SystemFines\Model\FinesTable;


class TestClass
{
    const FINE_FIELDS = [
        'title',
        'value',
        'comment',
        'target_id',
        'creator_id'
    ];

    /**
     *
     */
    public static function test()
    {
        echo __CLASS__;
    }

    public function createFine(array $data)
    {
        $values = $this->convertArrayValues($data);
        $fine = SystemFines\Model\FinesTable::add($values);

        if (!$fine->isSuccess()) {
            return $this->convertErrorArray($fine);
        }
        return $fine->getObject();
    }

    public function updateFine(int $id, array $data)
    {
        $values = $this->convertArrayValues($data);
        $fine = FinesTable::update($id, $values);

        if (!$fine->isSuccess()) {
            return $this->convertErrorArray($fine);
        }
        return $fine->getObject();
    }

    public function deleteById(int $id)
    {
        $fine = FinesTable::getByPrimary($id)->fetchObject();
        if ($fine) {
            $fine->delete();
            return true;
        }
        return false;
    }

    public function convertArrayValues(array $values)
    {
        $data = [];

        foreach ($values as $key => $value) {
            $data[strtoupper($key)] = $value;
        }

        return $data;
    }

    public function getOptionForSelect($userId = null)
    {
        $users = UserTable::getList();

        $options = '';
        foreach ($users as $user) {
            if ($userId && $userId == $user['ID']) {
                $options .= '<option selected value="' . $user['ID'] . '">' . $user['NAME'] . '</option>';
            } else {
                $options .= '<option value="' . $user['ID'] . '">' . $user['NAME'] . '</option>';
            }
        }
        return $options;
    }

    public function getFormFine($errors, $fine)
    {
        $errorsArray = $this->getArrayErrors($errors);
        $fineId = !empty($fine) ? $fine['ID'] : 0;
        $updateData = $this->getUpdateData($fine);
        $optionsForCreator = $this->getOptionForSelect($updateData['creator_id']);
        $optionsForTarget = $this->getOptionForSelect($updateData['target_id']);

        $html = '<div class="col-12">
            <form method="post">
            <input type="hidden" name="id" value="' . $fineId . '">
                <div class="form-group">
                    <label for="title">Тайтл</label>
                    <input type="text" name="title" id="title" class="form-control  
                    ' . $errorsArray['title']['class'] . '" 
            value="' . $updateData['title'] . '">
                    <div class="invalid-feedback">' . $errorsArray['title']['message'] . '</div>
                </div>
                <div class="form-group">
                    <label for="value">Размер штрафа или бонуса</label>
                    <input type="number" step="0.01" name="value" id="value" class="form-control 
                    ' . $errorsArray['value']['class'] . '" value="' . $updateData['value'] . '">
                    <div class="invalid-feedback">' . $errorsArray['value']['message'] . '</div>
                </div>
                <div class="form-group">
                    <label for="target_id">На кого</label>
                    <select name="target_id" id="target_id" 
                    class="form-control ' . $errorsArray['target_id']['class'] . '">
                        ' . $optionsForTarget . '
                    </select>
                    <div class="invalid - feedback">' . $errorsArray['target_id']['message'] . '</div>
                </div>
                <div class="form-group">
                    <label for="creator_id">Кто</label>
                    <select name="creator_id" id="creator_id"
                     class="form-control ' . $errorsArray['creator_id']['class'] . '">
                      ' . $optionsForCreator . '
                    </select>
                    <div class="invalid-feedback">' . $errorsArray['creator_id']['message'] . '</div>
                </div>
                <div class="form - group">
                    <label for="comment">Комментарий</label>
                    <textarea name="comment" id="comment" 
                    class="form-control ' . $errorsArray['comment']['class'] . '">' . $updateData['comment'] . '</textarea>
                      <div class="invalid-feedback">' . $errorsArray['comment']['message'] . '</div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>';

        return $html;
    }

    public function getTableFines()
    {
        $fines = \Itbizon\Template\SystemFines\Model\FinesTable::getList();
        $html = '
        <table class="table table-dark">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Размер</th>
            <th scope="col">Дата создания</th>
            <th scope="col">На кого</th>
            <th scope="col">Кто</th>
            <th scope="col">Действие</th>
        </tr>
        </thead>
        <tbody>';
        foreach ($fines as $fine) {
            $targetUser = UserTable::getByPrimary($fine['TARGET_ID'])->fetchObject();
            $creatorUser = UserTable::getByPrimary($fine['CREATOR_ID'])->fetchObject();

            $html .= '<tr>';
            $html .= '<th scope="row">' . $fine['ID'] . '</th>';
            $html .= '<td>' . $fine['TITLE'] . '</td>';
            $html .= '<td>' . $fine['VALUE'] . '</td>';
            $html .= '<td>' . $fine['DATE_CREATE'] . '</td>';
            $html .= '<td>' . $targetUser->getName()  . '</td>';
            $html .= '<td>' . $creatorUser->getName() . '</td>';
            $html .= '<td><a href="index.php?update=1&id=' . $fine['ID'] . '">Редактировать</a></td>';
            $html .= '<td><a href="index.php?id=' . $fine['ID'] . '">Удалить</a></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    public function getArrayErrors($errors)
    {
        $errorsArray = self::FINE_FIELDS;

        foreach ($errorsArray as $field) {
            if (key_exists(strtoupper($field), $errors)) {
                $errorsArray[$field] = [
                    'message' => $errors[strtoupper($field)],
                    'class' => 'is-invalid'
                ];
            } else {
                $errorsArray[$field] = [
                    'message' => '',
                    'class' => 'is-valid'
                ];
            }
        }
        return $errorsArray;
    }

    public function getUpdateData($fine)
    {
        $fields = self::FINE_FIELDS;
        $data = [];
        if ($fine) {
            foreach ($fine as $key => $field) {
                if ($key === 'VALUE') {
                    $data[strtolower($key)] = $field / 100;
                } else {
                    $data[strtolower($key)] = $field;
                }
            }
        } else {
            foreach ($fields as $field) {
                $data[$field] = '';
            }
        }

        return $data;
    }

    public function convertErrorArray($fine)
    {
        $errors = [];
        foreach ($fine->getErrors() as $error) {
            $errors[$error->getField()->getName()] = $error->getMessage();
        }
        return $errors;
    }
}