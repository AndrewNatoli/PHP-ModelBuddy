<?php
/**
 * PHP Model Buddy
 * Functions
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

function mb_debugMessage($str) {
    if(MB_DEBUG == false)
        return;
    else {

        echo '<span class="mb_debugMessage">';
        if(is_array($str)) {
            echo "<pre>";
            print_r($str);
            echo "</pre>";
        }
        else {
            echo $str;
        }
        echo '</span><br/>';
    }
}