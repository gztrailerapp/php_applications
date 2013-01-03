<?php

/**
 * This file contains common functions used throughout the Integration package.
 *
 * @package    Integration
 * @subpackage Config
 * @author     Jonas Colmsjö <jonas.colmsjo@gizur.com>
 * @version    SVN: $Id$
 *
 * @license    Commercial license
 * @copyright  Copyright (c) 2012, Gizur AB, <a href="http://gizur.com">Gizur Consulting</a>, All rights reserved.
 *
 * Coding standards:
 * http://pear.php.net/manual/en/standards.php
 *
 * PHP version 5
 *
 */
/* * *************************************** INTEGRATION DATABASE *********************************** */

/**
 * DNS of database server to use 
 * @global string $dbconfig_integration['db_server']
 */
$dbconfig_integration['db_server'] = 'gizurcloud.colm85rhpnd4.eu-west-1.rds.amazonaws.com';

/**
 * The port of the database server
 * @global string $dbconfig_integration['db_port']       
 */
$dbconfig_integration['db_port'] = ':3306';

/**
 * The usename to use when logging into the database
 * @global string $dbconfig_integration['db_username']  
 */
$dbconfig_integration['db_username'] = 'vtiger_integrati';

/**
 * The password to use when logging into the database
 * @global string $dbconfig_integration['db_password']
 */
$dbconfig_integration['db_password'] = 'ALaXEryCwSFyW5jQ';

/**
 * The name of the database
 * @global string $dbconfig_integration['db_name']
 */
$dbconfig_integration['db_name'] = 'vtiger_integration';

/**
 * The type of database (currently is only mysql supported)
 * @global string $dbconfig_integration['db_type']
 */
$dbconfig_integration['db_type'] = 'mysql';


/* * *************************************** VTIGER DATABASE *********************************** */


/**
 * DNS of database server to use 
 * @global string $dbconfig_vtiger['db_server']
 */
$dbconfig_vtiger['db_server'] = 'gizurcloud.colm85rhpnd4.eu-west-1.rds.amazonaws.com';

/**
 * The port of the database server
 * @global string $dbconfig_vtiger['db_port']       
 */
$dbconfig_vtiger['db_port'] = ':3306';

/**
 * The usename to use when logging into the database
 * @global string $dbconfig_vtiger['db_username']  
 */
$dbconfig_vtiger['db_username'] = 'user_6bd70dc3';

/**
 * The password to use when logging into the database
 * @global string $dbconfig_vtiger['db_password']
 */
$dbconfig_vtiger['db_password'] = 'fbd70dc30c05';

/**
 * The name of the database
 * @global string $dbconfig_vtiger['db_name']
 */
$dbconfig_vtiger['db_name'] = 'vtiger_7cd70dc3';

/**
 * The type of database (currently is only mysql supported)
 * @global string $dbconfig_vtiger['db_type']
 */
$dbconfig_vtiger['db_type'] = 'mysql';



/* * ************************** BATCH CONFIGURATION *************************** */

/**
 *  Set Batch Valiable
 * 
 * 
 */
$dbconfig_batchvaliable['batch_valiable'] = 10;

/* * *************************** FTP CONFIGURATION **************************** */


/**
 *  @FTP Host Name 
 */
$dbconfig_ftphost['Host'] = "int-gc1-dev-server2.developer1.gizurcloud.com";

/**
 *  @FTP User Name 
 */
$dbconfig_ftpuser['User'] = "ftp-dev";


/**
 *  @FTP Password
 */
$dbconfig_ftppassword['Password'] = "DyjX7z3cqYiX";

/**
 *  @FTP Local files path
 */
$dbconfig_ftplocalpath['localpath'] = "cronsetfiles/";

/**
 *  @FTP Server files path
 */
$dbconfig_ftpserverpath['serverpath'] = "/";


/** * ******************* Amazon SQS Configuration ********************** * */
/**
 * Queue URL
 */
$amazonqueue_config['_url'] = 'https://sqs.eu-west-1.amazonaws.com/065717488322/cikab_queue';
/**
 * Amazon Access Key
 */
$amazonqueue_config['_access_key'] = 'AKIAJX43RR2UCVINIL3Q';
/**
 * Amazon Secret Key
 */
$amazonqueue_config['_secret_key'] = '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';
?>