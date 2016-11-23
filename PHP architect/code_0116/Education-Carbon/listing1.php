$outputString = "Time difference between %s & %s: %s hours.\n";
// Date difference
printf(
    $outputString,
    "Berlin", "Brisbane, Australia",
    $berlinNow->diffInHours($brisbane)
);
printf(
    $outputString,
    "Berlin", "New York City, America",
    $berlinNow->diffInHours($newYorkCity)
);