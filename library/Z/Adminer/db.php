<?php
function adminer_object() {
    // required to run any plugin
    //http://cimb.me/sys/adminer/db.php?server=localhost&username=root&db=cimb
    include_once "plugin.php";
    // autoloader
    foreach (glob("/plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        // specify enabled plugins here
        new Dbadmin
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
} 
// include original Adminer or Adminer Editor
include "adminer-3.3.4.php";
?>