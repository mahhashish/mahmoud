<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('questions', function(Blueprint $table)
		{
			//Question's ID
			$table->increments('id');
			//title of the question
			$table->string('title',400)->default('');
			//asker's id
			$table->integer('userID')->unsigned()->default(0);
			//question's details
			$table->text('question')->default('');
			//how many times it's been viewed:
			$table->integer('viewed')->unsigned()->default(0);
			//total number of votes:
			$table->integer('votes')->default(0);
			//Foreign key to match userID (asker's id) to users
			$table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
			//we will get asking time from the created_at column
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
		Schema::drop('questions');
	}

}
