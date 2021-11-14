<?php
$url = 'http://www.omdbapi.com/?apikey=ad692e&i='.$id;
$contents = file_get_contents($url);
$contents = json_decode($contents);
$title = $contents->Title;
$year = $contents->Year;
$released = $contents->Released;
$runtime = $contents->Runtime;
$rated = $contents->Rated;
$genre = $contents->Genre;
$director = $contents->Director;
$writer = $contents->Writer;
$actors = $contents->Actors;
$plot = $contents->Plot;
$language = $contents->Language;
$country = $contents->Country;
$poster = $contents->Poster;
$all_ratings = $contents->Ratings; // array
$rating = $contents->imdbRating;
$votes = $contents->imdbVotes;
?>