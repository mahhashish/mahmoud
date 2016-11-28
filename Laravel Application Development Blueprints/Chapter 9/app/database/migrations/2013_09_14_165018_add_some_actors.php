<?php

use Illuminate\Database\Migrations\Migration;

class AddSomeActors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$woody = Actors::create(array(
            'name' => 'Woody Allen'
        	));

		$woody->Movies()->attach(array('1','2'));

		$diane = Actors::create(array(
            'name' => ' Diane Keaton'
        	));

		$diane->Movies()->attach(array('1','2'));

		$jack = Actors::create(array(
		            'name' => 'Jack Nicholson'
		      	));

		$jack->Movies()->attach(3);

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}