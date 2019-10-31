<?php

namespace Gjae\MercadoPago\Contracts;


interface IItem{

    public function getTitle() : string;

    public function getQuantity();

    public function getPrice();

    public function getCurrency(): string;

    public function getId();

    public function toArray();

}