<?php

#
# show_source_css2xhtml()
#
# Creates XHTML compliant color-coded syntax for CSS similar to that made by
# the built-in PHP function show_source() and similar functions.
#
# Pass a CSS file for which highlighted source should be created and returned.
#
# Styles used:
#   div#cssSource, div.source
#   span.tag, span.identifier, span.attribute, span.value, span.comment
#
function show_source_css2xhtml($file)
    {
    #
    # Get source of passed CSS file as string.
    #
    $fp = fopen($file,'r');
    $source = fread($fp,filesize($file));
    fclose($fp);
    clearstatcache();

    #
    # Enclose tag, tag.class, tag.id, .class, #id with appropriate classed <span>s.
    #
    # RegEx:
    #    match any letter one or more times followed by a pound (#) or period (.) or
    #    colon (:) exactly once followed by one or more combinations of letters and/or
    #    numbers, which, along with the pound, period, or colon, should be matched
    #    zero or one times followed by optional whitespace followed by a single white-
    #    space or comma (,) or code block ({ to }) once.  Ignore case, period (.)
    #    token matches new lines (\n), not greedy.
    #
    $source = preg_replace("!([a-z]+)?(([#.:])([a-z0-9]+))?(\s*( |,|(\{.*\})))!isU",'<span class="tag">'."$1".'</span>'."$3".'<span class="identifier">'."$4".'</span>'."$5",$source);

    #
    # Enclose attribute: value; with appropriate classed <span>s.
    #
    # RegEx:
    #    match any combination of dashes (-) and letters followed by optional white-
    #    space followed by a colon (:) followed by optional white-space followed by
    #    any character one or more times followed by optional white-space followed by
    #    a semi-colon (;) followed by optional white-space.  Period (.) token matches
    #    new lines (\n), not greedy.
    #
    $source = preg_replace("!([-a-zA-Z]+)(\s*:\s*)(.+)(\s*;\s*)!sU",'&nbsp;&nbsp;&nbsp;&nbsp;<span class="attribute">'."$1".'</span>'."$2".'<span class="value">'."$3".'</span>'."$4",$source);

    #
    # Callback function used to reclaim comments that have already been color
    # coded due to previous preg_replace().
    #
    function comment($arr)
        {
        return '<span class="comment">'.strip_tags($arr[0]).'</span>';
        }

    #
    # Enclose comments with appropriate classed <span> while using the callback
    # function 'comment' to reclaim already matched text that is within a comment
    # block.
    #
    # The following would complete the task without the callback, but it didn't
    # work as desired:
    #
    #    $source = preg_replace("!(/\*).*(\*/)!sU",'<span class="comment">'.strip_tags("$0").'</span>',$source);
    #
    $source = preg_replace_callback("!(/\*).*(\*/)!sU",'comment',$source);

    #
    # Enclose and return the color-coded source.
    #
    return '<div id="cssSource" class="source">'.nl2br($source).'</div>';
    }

?>
