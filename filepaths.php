<?php
//Define the base directory for the project
define('BASE_DIR', __DIR__);

//database connection
define('connect_php', BASE_DIR . '/connect.php');

//Define the paths for different directories

//directories under auth folder --------------------------------------
//auth folder
define('auth_dir', BASE_DIR . '/auth');


//data Privacy folder
define('dataPriv_dir', auth_dir . '/dataPriv');

//emailing folder
define('emailing_dir', auth_dir . '/emailing');

//landing folder
define('landing_dir', auth_dir . '/landing');

//login folder
define('log_in_dir', auth_dir . '/log_in');

//OTP verification folder
define('otpVerify_dir', auth_dir . '/otpVerify');

//sign out folder
define('sign_out_dir', auth_dir . '/sign_out');

//sign up folder
define('sign_up_dir', auth_dir . '/sign_up');

//----------------------------------------------------------------------


//directories under generalComponents folder ---------------------------
//generalComponents folder
define('generalComponents_dir', BASE_DIR . '/generalComponents');

//header folder
define('header_dir', generalComponents_dir . '/header');

//pdfViewer folder
define('pdfViewer_dir', generalComponents_dir . '/pdfViewer');

//taskManager folder
define('taskManager_dir', generalComponents_dir . '/taskManager');

//----------------------------------------------------------------------


//directories under genMsg folder ----------------------------------
//genMsg folder
define('genMsg_dir', BASE_DIR . '/genMsg');

//----------------------------------------------------------------------


//directories under QADSpecificComponents folder ---------------------
//QADSpecificComponents folder
define('QADSpecificComponents_dir', BASE_DIR . '/QADSpecificComponents');

//QADMain folder
define('QADMain_dir', QADSpecificComponents_dir . '/QADMain');

//----------------------------------------------------------------------


//directories under QAPSpecificComponents folder ---------------------
//QAPSpecificComponents folder
define('QAPSpecificComponents_dir', BASE_DIR . '/QAPSpecificComponents');

//QAPMain folder
define('QAPMain_dir', QAPSpecificComponents_dir . '/QAPMain');

//----------------------------------------------------------------------


//directories under staffSpecificComponents folder ---------------------
//staffSpecificComponents folder
define('staffSpecificComponents_dir', BASE_DIR . '/staffSpecificComponents');

//staffMain folder
define('staffMain_dir', staffSpecificComponents_dir . '/staffMain');

//----------------------------------------------------------------------

//directory of files folder---------------------------------------------
define('files_dir', BASE_DIR . '/files');
//----------------------------------------------------------------------

?>