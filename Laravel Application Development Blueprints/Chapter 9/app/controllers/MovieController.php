<?php

class MovieController extends BaseController {


	public function getMovieInfo($moviename)
	{

		$movie = Movie::where('name', 'like', '%'.$moviename.'%')->first();
		if($movie){
			
			$movieInfo = array('error'=>false,'Movie Name'=>$movie->name,'Release Year'=>$movie->release_year,'Movie ID'=>$movie->id);
			$movieactors = json_decode($movie->Actors);
			foreach ($movieactors as $actor) {
				$actorlist[] = array("Actor"=>$actor->name);
			}
			$actorlist =array('Actors'=>$actorlist);
			return Response::json(array_merge($movieInfo,$actorlist));	

		}
		else{

			return Response::json(array(
				'error'=>true,
				'description'=>'We could not find any movie in database like :'.$moviename
				));
		}
	}
	public function putMovie($moviename,$movieyear)
	{

		$movie = Movie::where('name', '=', $moviename)->first();
		if(!$movie){
			
			$the_movie = Movie::create(array('name'=>$moviename,'release_year'=>$movieyear));

			return Response::json(array(
				'error'=>false,
				'description'=>'The movie successfully saved. The ID number of Movie is : '.$the_movie->id
				));	

		}
		else{

			return Response::json(array(
				'error'=>true,
				'description'=>'We have already in database : '.$moviename.'. The ID number of Movie is : '.$movie->id
				));
		}
	}
	public function deleteMovie($id)
	{

		$movie = Movie::find($id);
		if($movie){
			
			$movie->delete();

			return Response::json(array(
				'error'=>false,
				'description'=>'The movie successfully deleted : '.$movie->name
				));	

		}
		else{

			return Response::json(array(
				'error'=>true,
				'description'=>'We could not find any movie in database with ID number :'.$id
				));
		}
	}
}