<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->string('uniqueId')->primary()->unique();
            $table->string('question')->nullable();
            $table->string('questionPhoto')->nullable();
            $table->integer('goalId')->nullable();
            $table->integer('objectiveId')->nullable();
            $table->string('createdBy');
            $table->string('courseId')->nullable();
            $table->tinyInteger('level');
            $table->boolean('isEssential')->default(false);
            $table->integer('duration');//sec
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
        Schema::dropIfExists('questions');
    }
}
