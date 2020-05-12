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
    public function addInvoice($title,  $amount = 0, $comment = "", $user_id = 0)
    {
        global $USER;
        $creator_id = $user_id;

        if(isset($USER))
            $creator_id = $USER->GetID();

        $invoice = ItbInvoiceTable::createObject();

        $invoice->set('TITLE', $title);
        $invoice->set('CREATOR_ID', $creator_id);
        $invoice->set('AMOUNT', $amount);
        $invoice->set('COMMENT', $comment);

        $invoice->save();

        return $invoice;
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
    public function addProduct($data)
    {
        if(empty($data['invoice_id']))
            throw new Exception('Заполните обязательное поле invoice_id');

        global $USER;
        $creator_id = 0;

        if(isset($USER))
            $creator_id = $USER->GetID();
        else if(isset($data['creator_id']))
            $creator_id = $data['creator_id'];

        $invoiceID  = $data['invoice_id'];

        $product = ItbProductTable::createObject();

        $product->set('TITLE',      (isset($data['title']) ? $data['title'] : ""));
        $product->set('CREATOR_ID', $creator_id);
        $product->set('INVOICE_ID', $invoiceID);
        $product->set('VALUE',      (isset($data['value']) ? $data['value'] : 0));
        $product->set('COUNT',      (isset($data['count']) ? $data['count'] : 0));
        $product->set('COMMENT',    (isset($data['comment']) ? $data['comment'] : ""));

        $product->save();

        $invoice = ItbInvoiceTable::getByPrimary($invoiceID)
            ->fetchObject();

        if($invoice) {
            $amountInvoice = $invoice->get('AMOUNT');

            // Перерасчет суммы
            $amountInvoice += $data['COUNT'] * $data['VALUE'];

            $invoice->set('AMOUNT', $amountInvoice);

            $invoice->save();
        }

        return $product;
    }

    /**
     *
     */
    public function removeProduct($id)
    {
        $product = ItbProductTable::getByPrimary($id, [
            'select' => ['*' => 'INVOICE']
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

            $invoice->save();
        }

        $product->delete();
        return true;
    }

}