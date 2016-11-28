<?php
class AuthorsTableSeeder extends Seeder {
 
    public function run()
    {

        Author::create(array(
            'name' => 'Lauren',
            'surname'=>'Oliver'
        ));

        Author::create(array(
            'name' => 'Stephenie',
            'surname'=>'Meyer'
        ));

        Author::create(array(
            'name' => 'Dan',
            'surname'=>'Brown'
        ));

    }
 
}