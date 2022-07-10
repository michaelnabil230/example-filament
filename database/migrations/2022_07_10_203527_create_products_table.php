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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->longText('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->decimal('price', 10, 2)->nullable();

            $table->decimal('weight_value', 10, 2)->nullable()->default(0.00)->unsigned();
            $table->string('weight_unit')->default('kg');
            $table->decimal('height_value', 10, 2)->nullable()->default(0.00)->unsigned();
            $table->string('height_unit')->default('cm');
            $table->decimal('width_value', 10, 2)->nullable()->default(0.00)->unsigned();
            $table->string('width_unit')->default('cm');
            $table->decimal('depth_value', 10, 2)->nullable()->default(0.00)->unsigned();
            $table->string('depth_unit')->default('cm');
            $table->decimal('volume_value', 10, 2)->nullable()->default(0.00)->unsigned();
            $table->string('volume_unit')->default('l');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
