<?php
    error_reporting(-1);
    //version information
    $ECADPHPHubVersion = '0.00.01';
    
    //load libraries
    include_once('userHandler.php'); //user management
    //include_once('folderHandler.php'); //folder manager
    
    writeHeader("testUser");
    
    
    
    
    
    
    
    
    function writeHeader($username){
        global $ECADPHPHubVersion;
        echo '<p id="ECADPHPHubVersionHeader">ECAD PHP Hub '.$ECADPHPHubVersion.'<span style="padding-left:80px">user: '.$username.'</span><span style="padding-left:20px"></span><a href="?userpanel">user pannel </a><span style="padding-left:20px"></span><a href="?logout">logout</a></p>';
    }
?>