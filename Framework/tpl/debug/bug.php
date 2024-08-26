<?php

session_start();

if (isset($_SESSION['bug_content']))
{
   echo $_SESSION['bug_content'];
   unset($_SESSION['bug_content']);
}

?>
