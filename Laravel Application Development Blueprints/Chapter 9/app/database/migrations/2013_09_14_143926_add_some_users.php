<?php

use Illuminate\Database\Migrations\Migration;

class AddSomeUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		User::create(array(
            'email' => 'john@gmail.com',
            'password' => Hash::make('johnspassword'),
        ));

		User::create(array(
            'email' => 'andrea@gmail.com',
            'password' => Hash::make('andreaspassword'),
        ));
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