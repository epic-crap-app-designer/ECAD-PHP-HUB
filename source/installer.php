<?php
    error_reporting(-1);
    function createTables($mySQLIServer, $tabePrefix){
        //user table
        $mySQLInstallString = '';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_User (ID VARCHAR(50), username Text, groupID VARCHAR(50), UPassword VARCHAR(255), email Text, active boolean, confirmedEmail boolean, personalFolderID VARCHAR(50), allowedAmountOfFolders int, allowedAmountOfPages int, AllowedAmountOfMenues int, canChageSystemFolder boolean, canChagePassword boolean, administrator boolean, deleted boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Sessions (ID VARCHAR(50), creatorID VARCHAR(50), IP VARCHAR(50), cookie VARCHAR(50), creationDate date, active boolean, lastSeen date, unusedTimeout time, timeout time); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Pages (ID VARCHAR(50), creatorID VARCHAR(50), creationDate date, isPublic boolean, name VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_PageShares (ID VARCHAR(50), creatorID VARCHAR(50), administrative boolean, canEdit boolean, canRename boolean, canView boolean, canDeletePage boolean, canAddUsers boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Folders (ID VARCHAR(50), uID VARCHAR(50), creationDate date, public boolean, name  VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_FolderShare (ID VARCHAR(50), creatorID VARCHAR(50), creationdate date, canView boolean, canDelete boolean, canUpload boolean, canDownload boolean, canRename boolean, canDeleteFolder boolean, canAddUser boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Administrators (auID VARCHAR(50), creatorID VARCHAR(50), creationdate date); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Group (ID VARCHAR(50), name VARCHAR(50), frontPageType VARCHAR(50), frontPageID VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Menus (ID VARCHAR(50), Creator VARCHAR(50), name VARCHAR(50), itemtype VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_MenueItems (ID VARCHAR(50), MenueID VARCHAR(50), test VARCHAR(50), type VARCHAR(50), destinationType VARCHAR(50), destinationID VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_MenuesinPages (menueID VARCHAR(50), pageID VARCHAR(50), pageindex VARCHAR(50), position VARCHAR(50)); ';
        
        
        //raw
        /*
        create table _User (ID VARCHAR(50), Name Text, groupID VARCHAR(50), UPassword VARCHAR(50), email Text, active boolean, confirmedEmail boolean, personalFolderID VARCHAR(50), allowedAmountOfFolders int, allowedAmountOfPages int, AllowedAmountOfMenues int, canChageSystemFolder boolean, canChagePassword boolean, administrator boolean);
        create table _Sessions (ID VARCHAR(50), uID VARCHAR(50), IP VARCHAR(50), cookie VARCHAR(50), creationDate date, active boolean, lastSeen date, unusedTimeout time, timeout time);
        create table _Pages (ID VARCHAR(50), creatorID VARCHAR(50), creationDate date, isPublic boolean, name VARCHAR(50));
        create table _PageShares (ID VARCHAR(50), uID VARCHAR(50), administrative boolean, canEdit boolean, canRename boolean, canView boolean, canDeletePage boolean, canAddUsers boolean);
        create table _Folders (ID VARCHAR(50), uID VARCHAR(50), creationDate date, public boolean, name  VARCHAR(50));
        create table _FolderShare (ID VARCHAR(50), uID VARCHAR(50), creationdate date, canView boolean, canDelete boolean, canUpload boolean, canDownload boolean, canRename boolean, canDeleteFolder boolean, canAddUser boolean);
        create table _Administrators (auID VARCHAR(50), uID VARCHAR(50), creationdate date);
        create table _Group (ID VARCHAR(50), name VARCHAR(50), frontPageType VARCHAR(50), frontPageID VARCHAR(50));
        create table _Menus (ID VARCHAR(50), Creator VARCHAR(50), name VARCHAR(50), itemtype VARCHAR(50));
        create table _MenueItems (ID VARCHAR(50), MenueID VARCHAR(50), test VARCHAR(50), type VARCHAR(50), destinationType VARCHAR(50), destinationID VARCHAR(50));
        create table _MenuesinPages (menueID VARCHAR(50), pageID VARCHAR(50), pageindex VARCHAR(50), position VARCHAR(50));
         */
        

        if ($mySQLIServer->multi_query($mySQLInstallString)) {
            do {
                /* store first result set */
                if ($result = $mySQLIServer->store_result()) {
                    while ($row = $result->fetch_row()) {
                        echo ("%s\n". $row[0]);
                    }
                    $result->free();
                }
                /* print divider */
                if ($mySQLIServer->more_results()) {
                    //echo("-----------------\n");
                }
            } while ($mySQLIServer->next_result());
        }else{
            //error
            echo $mySQLIServer->error;
            echo '</br></br></br></br></br>';
        }
        
        /* close connection */
        $mySQLIServer->close();
        
    }
    function createProgramFolders($dataFolderName){
        mkdir('./'.$dataFolderName.'/users', 0777, true);
        mkdir('./'.$dataFolderName.'/programfiles', 0777, true);
        mkdir('./'.$dataFolderName.'/installer', 0777, true);
    }
    function createConfigFileFromHTMLFormData($dataFolderName){
        global $ECADPHPHubVersion;
        $ecadphpconfigfile = fopen("config.php", "w");
        $ecadphpconfigHead = '<?php'."\r\n".'$datarootpath='."'".__DIR__.$dataFolderName."'".';'."\r\n".'$firstInstallationVersion='."'".$ECADPHPHubVersion."'".';'."\r\n";
        $sqlString = '$mySQLIServer = new mysqli("'.$_POST['SQLServerAdress'].'","'.$_POST['SQLServerUsername'].'","'.$_POST['SQLServerUserPassword'].'","'.$_POST['SQLServerDatabase'].'");'."\r\n";
        $tabePrefixInstallString = '$tabePrefix="'.$_POST['SQLServerTablePrefix'].'";'."\r\n";

        fwrite($ecadphpconfigfile, $ecadphpconfigHead.$sqlString.$tabePrefixInstallString.'?'.'>');
        fclose($ecadphpconfigfile);

    }
    function startInstallation(){
        global $ECADPHPHubVersion;
        if(!isset($_POST['ECADPHPInstallCycle'])){
            writeHTMLHeader();
            echo '<div style="text-align:center; margin= 0 auto;">';
            echo 'Welcome to ECAD PHP Hub '.$ECADPHPHubVersion;
            echo '</br></br>';
            echo 'In a few easy steps we will set up your ECAD PHP HUB Installation';
            echo '</br>';
            echo '</br></br>';
            echo '<form method="POST" action=""><input name="ECADPHPInstallCycle" value="1" hidden><input type="submit" value="next -->"></form>';
            echo '</div>';
            writeHTMLEnd();
        }
        else if($_POST['ECADPHPInstallCycle'] == "1"){
            ECADPHPInstallCycle1(false, false, false);
        }
        else if($_POST['ECADPHPInstallCycle'] == "2"){
            if  (!isset($_POST['SQLTablesToUse'])){
                ECADPHPInstallCycle1(true, false, false);
                exit;
                die;
            }
            //check if SQL login is correct
            $mySQLIServer = new mysqli($_POST['SQLServerAdress'], $_POST['SQLServerUsername'],$_POST['SQLServerUserPassword']);
            if (mysqli_connect_errno()) {
                ECADPHPInstallCycle1(false, true, false);
                exit;
                die;
            }
            if(!isset($_POST['SQLServerDatabase'])){
                ECADPHPInstallCycle1(false, true, false);
                exit;
                die;
            }
            if  ($_POST['SQLTablesToUse'] == "useExisting"){
                $mySQLIServer = new mysqli($_POST['SQLServerAdress'], $_POST['SQLServerUsername'],$_POST['SQLServerUserPassword'],$_POST['SQLServerDatabase']);
                if (mysqli_connect_errno()) {
                    ECADPHPInstallCycle1(false, false, true);
                    exit;
                    die;
                }
            }
            if($_POST['SQLServerDatabase'] == ""){
                ECADPHPInstallCycle1(false, false, true);
            }
            ECADPHPInstallCycle2(false,false,false,false);
        }
        else if($_POST['ECADPHPInstallCycle'] == "3"){

            
            if(!isset($_POST['ECADPHPHUBAdministratorUsername']) || $_POST['ECADPHPHUBAdministratorUsername'] == ""){
                //no username given
                ECADPHPInstallCycle2(true,false,false,false);
                exit;
            }
            if(!isset($_POST['ECADPHPHUBAdministratorPassword']) || $_POST['ECADPHPHUBAdministratorPassword'] == ""){
                //no password was given
                ECADPHPInstallCycle2(false,true,false,false);
                exit;
            }
            if(!isset($_POST['ECADPHPHUBAdministratorPassword2']) || $_POST['ECADPHPHUBAdministratorPassword2'] == ""){
                //password was not repeated
                ECADPHPInstallCycle2(false,false,true,false);
                exit;
            }
            if($_POST['ECADPHPHUBAdministratorPassword'] != $_POST['ECADPHPHUBAdministratorPassword2']){
                //password was not correctly repeated
                ECADPHPInstallCycle2(false,false,false,true);
                exit;
            }
            
            ECADPHPInstallCycle3();
        }
        else if($_POST['ECADPHPInstallCycle'] == "4"){
            writeHTMLHeader();
            echo '</br>starting installation......</br></br>';
            if (installFromHTMLFormData()){
                //complete
                
                echo 'Installation complete!';
                echo '</br></br>';
                
                if($_POST['deleteInstallerAfterInstallation']){
                    deleteInstaller();
                    echo 'Installer has been deleted. If there is a problem with your installation please download it again';
                }else{
                    echo 'If the Installation is working correctly we would recomend to delete the file installer.php from your installation directory!</br>';
                }
                echo '</br>';
                echo '</br>';
                echo '</br>';
                echo '</br>';
                echo '</br>';
                echo '<a href=""> go to login page</a>';
                writeHTMLEnd();
            }else{
                //problem
                echo 'there was a problem!';
                echo '</br>';
                echo '<a href="">restart installation</a>';
                echo '</br>';
                writeHTMLEnd();
            }
        }else{
            echo 'ups there was an ERROR, your ECADPHPInstallCycle is not valid';
        }
    }
    
    //Installation process Interface functions ---------------------------------------------------------------------------------------------------
    function ECADPHPInstallCycle1($isError1, $isError2, $cantConnectTODatabase){
        writeHTMLHeader();
        echo 'ECAD PHP HUB Installation';
        echo '</br>';
        echo '</br>';
        echo 'Step 1 out of 4 (SQL Database)';
        echo '<form method="POST" action="">';
        echo '</br></br>';
        if($isError1){
            echo '<font color="red">Please Choose one of the following</font></br>';
        }

        echo 'do you want to create a new Database or use an existing one?';
        echo '</br>';

        if(isset($_POST['SQLTablesToUse']) && $_POST['SQLTablesToUse'] == "useExisting"){
            echo '<input type="radio" name="SQLTablesToUse" value="useExisting" checked>';
        }else{
            echo '<input type="radio" name="SQLTablesToUse" value="useExisting" >';
        }
        echo '<label> use existing</label><br>';
        if(isset($_POST['SQLTablesToUse']) &&$_POST['SQLTablesToUse'] == "createNew"){
            echo '<input type="radio" name="SQLTablesToUse" value="createNew" checked>';
        }else{
            echo '<input type="radio" name="SQLTablesToUse" value="createNew">';
        }
        echo '<label> create new</label><br>';
        
        echo '</br></br></br>';
        if($isError2){
            echo '<font color="red">One or multiple informations are not valid (could\'t connect to the SQL Server under the given credentials)!</font></br>';
        }
        if(isset($_POST['SQLServerAdress'])){
            echo '<label>SQL Server: </label><input type="text" name="SQLServerAdress" value="'.$_POST['SQLServerAdress'].'"></input></br>';
        }else{
            echo '<label>SQL Server: </label><input type="text" name="SQLServerAdress" value="localhost"></input></br>';
        }
        echo '</br>';
        if(isset($_POST['SQLServerUsername'])){
            echo '<label>Username: </label><input type="text" name="SQLServerUsername" value="'.$_POST['SQLServerUsername'].'"></input></br>';
        }else{
            echo '<label>Username: </label><input type="text" name="SQLServerUsername"></input></br>';
        }
        if(isset($_POST['SQLServerUserPassword'])){
            echo '<label>Password: </label><input type="password" name="SQLServerUserPassword" value="'.$_POST['SQLServerUserPassword'].'"></input></br>';
        }else{
            echo '<label>Password: </label><input type="password" name="SQLServerUserPassword"></input></br>';
        }
        
        echo '</br>';
        if($cantConnectTODatabase){
            echo '<font color="red">The selected database cant be connected to (or is empty)!</font></br>';
        }
        if(isset($_POST['SQLServerDatabase'])){
            echo '<label>Database: </label><input type="text" name="SQLServerDatabase" value="'.$_POST['SQLServerDatabase'].'"></input></br>';
        }else{
            echo '<label>Database: </label><input type="text" name="SQLServerDatabase"></input></br>';
        }
        if(isset($_POST['SQLServerTablePrefix'])){
            echo '<label>Table prefix: </label><input type="text" name="SQLServerTablePrefix" value="'.$_POST['SQLServerTablePrefix'].'"></input></br>';
        }else{
            echo '<label>Table prefix: </label><input type="text" name="SQLServerTablePrefix"></input></br>';
        }
        echo '<input name="ECADPHPInstallCycle" value="2" hidden><input type="submit" value="next -->">';
        echo '</form>';
        writeHTMLEnd();
    }
    
    
    function ECADPHPInstallCycle2($noUsername, $noPassword, $noPasswordRepeat, $wrongPasswordRepeat){
        writeHTMLHeader();
        echo 'ECAD PHP HUB Installation';
        echo '</br>';
        echo '</br>';
        echo 'Step 2 out of 4 (User)';
        echo '</br></br> Please create an administrator. </br>';
        echo '<form method="POST" action="">';
        
        if($noUsername){
            echo '<font color="red">please enter a username!</font></br>';
        }
        if(isset($_POST['ECADPHPHUBAdministratorUsername'])){
            echo '<label>Username: </label><input type="text" name="ECADPHPHUBAdministratorUsername" value="'.$_POST['ECADPHPHUBAdministratorUsername'].'"></input></br>';
            
        }else{
            echo '<label>Username: </label><input type="text" name="ECADPHPHUBAdministratorUsername"></input></br>';
        }
        if($noPassword){
            echo '<font color="red">please enter a password!</font></br>';
        }
        if($noPasswordRepeat){
            echo '<font color="red">don\'t forget to repeat the password!</font></br>';
        }
        if($wrongPasswordRepeat){
            echo '<font color="red">the repeated password was incorrect</font></br>';
        }
        echo '<label>Password: </label><input type="password" name="ECADPHPHUBAdministratorPassword"></input></br>';
        echo '<label>re-enter password: </label><input type="password" name="ECADPHPHUBAdministratorPassword2"></input></br>';
        
        echo '<input name="ECADPHPInstallCycle" value="3" hidden><input type="submit" value="next -->">';
        //include old form data as hidden

        echo '<input type="text" name="SQLTablesToUse" value="'.$_POST['SQLTablesToUse'].'" hidden>';
        echo '<input type="text" name="SQLServerAdress" value="'.$_POST['SQLServerAdress'].'" hidden>';
        echo '<input type="text" name="SQLServerUsername" value="'.$_POST['SQLServerUsername'].'" hidden>';
        echo '<input type="password" name="SQLServerUserPassword" value="'.$_POST['SQLServerUserPassword'].'" hidden>';
        echo '<input type="text" name="SQLServerDatabase" value="'.$_POST['SQLServerDatabase'].'" hidden>';
        echo '<input type="text" name="SQLServerTablePrefix" value="'.$_POST['SQLServerTablePrefix'].'" hidden>';
        
        
        
        
        
        echo '</form>';
        writeHTMLEnd();
    }
    function ECADPHPInstallCycle3(){
        writeHTMLHeader();
        echo 'ECAD PHP HUB Installation';
        echo '</br>';
        echo '</br>';
        echo 'Step 3 out of 4 (Review)';
        echo '</br> Please review your installation if everything is correct </br>';
        echo '<form method="POST" action="">';
        
        echo '</br></br>SQL Server:</br></br>';
        echo '<label>Installation Type: </label><input type="text" name="SQLTablesToUse" value="'.$_POST['SQLTablesToUse'].'" readonly></br>';
        echo '<label>Server: </label><input type="text" name="SQLServerAdress" value="'.$_POST['SQLServerAdress'].'" readonly></br>';
        echo '<label>Username: </label><input type="text" name="SQLServerUsername" value="'.$_POST['SQLServerUsername'].'" readonly></br>';
        echo '<label>Password: </label><input type="password" name="SQLServerUserPassword" value="'.$_POST['SQLServerUserPassword'].'" readonly></br>';
        echo '<label>Database Name: </label><input type="text" name="SQLServerDatabase" value="'.$_POST['SQLServerDatabase'].'" readonly></input></br>';
        echo '<label>Table prefix:: </label><input type="text" name="SQLServerTablePrefix" value="'.$_POST['SQLServerTablePrefix'].'" readonly></br>';
        
        echo '</br></br>ECAD PHP Hub Admin User:</br>';
        echo '<label>Username: </label><input type="text" name="ECADPHPHUBAdministratorUsername" value="'.$_POST['ECADPHPHUBAdministratorUsername'].'" readonly></input></br>';
        echo '<input type="password" name="ECADPHPHUBAdministratorPassword" value="'.$_POST['ECADPHPHUBAdministratorPassword'].'" hidden></input></br>';
        
        echo '</br>';
        echo '<input type="checkbox" name="deleteInstallerAfterInstallation" value="yes" checked> delete Installer.php after sucessfull installation (recomendet)';
        echo '</br>';
        echo '<input name="ECADPHPInstallCycle" value="4" hidden><input type="submit" value="apply changes and install ">';
        writeHTMLEnd();
    }
    
    
    function deleteInstaller(){
        unlink('installer.php');
    }
    


    function installFromHTMLFormData(){
        $dataFolderName = 'ECADPhpHubData';
        
        if($_POST['SQLTablesToUse'] == 'useExisting'){
            //nothing needs to be done
        }else if($_POST['SQLTablesToUse'] == 'createNew'){
            //create new database
            $mySQLIServer = new mysqli($_POST['SQLServerAdress'], $_POST['SQLServerUsername'],$_POST['SQLServerUserPassword']);
            
            
            if ($mySQLIServer->multi_query('CREATE DATABASE '.mysqli_real_escape_string($mySQLIServer, $_POST['SQLServerDatabase']).' COLLATE utf8_general_ci')) {
                echo '</br> The Database has been created';
                flush();
            }else{
                //error
                echo $mySQLIServer->error;
                echo '</br></br></br></br></br>';
                
                return false;
            }
            
            
            
        }else{
            return false;
        }
        
        $mySQLIServer = new mysqli($_POST['SQLServerAdress'], $_POST['SQLServerUsername'],$_POST['SQLServerUserPassword'],$_POST['SQLServerDatabase']);
        
        if (mysqli_connect_errno()) {
            return false;
        }
        if(!isset($_POST['SQLServerTablePrefix']) or $_POST['SQLServerTablePrefix'] == ""){
            createTables($mySQLIServer, 'A');
        }else{
            createTables($mySQLIServer, $_POST['SQLServerTablePrefix']);
        }
        echo '</br></br>SQL Tables created';
        flush();
        
        createProgramFolders($dataFolderName);
        echo '</br></br>Folders created';
        flush();
        
        createConfigFileFromHTMLFormData($dataFolderName);
        echo '</br></br>config file created';
        flush();
        
        createHtaccessFile($dataFolderName);
        echo '</br></br>htaccess file created (prohibits users from directly accessing the data folder)';
        flush();
        echo '</br></br>';
        
        //reconnect to sql server
        $mySQLIServer = new mysqli($_POST['SQLServerAdress'], $_POST['SQLServerUsername'],$_POST['SQLServerUserPassword'],$_POST['SQLServerDatabase']);
        createAdminUser($mySQLIServer, $_POST['SQLServerTablePrefix'], $_POST['ECADPHPHUBAdministratorUsername'], $_POST['ECADPHPHUBAdministratorPassword']);
        
        flush();
        
        
        echo '</br></br>';
        $mySQLIServer->close();
        return true;
        
    }
    function createHtaccessFile($dataFolderName){
        $htaccess_file = fopen($dataFolderName.'/.htaccess', "w");
        $htaccess_file_Standard = '<Directory ./>'."\r\n".'Order deny,Allow'."\r\n".'Deny from all'."\r\n".'</Directory>';
        fwrite($htaccess_file, $htaccess_file_Standard);
        fclose($htaccess_file);
        
        $htaccess_file = fopen('./.htaccess', "w");
        $htaccess_file_Standard = '<Files "config.php">'."\r\n".'Order deny,Allow'."\r\n".'Deny from all'."\r\n".'</Files>';
        fwrite($htaccess_file, $htaccess_file_Standard);
        fclose($htaccess_file);
    }
    function createAdminUser($mySQLIServer, $tabePrefix, $username, $password){
        $passwordHash= password_hash($password, PASSWORD_BCRYPT);
        
        
        $querie= 'INSERT INTO '.$tabePrefix.'_User (ID, username, UPassword, active, administrator) VALUES ("U.'.getUniqueIdentifier().'", "'.$username.'", "'.$passwordHash.'", true, true);';
        echo $querie;
        
        if ($mySQLIServer->multi_query($querie)) {
            echo '</br></br>created admin user: "'.$username.'"';
            flush();
        }else{
            //error
            echo $mySQLIServer->error;
            echo '</br></br></br></br></br>';
            
            return false;
        }
    }
    
    
    
    
    
    
    
    
?>