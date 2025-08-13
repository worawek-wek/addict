<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_has_addon_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_order_id');
            $table->unsignedBigInteger('ref_option_id');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Foreign keys (optional)
            // $table->foreign('ref_order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->foreign('ref_option_id')->references('id')->on('addon_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_has_addon_options');
    }
};
