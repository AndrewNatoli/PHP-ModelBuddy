<?php
/**
 * PHP Model Buddy
 * Functions
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

/**
 * mb_debugMessage
 * Prints a message to the screen.
 * If supplied an array it will print_r it between <pre></pre> tags
 * @param mixed $str
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

/**
 * mb_autoload_models
 * Automatically loads the model classes from MB_MODEL_DIR
 * Moved to a function because certain projects may need to
 * call this at a different time to prevent errors.
 */
function mb_autoload_models() {
    if(!file_exists(MB_MODEL_DIR))
        die("Could not find models directory!");
    else {
        /* TODO: Need a way to make sure these files which are being auto-loaded couldn't have been injected by an attacker */
        $mb_modelFiles = scandir(MB_MODEL_DIR);

        //Sanitize the array by removing any non PHP file indexes
        for($i=0; $i<=count($mb_modelFiles);$i++) {
            if(!strstr($mb_modelFiles[$i],"php"))
                unset($mb_modelFiles[$i]);
        }

        //Now load them in.
        foreach($mb_modelFiles as $mb_modelFilename) {
            $mb_modelFile = MB_MODEL_DIR . "/" . $mb_modelFilename;
            mb_debugMessage("Loading model: " . $mb_modelFile);
            require_once($mb_modelFile);
        }
    }
}