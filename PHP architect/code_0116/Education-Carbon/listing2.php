$septEighteen2014 = Carbon::createFromDate(
   2014, 9, 18, $dtBerlin->getTimezone()
);

printf(
    "difference between now and %s in \n\thours: %d, \n\t"
    . "days: %d, \n\tweeks: %d, \n\tweekend days: %d, \n\t"
    . "+1week days: %s, \n\thuman readable: %s",
    $septEighteen2014->toFormattedDateString(),
    $dtBerlin->diffInHours($septEighteen2014),
    $dtBerlin->diffInDays($septEighteen2014),
    $dtBerlin->diffInWeeks($septEighteen2014),
    $dtBerlin->diffInWeekendDays($septEighteen2014),
    $dtBerlin->diffInWeekDays($septEighteen2014),
    $dtBerlin->diffForHumans($septEighteen2014)
);