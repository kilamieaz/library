<?php

//<li class="{{ url('/home') == request()->url() ? 'active' : '' }}">
//<a href="{{ url('/home') }}">Dashboard</a></li>


\Html::macro('smartNav', function($url, $title) {
    $class = $url == request()->url() ? 'active' : '' ;
    return "<li class=\"$class\"><a href=\"$url\">$title</a></li>";
});

?>