<?php
namespace Gjae\MercadoPago\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MPTransaction extends Model
{
    use SoftDeletes;
    protected $table = 'mercadopago_transactions';
    protected $fillable = [
        'collection_id', 'collection_status', 'external_reference', 'payment_type', 'merchant_order_id', 'preference_id', 'site_id', 'processing_account_id', 'merchant_account_id', 
    ];
    
    public function model()
    {
        return $this->hasMany(\Gjae\MercadoPago\Models\ModelTransaction::class, 'mercadopago_transaction_id');
    }
}