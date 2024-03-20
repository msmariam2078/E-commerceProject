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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address1');
            $table->string('address2');
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
            $table->enum('type',['shipping','billing']);
            $table->string("country_code");
            $table->foreign("country_code")->references("code")->on("countries")->onDelete("CASCADE");
            $table->unsignedBigInteger("state_id");
            $table->foreign("state_id")->references("id")->on("states")->onDelete("CASCADE");
 
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
        Schema::dropIfExists('user_addresses');
    }
};
