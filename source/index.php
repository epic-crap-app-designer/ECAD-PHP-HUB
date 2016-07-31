<?php
    error_reporting(-1);
    //version information
    $ECADPHPHubVersion = '0.00.02E';
    
    //check for installation
    if(!file_exists('config.php')){
        //load installer
        include_once('installer.php');
        startInstallation();
        exit();
        die();
    }
    
    
    
    //load libraries
    include_once('config.php'); //load configuration
    include_once('userHandler.php'); //user management
    //include_once('folderHandler.php'); //folder manager
    //include_once('pageHandler.php'); //page handler and creator
    
    //chech if sql server is reachable
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        echo 'SQL server unreachable';
        exit();
    }
    //checks if client has a session open (does not check if session is correct!)
    if(isSession()){
        writeHTMLHeader();
        writeHeader("testUser");
        writeHTMLEnd();
        
        
        
        
    }else{
        //login handling
        if(isset($_POST['normalLoginAtempt'])){
            makeLogin();
        }else{
            writeLoginScreen();
        }
    }

   
    
    
    
    

    
    
    //functions ---------------------------------------------------------------------------
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
    
    
    function SQLiQuerieHandler($mySQLIServer, $querie){
        $completeReseult = array();
        
        
        if ($mySQLIServer->multi_query($querie)) {
            do {
                /* store first result set */
                if ($result = $mySQLIServer->store_result()) {
                    $querieReseult = array();
                    
                    $querieReseult = $result->fetch_all(MYSQLI_ASSOC);
                    $result->free();
                    
                    array_push($completeReseult, $querieReseult);
                }
            } while ($mySQLIServer->more_results() && $mySQLIServer->next_result());
        }else{
            //error
            echo $mySQLIServer->error;
            echo '</br></br></br></br></br>';
            exit();
        }
        return $completeReseult;
    }
    
    
    
    
?>