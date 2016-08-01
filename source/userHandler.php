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
    
    function userAdminisrtationPanelHandler(){
        //check for form data
        
        //-------
        //show administrator panel
        showUserAdministratorPanel;
        

    }
    function showUserAdministratorPanel(){
        global $mySQLIServer;
        global $tabePrefix;
        $cookie = $mySQLIServer->real_escape_string($_COOKIE['ECADPHPHUB-UserCoockie']);
        
        //get user information
        $mainQuerie = getUserBaseInformatioQuerie($cookie);
        //get additional information for request when criterias are met
        $mainQuerie .= ' Select * From ad_Users where @userIsAdministrator = 1; ';
        //update last seen
        $mainQuerie .= getLastSessionUpdateQuerie($cookie);
        
        $result = SQLiQuerieHandler($mySQLIServer, $mainQuerie);
        
        //checks if session is active and if user is administrator
        if(checkSession($result[0], $cookie) && $result[0][0]["administrator"] = "1"){
            writeHTMLHeader();
            writeHeader($result[0][0]["username"]);
            echo '';
            echo 'users total: '.count($result[1]);
            echo '</br><form method="POST" action=""><input type="submit" name="newUser" value="new user"></input></form>';
            echo '</br>Users:';
            //make menue for each user
            for ($i = 0; $i < count($result[1]); $i++) {
                echo '<form method="POST" action="">';
                echo ''.$result[1][$i]["username"].' <input type="text" name="userID" value="'.$result[1][$i]["ID"].'" hidden></input> <input type="submit" name="editUser" value="edit"></input><input type="submit" name="logoutUser" value="logout"></input><input type="submit" name="deleteUser" value="delete"></input> (ID:'.$result[1][$i]["ID"].')';
                echo '</form>';
            }
            
            writeHTMLEnd();
        }
    }
    


?>