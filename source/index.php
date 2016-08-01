<?php
    error_reporting(-1);
    //version information
    $ECADPHPHubVersion = '0.00.02H';
    
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
        if(isset($_GET["userpanel"])){
            //userpannel request
            userPannelHandler();
        }
        else if(isset($_GET["settings"])){
            //settings request
            
        }
        else if(isset($_GET["F"])){
            //folder request
            //check if private user folder
            if($_GET["F"] == 'user'){
                //private user folde
            }else{
                
            }
        }
        else if(isset($_GET["P"])){
            //page request
            
        }
        else if(isset($_GET["adminpanel"])){
            //administrator panel request
                
        }
        else if(isset($_GET["logout"])){
            //perform logout
            makeLogout('your have logged out');
        }
        else{
            //no known selection
            //redirect to userpanel
            header("Refresh:0; url=?userpanel");
        }
        
    }else{
        //login handling
        if(isset($_POST['normalLoginAtempt'])){
            makeLogin();
        }else{
            writeLoginScreen('');
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
            echo 'an internal server error has ocured</br></br></br>';
            echo $mySQLIServer->error;
            echo '</br></br></br></br></br>';
            exit();
        }
        return $completeReseult;
    }
    
    function userPannelHandler(){
        global $mySQLIServer;
        global $tabePrefix;
        $cookie = $mySQLIServer->real_escape_string($_COOKIE['ECADPHPHUB-UserCoockie']);

        $getUserInformationbySession =  'Select session.creationDate, session.active, session.lastSeen, session.unusedTimeout, session.timeout, session.userID, user.username, user.personalFolderID, user.administrator from '.$tabePrefix.'_Sessions session left join '.$tabePrefix.'_Users user on session.userID = user.ID where cookie = '."'".$cookie."'".';';
        
        //update last seen
        $getUserInformationbySession .= getLastSessionUpdateQuerie($cookie);
        
        $result = SQLiQuerieHandler($mySQLIServer, $getUserInformationbySession);

        //checks if there are enougth results
        if(checkSession($result[0], $cookie)){
            writeHTMLHeader();
            writeHeader($result[0][0]["username"]);
            echo '<a href="?F=user&path=/">myFolder</a>';
            echo '</br></br><a href="?F">all Folders</a>';
            echo '</br></br><a href="?P">all Pages</a>';
            echo '</br></br><a href="?settings">settings</a>';
            if($result[0][0]["administrator"] == 1){
                echo '</br></br><a href="?adminpanel">administratorPanel</a>';
            }
            writeHTMLEnd();
        }
    }
    function getLastSessionUpdateQuerie($cookie){
        global $tabePrefix;
        $querie = 'UPDATE '.$tabePrefix.'_Sessions SET lastSeen="'.date("Y-m-d H:i:s").'" where cookie = '."'".$cookie."'".';';
        return $querie;
    }

    
    
    
    
    
    
    
    
    
?>