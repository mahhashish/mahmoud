<?php
use Illuminate\Database\Migrations\Migration;

class CreateTodoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	
	Schema::create('posts', function(Blueprint $table){

            $table->create();
            $table->increments("id");
            $table->string("title", 255);
            $table->enum('status', array('0', '1'))->default('0');
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
		Schema::drop("todos");
	}

}