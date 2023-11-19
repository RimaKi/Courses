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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->text('goalIds')->nullable();
            $table->text('objectiveIds')->nullable();
            $table->tinyInteger('quantity');
            $table->string('createdBy');
            $table->tinyInteger('succeedMark');
            $table->integer('totalMark')->default(100);
            $table->string('courseId')->nullable();
            $table->integer('unitId')->nullable();
            $table->boolean('isCompetition')->default(false);
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
        Schema::dropIfExists('exams');
    }
};
