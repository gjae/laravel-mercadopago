<?php
namespace Gjae\MercadoPago\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelTransaction extends Model
{
    protected $table = 'mercadopago_transaction_has_model';
    protected $fillable = ['mercadopago_transaction_id'];

    public function transaction()
    {
        return $this->belongsTo(\Gjae\MercadoPago\Models\MPTransaction::class, 'mercadopago_transaction_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}