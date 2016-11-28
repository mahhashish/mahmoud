@extends('template_masterpage')

@section('content')
<h1 id="replyh">Ask A Question</h1>
<p class="bluey">Note: If you think your question's been answered correctly, please don't forget to click "✓" icon to mark the answer as "correct".</p>
{{Form::open(array('route'=>'ask_post'))}}
	<p class="minihead">Question's title:</p>
	{{Form::text('title',Input::old('title'),array('class'=>'fullinput'))}}
	<p class="minihead">Explain your question:</p>
	{{Form::textarea('question',Input::old('question'),array('class'=>'fullinput'))}}
	<p class="minihead">Tags: Use commas to split tags (tag1, tag2 etc.). To join multiple words in a tag, use - between the words (tag-name, tag-name-2):</p>
	{{Form::text('tags',Input::old('tags'),array('class'=>'fullinput'))}}
	{{Form::submit('Ask this Question')}}
{{Form::close()}}
@stop

@section('footer_assets')
{{-- A simple jQuery code to lowercase all tags before submission --}}
<script type="text/javascript">
$('input[name="tags"]').keyup(function(){
	$(this).val($(this).val().toLowerCase());
});
</script>
@stop