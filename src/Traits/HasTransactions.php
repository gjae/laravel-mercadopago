<?php
namespace Gjae\MercadoPago\Traits;

trait HasTransactions
{
    public function transactions()
    {
        return $this->morphMany(\Gjae\MercadoPago\Models\ModelTransaction::class, 'model');
    }
}