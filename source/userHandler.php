<?php
    //version information
    
    //check if client has a user session
    function isSession(){
        if($_COOKIE['ECADPHPHUB-UserCoockie']){
            return true;
        }
        return false;
    }
    function startSession(){
        setcookie('ECADPHPHUB-UserCoockie','A');
    }
    function closeSession(){
        setcookie('ECADPHPHUB-UserCoockie',"null");
    }
    function writeLoginScreen(){
        writeHTMLHeader();
        ?>
<div style="text-align:center; margin= 0 auto;">
<p>ECAD PHP HUB</p>
<form method="POST" action="">
Username: <input type="text" name="username"></input><br/>
Password: <input type="password" name="password"></input><br/>
<input type="submit" name="submit" value="login"></input>
</form>
</div>
<?php
    writeHTMLEnd();
    }
    


?>