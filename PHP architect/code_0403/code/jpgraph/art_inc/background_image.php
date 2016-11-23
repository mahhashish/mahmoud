if (USING_TRUECOLOR) {
  $graph->SetBackgroundImage('img/abc-background_prefade.png', BGIMG_FILLFRAME);
} else {
  //AdjBackgroundImage only works with GD, not GD2 true color
  $graph->SetBackgroundImage('img/abc-background.png', BGIMG_FILLFRAME);
  $graph->AdjBackgroundImage(0.9, 0.3);
}

