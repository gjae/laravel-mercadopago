<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateMercadopagoTransactionsTable extends Migration 
{
    public function up()
    {
        Schema::create('mercadopago_transactions', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('collection_id', 130)->default('EN ESPERA')->index();
            $table->string('collection_status', 100)->default('EN ESPERA');
            $table->string('external_reference', 100)->default('S/ER')->index();
            $table->string('payment_type', 23)->default('EE');
            $table->string('merchant_order_id')->nullable();
            $table->string('preference_id')->default('SP')->index();
            $table->string('site_id', 10)->nullable();
            $table->string('processing_mode', 33)->nullable();
            $table->string('merchant_account_id', 100)->default('SMAID')->index();
            $table->decimal('amount', 12,2)->default(0.00);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mercadopago_transactions');
    }
}