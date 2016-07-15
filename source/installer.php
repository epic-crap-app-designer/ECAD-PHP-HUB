<?php
    error_reporting(-1);
    function createTables($mySQLIServer, $tabePrefix){
        //user table
        $mySQLInstallString = '';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_User (ID VARCHAR(50), Name Text, groupID VARCHAR(50), UPassword VARCHAR(50), email Text, active boolean, confirmedEmail boolean, personalFolderID VARCHAR(50), allowedAmountOfFolders int, allowedAmountOfPages int, AllowedAmountOfMenues int, canChageSystemFolder boolean, canChagePassword boolean, administrator boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Sessions (ID VARCHAR(50), uID VARCHAR(50), IP VARCHAR(50), cookie VARCHAR(50), creationDate date, active boolean, lastSeen date, unusedTimeout time, timeout time); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Pages (ID VARCHAR(50), creatorID VARCHAR(50), creationDate date, isPublic boolean, name VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_PageShares (ID VARCHAR(50), uID VARCHAR(50), administrative boolean, canEdit boolean, canRename boolean, canView boolean, canDeletePage boolean, canAddUsers boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Folders (ID VARCHAR(50), uID VARCHAR(50), creationDate date, public boolean, name  VARCHAR(50)); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_FolderShare (ID VARCHAR(50), uID VARCHAR(50), creationdate date, canView boolean, canDelete boolean, canUpload boolean, canDownload boolean, canRename boolean, canDeleteFolder boolean, canAddUser boolean); ';
        $mySQLInstallString .= 'create table '.$tabePrefix.'_Administrators (auID VARCHAR(50), uID VARCHAR(50), creationdate date); ';
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
                    echo("-----------------\n");
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
?>