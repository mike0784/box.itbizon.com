<?php

namespace Itbizon\Kulakov\Orm;

use Itbizon\Kulakov\Orm\ItbInvoiceTable;
use Itbizon\Kulakov\Orm\ItbProductTable;

class Manager
{

    /**
     * @param $data - Параметры для создания Invoice
     * @return ItbInvoiceTable
     */
    public function addInvoice($title,  $comment = "", $user_id = 0)
    {
        global $USER;
        $creator_id = $user_id;

        if(isset($USER) && $user_id == 0)
            $creator_id = $USER->GetID();

        $invoice = ItbInvoiceTable::createObject();

        $invoice->set('TITLE', $title);
        $invoice->set('CREATOR_ID', $creator_id);
        $invoice->set('COMMENT', $comment);

        $invoice->save();

        return $invoice;
    }

    /**
     * @param $id
     * @param $title
     * @param string $comment
     * @param int $user_id
     * @return bool|mixed
     */
    public function editInvoice($id, $title,  $comment = "", $user_id = 0)
    {

        if(!$id) return false;

        $data = self::getInvoice($id);

        if(!empty($data['invoice'])) {
            $invoice = $data['invoice'];

            $invoice->set('TITLE', $title);
            $invoice->set('COMMENT', $comment);

            $invoice->save();
            return $invoice;
        }


        return false;
    }

    /**
     *
     */
    public function removeInvoice($id)
    {
        $data = self::getInvoice($id);

        try {
            if(!empty($data['invoice']))
                $data['invoice']->delete();

            foreach ($data['products'] as $product)
            {
                self::removeProduct($product["ID"]);
            }

        }
        catch (Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function getInvoice($id)
    {
        $result = ItbProductTable::getList(array(
            'filter' => array('=INVOICE_ID' => $id)
        ));
        $rows = $result->fetchAll();

        return array(
            'invoice' => ItbInvoiceTable::getByPrimary($id)
                ->fetchObject(),
            'products' => $rows,
        );
    }

    /**
     *
     */
    public function getInvoiceList()
    {
        $result = ItbInvoiceTable::getList();
        return $result->fetchAll();
    }

    /**
     *
     */
    public function getProductList($invoice_id = 0)
    {
        $result = ItbProductTable::getList(array(
            'filter' => array('=INVOICE_ID' => $invoice_id)
        ));
        return $result->fetchAll();
    }

    /**
     *
     */
    public function addProduct($data)
    {
        if(empty($data['invoice_id']))
            throw new Exception('Заполните обязательное поле invoice_id');

        global $USER;
        $creator_id = 0;

        if(isset($data['creator_id']))
            $creator_id = $data['creator_id'];
        else if(isset($USER))
            $creator_id = $USER->GetID();


        $invoiceID  = $data['invoice_id'];

        $product = ItbProductTable::createObject();

        $product->set('TITLE',      (isset($data['title']) ? $data['title'] : ""));
        $product->set('CREATOR_ID', $creator_id);
        $product->set('INVOICE_ID', $invoiceID);
        $product->set('VALUE',      (isset($data['value']) ? $data['value'] : 0));
        $product->set('COUNT',      (isset($data['count']) ? $data['count'] : 0));
        $product->set('COMMENT',    (isset($data['comment']) ? $data['comment'] : ""));

        $invoice = ItbInvoiceTable::getByPrimary($invoiceID)
            ->fetchObject();

        if($invoice) {
            $amountInvoice = $invoice->get('AMOUNT');
            // Перерасчет суммы
            $amountInvoice += $data['count'] * $data['value'];

            $invoice->set('AMOUNT', $amountInvoice);

            $invoice->save();
        }

        $product->save();

        return $product;
    }

    /**
     *
     */
    public function removeProduct($id)
    {
        $product = ItbProductTable::getByPrimary($id, [
            'select' => [
                '*' => 'INVOICE',
                '*'
            ]
        ])->fetchObject();

        if(!$product) return false;

        $productCount = $product->get('COUNT');
        $productValue = $product->get('VALUE');

        $invoice = $product->get('INVOICE');

        if($invoice)
        {
            $amountInvoice = $invoice->get('AMOUNT');

            // Перерасчет суммы
            $amountInvoice -= $productCount * $productValue;

            $invoice->set('AMOUNT', $amountInvoice);
            $amountInvoice = $invoice->get('AMOUNT');

            $invoice->save();
        }
        $product->delete();

        return true;
    }

}