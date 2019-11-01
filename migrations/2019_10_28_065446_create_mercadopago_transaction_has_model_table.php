<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadopagoTransactionHasModelTable extends Migration
{
    public function up()
    {
        Schema::create('mercadopago_transaction_has_model', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('model_type', 90)->default('??')->index();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('mercadopago_transaction_id')->references('id')->on('mercadopago_transactions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mercadopago_transaction_has_model');
    }
}