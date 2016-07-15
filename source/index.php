<?php
    error_reporting(-1);
    //version information
    $ECADPHPHubVersion = '0.00.01';
    $tabePrefix = "testPrefix80";
    
    
    

    
    
    
    //load libraries
    include_once('installer.php');
    include_once('userHandler.php'); //user management
    //include_once('folderHandler.php'); //folder manager
    
    $mySQLIServer = new mysqli("localhost", "root","","ecadphphubtest");
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    createTables($mySQLIServer, $tabePrefix);
    
    
    
    
    
    
    
    
    
    
    
    echo getUniqueIdentifier();
    echo $_POST['username'];
    
    if($_POST['username']){
        startSession();
        header("Refresh:0; url=?userpanel");
    }
    if(isSession()){
        writeHTMLHeader();
        writeHeader("testUser");
        writeHTMLEnd();
    }else{
        writeLoginScreen();
    }

   
    
    
    
    

    
    function writeHeader($username){
        global $ECADPHPHubVersion;
        echo '<p id="ECADPHPHubVersionHeader">ECAD PHP Hub '.$ECADPHPHubVersion.'<span style="padding-left:80px">user: '.$username.'</span><span style="padding-left:20px"></span><a href="?userpanel">user pannel </a><span style="padding-left:20px"></span><a href="?logout">logout</a></p>';
    }
    function writeHTMLHeader(){
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<title>'.'ECAD PHP Hub'.'</title>';
        echo '</head>';
        echo '<body>';
    }
    function writeHTMLEnd(){
        echo '</body>';
        echo '</html>';
    }
    function getUniqueIdentifier(){
        if(function_exists('com_create_guid') === true){
            return com_create_guid();
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
?>