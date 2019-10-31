<?php
namespace Gjae\MercadoPago\Classes;

use MercadoPago\Item;

use Gjae\MercadoPago\Contracts\IItem;
class MercadoPagoItems
{
    private $items = null;

    public function __construct()
    {
        $this->items = \collect();
    }


    public function add(IItem $item)
    {
        $this->items->push($item);
    }

    public function getAllItems()
    {
        return $this->items;
    }

    public function asArray()
    {
        $items = [];
        $this->items->each(function($item) use(&$items){
            array_push($items, $item);
        });

        return $items;
    }

    public function removeByTitle( string $title = "")
    {
       $aux = $this->items->filter(function($item) use(&$title){
            return $item->getTitle() != $title;
        });

        $this->items = $aux;

        return $this;
    }

    public function removeByItemId( string $item_id )
    {
        $aux = $this->items->filter(function($item) use(&$item_id){
            return $item->getId() != $item_id;
        });

        $this->items = $aux;

        return $this;
    }

    public function getById( string $item_id )
    {
        $item = $this->items->search(function($item) use(&$item_id){
            return $item->getId() == $item_id;
        });

        return $this->items[$item];
    }

    public function getItemByTitle( string $title )
    {
        $item = $this->items->search(function($item) use(&$title){
            return $item->getTitle() == $title;
        });

        return $this->items[$item];
    }

    public function clear()
    {
        $this->items = collect();
    }
}