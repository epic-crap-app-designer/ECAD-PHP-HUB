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
        $cookie = 'c'.getUniqueIdentifier();
        $clientIP = $_SERVER["REMOTE_ADDR"];
        $cookieCreatorQuerie = 'INSERT INTO '.$tabePrefix.'_Sessions (userID, IP, cookie, creationDate, active, lastSeen) VALUES ("'.$userID.'", "'.$clientIP.'", "'.$cookie.'", "'.date("Y-m-d H:i:s").'", true, "'.date("Y-m-d H:i:s").'")';
        
        $result = SQLiQuerieHandler($mySQLIServer, $cookieCreatorQuerie);
        setcookie('ECADPHPHUB-UserCoockie',$cookie);
    }
    function closeSession(){
        setcookie('ECADPHPHUB-UserCoockie',"null");
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
    


?>