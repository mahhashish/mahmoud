<?php

#
# show_source_php2xhml()
#
# Modifies the results of the built-in PHP function, show_source(), so that it
# is XHTML Strict compliant.
#
# Pass a PHP file for which the source should be modified and returned.
#
# Styles used:
#   div#phpSource, div.source
#   span.bg, span.comment, span.default, span.html, span.keyword, span.string
#
function show_source_php2xhtml($file)
    {
    #
    # Get the color-coded source as normally returned by show_source().
    #
    # If PHP version is less than 4.2.0, use output buffering, otherwise use the
    # optional second argument, which became available in 4.2.0
    #
    if(strcmp(PHP_VERSION,'4.2.0') >= 0)
        {
        $source = show_source($file,TRUE);
        }
    else
        {
        ob_start();
        show_source($file);
        $source = ob_get_contents();
        ob_end_clean();
        }

    #
    # The $trans array is used to translate standard HTML 4 tags and their
    # attributes to XHTML compliant <div> and <span> tags with ids and classes.
    #
    $trans = array
        (
        '<code>' => '<div id="phpSource" class="source">',
        '</code>' => '</div>',
        '<font color="'.ini_get('highlight.bg').'">' => '<span class="bg">',
        '<font color="'.ini_get('highlight.comment').'">' => '<span class="comment">',
        '<font color="'.ini_get('highlight.default').'">' => '<span class="default">',
        '<font color="'.ini_get('highlight.html').'">' => '<span class="html">',
        '<font color="'.ini_get('highlight.keyword').'">' => '<span class="keyword">',
        '<font color="'.ini_get('highlight.string').'">' => '<span class="string">',
        '</font>' => '</span>'
        );

    #
    # Translate and return the color-coded source.
    #
    return strtr($source,$trans);
    }

?> 
