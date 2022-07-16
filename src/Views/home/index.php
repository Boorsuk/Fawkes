<?php
    /** @var string $msg */
    /** @var array $params */

    echo $msg;

    if(!empty($params)){
        echo '<br/>';
        echo '<pre>';
        print_r($params);
        echo '</pre>';
    }
?>