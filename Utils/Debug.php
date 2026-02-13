<?php

class Debug
{
    public function printr($var)
    {
        echo "<h1>". $var ."</h1>";
    }
    
    public function dd($a) {
        echo("<pre>");
        echo("<code>");
        var_dump($a);
        die();
        echo("</code>");
        echo("</pre>");
    }
}