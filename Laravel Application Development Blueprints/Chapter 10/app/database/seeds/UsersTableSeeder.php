<?php
Class UsersTableSeeder extends Seeder {
 
    public function run()
    {
	DB::table('users')->delete();
	
    User::create(array(
                'email' => 'member@email.com',
                'password' => Hash::make('password'),
                'name' => 'John Doe',
                'admin'=>0
    ));
        
	User::create(array(
            'email' => 'admin@store.com',
            'password' => Hash::make('adminpassword'),
            'name' => 'Jeniffer Taylor',
            'admin'=>1
    ));	

    }
 
}
