@extends('template_masterpage')

@section('content')
	<h1>{{$title}}</h1>

	@if(count($questions))

		@foreach($questions as $question)

			<div class="qwrap questions">
				{{-- Guests cannot see the vote arrows --}}
				@if(Sentry::check())
					<div class="arrowbox">
						{{HTML::linkRoute('vote','',array('up',$question->id),array('class'=>'like', 'title'=>'Upvote'))}}
						{{HTML::linkRoute('vote','',array('down',$question->id),array('class'=>'dislike','title'=>'Downvote'))}}
					</div>
				@endif

				{{-- class will differ on the situation --}}
				@if($question->votes > 0)
					<div class="cntbox cntgreen">
				@elseif($question->votes == 0)
					<div class="cntbox">
				@else
					<div class="cntbox cntred">
				@endif
						<div class="cntcount">{{$question->votes}}</div>
						<div class="cnttext">vote</div>
					</div>

				<?php
					//does the question have an accepted answer?
					$answers 	= $question->answers; 
					$accepted 	= false; //default false

					//We loop through each answer, and check if there is an accepted answer
					if($question->answers!=null) {
						foreach ($answers as $answer) {
							//If an accepted answer is found, we break the loop
							if($answer->correct==1) {
								$accepted=true;
								break;
							}
						}
					}
				?>

				@if($accepted)
					<div class="cntbox cntgreen">
				@else
					<div class="cntbox cntred">
				@endif
						<div class="cntcount">{{count($answers)}}</div>
						<div class="cnttext">answer</div>
					</div>

					
				<div class="qtext">
					<div class="qhead">
						{{HTML::linkRoute('question_details',$question->title,array($question->id,Str::slug($question->title)))}}
					</div>
					<div class="qinfo">Asked by <a href="#">{{$question->users->first_name.' '.$question->users->last_name}}</a> around {{date('m/d/Y H:i:s',strtotime($question->created_at))}}</div>
					@if($question->tags!=null)
						<ul class="qtagul">
							@foreach($question->tags as $tag)
								<li>{{HTML::linkRoute('tagged',$tag->tag,$tag->tagFriendly)}}</li>
							@endforeach
						</ul>
					@endif
				</div>
			</div>
		@endforeach

		{{-- and lastly, the pagination --}}
		{{$questions->links()}}

	@else
		No questions found. {{HTML::linkRoute('ask','Ask a question?')}}
	@endif

@stop