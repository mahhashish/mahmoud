<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your awesome news aggregation site</title>
    <style type="text/css">
    body { font-family: Tahoma, Arial, sans-serif; }
    h1, h2, h3, strong { color: #666; }
    blockquote{ background: #bbb; border-radius: 3px; }
    li { border: 2px solid #ccc; border-radius: 5px; list-style-type: none; margin-bottom: 10px }
    a { color: #1B9BE0; }
    </style>
</head>
<body>

    <h1>Your awesome news aggregation site</h1>

   <h2>Latest News</h2>
    @if(count($news))
        {{--We loop all news feed items --}}
        @foreach($news as $each)
            <h3>News from {{$each->title}}:</h3>
            <ul>
            {{-- for each feed item, we get and parse its feed elements --}}
            <?php $feeds = Str::parse_feed($each->feed); ?>
            @if(count($feeds))
                {{-- In a loop, we show all feed elements one by one --}}
                @foreach($feeds as $eachfeed)
                    <li>
                        <strong>{{$eachfeed->title}}</strong><br />
                        <blockquote>{{Str::limit(strip_tags($eachfeed->description),250)}}</blockquote>
                        <strong>Date: {{$eachfeed->pubDate}}</strong><br />
                        <strong>Source: {{HTML::link($eachfeed->link,Str::limit($eachfeed->link,35))}}</strong>
                        
                    </li>
                @endforeach
            @else
                <li>No news found for {{$each->title}}.</li>
            @endif
            </ul>
        @endforeach
    @else
        <p>No News found :(</p>
    @endif

    <hr />

    <h2>Latest Sports News</h2>
    @if(count($sports))
        {{--We loop all news feed items --}}
        @foreach($sports as $each)
            <h3>Sports News from {{$each->title}}:</h3>
            <ul>
            {{-- for each feed item, we get and parse its feed elements --}}
            <?php $feeds = Str::parse_feed($each->feed); ?>
            @if(count($feeds))
                {{-- In a loop, we show all feed elements one by one --}}
                @foreach($feeds as $eachfeed)
                    <li>
                        <strong>{{$eachfeed->title}}</strong><br />
                        <blockquote>{{Str::limit(strip_tags($eachfeed->description),250)}}</blockquote>
                        <strong>Date: {{$eachfeed->pubDate}}</strong><br />
                        <strong>Source: {{HTML::link($eachfeed->link,Str::limit($eachfeed->link,35))}}</strong>
                    </li>
                @endforeach
            @else
                <li>No Sports News found for {{$each->title}}.</li>
            @endif
            </ul>
        @endforeach
    @else
        <p>No Sports News found :(</p>
    @endif


    <hr />


    <h2>Latest Technology News</h2>
    @if(count($technology))
       {{--We loop all news feed items --}}
        @foreach($technology as $each)
            <h3>Technology News from {{$each->title}}:</h3>
            <ul>
            {{-- for each feed item, we get and parse its feed elements --}}
            <?php $feeds = Str::parse_feed($each->feed); ?>
            @if(count($feeds))
                {{-- In a loop, we show all feed elements one by one --}}
                @foreach($feeds as $eachfeed)
                    <li>
                        <strong>{{$eachfeed->title}}</strong><br />
                        <blockquote>{{Str::limit(strip_tags($eachfeed->description),250)}}</blockquote>
                        <strong>Date: {{$eachfeed->pubDate}}</strong><br />
                        <strong>Source: {{HTML::link($eachfeed->link,Str::limit($eachfeed->link,35))}}</strong>
                    </li>
                @endforeach
            @else
                <li>No Technology News found for {{$each->title}}.</li>
            @endif
            </ul>
        @endforeach
    @else
        <p>No Technology News found :(</p>
    @endif

    
</body>
</html>
