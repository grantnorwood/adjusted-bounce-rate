<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit ();

//Delete option from db.
delete_option('adjusted-bounce-rate-options');
