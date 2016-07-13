<?php
    //version information
    
    //check if client has a user session
    function isSession(){
        if($_COOKIE['ECADPHPHUB-UserCoockie']){
            return true;
        }
        return false;
    }
    

?>