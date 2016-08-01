<?php
    //version information
    
    //check if client has a user session
    function isSession(){
        if(isset($_COOKIE['ECADPHPHUB-UserCoockie'])){
            return true;
        }
        return false;
    }
    function makeLogin(){
        global $mySQLIServer;
        global $tabePrefix;
        
        //echo $_POST['username'].' requests login';

        $username = $mySQLIServer->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        
        
        $getPasswordHashFromUserQuerie =  'Select password, ID from '.$tabePrefix.'_Users where username = '."'".$username."'";
        
        $result = SQLiQuerieHandler($mySQLIServer, $getPasswordHashFromUserQuerie);
        
        //checks if there are enougth results
        if(count($result[0]) > 0){
            //check password
            if(checkPassword($password, $result[0][0]["password"])){
                //password confirmed
                echo "welcome";
                //create session
                startSession($result[0][0]["ID"]);
                //reload page
                echo "login complete, welcome.</br></br></br> redirecting to userpanel";
                header("Refresh:0; url=?userpanel");
            }else{
                //password not correct
                writeLoginScreen('password incorrect or user doesn\'t exist');
            }
        }else{
            //user not found
            writeLoginScreen('password incorrect or user doesn\'t exist');
        }

        
    }
    function startSession($userID){
        global $mySQLIServer;
        global $tabePrefix;
        $cookie = 'c.'.getUniqueIdentifier();
        $clientIP = $_SERVER["REMOTE_ADDR"];
        $cookieCreatorQuerie = 'INSERT INTO '.$tabePrefix.'_Sessions (userID, IP, cookie, creationDate, active, lastSeen) VALUES ("'.$userID.'", "'.$clientIP.'", "'.$cookie.'", "'.date("Y-m-d H:i:s").'", true, "'.date("Y-m-d H:i:s").'")';
        
        $result = SQLiQuerieHandler($mySQLIServer, $cookieCreatorQuerie);
        setcookie('ECADPHPHUB-UserCoockie',$cookie);
    }
    function closeSessionOnClient(){
        unset($_COOKIE['ECADPHPHUB-UserCoockie']);
        setcookie('ECADPHPHUB-UserCoockie', null, -1, '/');
    }
    function writeLoginScreen($errorText){
        writeHTMLHeader();
        ?>
<div style="text-align:center; margin= 0 auto;">
<p>ECAD PHP HUB</p>
<p><font color="red"><?php echo $errorText; ?> </font> </p>
<form method="POST" action="">
Username: <input type="text" name="username"></input><br/>
Password: <input type="password" name="password"></input><br/>
<input type="submit" name="normalLoginAtempt" value="login"></input>
</form>
</div>
<?php
    writeHTMLEnd();
    }
    
    function checkPassword($password,$passwordHash){
        return password_verify($password,$passwordHash);
    }
    function makeLogout($message){
        global $mySQLIServer;
        global $tabePrefix;
        $cookie = $mySQLIServer->real_escape_string($_COOKIE['ECADPHPHUB-UserCoockie']);
        
        //copies session to another table
        $cookieRemoveQuerie = 'INSERT INTO '.$tabePrefix.'_ClosedSessions (ID, userID, IP, cookie, creationDate, active, lastSeen, unusedTimeout, timeout, dateOfClose) SELECT session.ID, session.userID, session.IP, session.cookie, session.creationDate, 0, session.lastSeen, session.unusedTimeout, session.timeout, "'.date("Y-m-d H:i:s").'" FROM '.$tabePrefix.'_Sessions as session where session.cookie ='."'".$cookie."'".'; ';
        //removes session from sql server
        $cookieRemoveQuerie .= 'DELETE FROM '.$tabePrefix.'_Sessions where cookie ='."'".$cookie."'";

        $result = SQLiQuerieHandler($mySQLIServer, $cookieRemoveQuerie);
        

        //removes cookie from client
        closeSessionOnClient();

        
        //show login screen
        writeLoginScreen($message);
    }
    
    function checkSession($array, $cookie){

        //check if there are results (checks if there is a session with this cookie that is connected to a user)
        if (count($array) > 0){

            //check if session has absolut timeout
            if(isset($array[0]["timeout"])){
                /*
                var_dump($array[0]["creationDate"]);
                var_dump(strtotime($array[0]["creationDate"]));
                var_dump(((new DateTime('now'))->getTimestamp() - strtotime($array[0]["creationDate"])));
                var_dump($array[0]["timeout"]);
                */
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
    


?>