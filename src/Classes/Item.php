<?php
namespace Gjae\MercadoPago\Classes;

use Gjae\MercadoPago\Contracts\IItem;
use MercadoPago\Item as MercadoPagoItem;

class Item  extends MercadoPagoItem implements IItem
{


    public function __construct(array $data)
    {
        $this->setTitle($data['title']);
        $this->setPrice($data['price']);
        $this->setQuantity($data['qtty']);
        $this->setId($data['id']);
        $this->setCurrency($data['currency']);
    }

    /**
     * Undocumented function
     *
     * @param string $currency
     * @return void
     */
    public function setCurrency(string $currency)
    {
        $this->currency_id = $currency;
    }


    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Undocumented function
     *
     * @param [type] $price
     * @return void
     */
    public function setPrice($price)
    {
        $this->unit_price = $price;
    }

    /**
     * Undocumented function
     *
     * @param [type] $qtty
     * @return void
     */
    public function setQuantity($qtty)
    {
        $this->quantity = $qtty;
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * Undocumented function
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    public function getQuantity() 
    {
        return $this->quantity;
    }

    public function getPrice()
    {
        return $this->unit_price;
    }

    public function getCurrency() : string
    {
        return $this->currency_id;
    }

    public function getId()
    {
        return $this->id;
    }

}