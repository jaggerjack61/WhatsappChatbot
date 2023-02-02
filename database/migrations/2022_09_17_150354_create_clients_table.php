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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone_no')->unique();
            $table->string('EC')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_number')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('guest');
            $table->string('rough')->nullable();
            $table->string('handled_by')->nullable();
            $table->string('message_status')->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
