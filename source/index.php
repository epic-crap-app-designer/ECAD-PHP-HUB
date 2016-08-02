<?php
    error_reporting(-1);
    //version information
    $ECADPHPHubVersion = '0.00.02I';
    
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
            userAdminisrtationPanelHandler();
            

            
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
        //executes multiple queries seperated by semicolumn and fetches the result into an array
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
    

    function getLastSessionUpdateQuerie($cookie){
        //pus together a querie string that will upldate the last seen parameter of a session by its cookie
        global $tabePrefix;
        $querie = 'UPDATE '.$tabePrefix.'_Sessions SET lastSeen="'.date("Y-m-d H:i:s").'" where cookie = '."'".$cookie."'".';';
        return $querie;
    }

    function checkSession($array, $cookie){
        
        //check if there are results (checks if there is a session with this cookie that is connected to a user)
        if (count($array) > 0){
            
            //check if session has absolut timeout
            if(isset($array[0]["timeout"])){
                if (((new DateTime('now'))->getTimestamp() - strtotime($array[0]["creationDate"])) >= $array[0]["timeout"]){
                    //session has expired  because user was too long not active
                    makeLogout('your session has expired (absolute session timeout)');
                    return false;
                }
                
            }
            
            //check if unusedTimeout was set
            if(isset($array[0]["unusedTimeout"])){
                if (((new DateTime('now'))->getTimestamp() - strtotime($array[0]["lastSeen"])) >= $array[0]["unusedTimeout"]){
                    //session has expired  because user was too long not active
                    makeLogout('your session has expired (you haave been non active for too long)');
                    return false;
                }
                
            }
            
        }else{
            closeSessionOnClient();
            writeLoginScreen('your session has expired');
            return false;
        }
        
        return true;
    }
    function getUserBaseInformatioQuerie($cookie){
        global $tabePrefix;
        $getBaseUserInformationbySession = 'Select session.creationDate, session.active, session.lastSeen, session.unusedTimeout, session.timeout, @userID := session.userID as "userID", user.username, @userIsAdministrator :=user.administrator as "administrator" from '.$tabePrefix.'_Sessions session left join '.$tabePrefix.'_Users user on session.userID = user.ID where cookie = '."'".$cookie."'".';';
        return $getBaseUserInformationbySession;
    }
    
    
    
    
    
    
    
    
?>