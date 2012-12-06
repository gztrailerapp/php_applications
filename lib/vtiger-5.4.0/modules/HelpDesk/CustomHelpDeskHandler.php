<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
//error_reporting(E_ALL);
require_once 'modules/Emails/mail.php';
require_once 'modules/HelpDesk/HelpDesk.php';

class CustomHelpDeskHandler extends VTEventHandler
{

    function handleEvent($eventName, $entityData)
    {
        global $log, $adb;
        $log->debug("IN CustomHelpDeskHandler");
        if ($eventName == 'vtiger.entity.aftersave' && $entityData->isNew()) {

            $log->debug("IN CustomHelpDeskHandler :: vtiger.entity.aftersave with isNew");
            $moduleName = $entityData->getModuleName();
            if ($moduleName == 'HelpDesk') {
                $ticketId = $entityData->getId();
                //Increase/Decrease
                $cf_642 = $entityData->focus->column_fields['cf_642'];
                //Requested Date
                $cf_644 = $entityData->focus->column_fields['cf_644'];
                //Product Quantity
                $cf_645 = $entityData->focus->column_fields['cf_645'];
                //Product Id
                $product_id = $entityData->focus->column_fields['product_id'];

                //Validate ticket fields before executing the code.
                if (!empty($product_id) &&
                    in_array($cf_642, array('Increase', 'Decrease')) &&
                    !empty($cf_644) && !empty($cf_645)) {

                    $log->debug("IN CustomHelpDeskHandler :: calling _operationDecreaseOrIncrease($ticketId, $product_id, $cf_645, $cf_642, 1);");
                    /* If request is for Decrease */
                    $this->_operationDecreaseOrIncrease($ticketId, $product_id, $cf_645, $cf_642, 1);
                }
            }
        }
    }

    function _operationDecreaseOrIncrease($ticketId, $product_id, $cf_645, $type, $count = 1)
    {
        global $log, $adb;
        $log->debug("IN _operationDecreaseOrIncrease($ticketId, $product_id, $cf_645, $type, $count);");
        /*
         * GET THE FIRST TICKET WITH INCREASE REQUEST
         * FOR SAME PRODUCT.
         */
        if ($type == 'Increase')
            $result = $this->getFirstTicketByProductId($product_id, 'Decrease');
        else
            $result = $this->getFirstTicketByProductId($product_id, 'Increase');
        /*
         * FETCH THE RECORDS TO MATCH 
         */
        $log->debug('Fetching pr');
        $quantity = $result->fields['cf_645'];
        if (!empty($quantity)) {
            /*
             * IF QUANTITY EQUALS TO THE ORDERED QUANTITY 
             */
            if ($quantity == $cf_645) {
                $this->closeTroubleTicket($result->fields['ticketid']);
                /*
                 * CLOSING THE CURRENT CREATED TICKET 
                 */
                $this->closeTroubleTicket($ticketId);
            } elseif ($quantity > $cf_645) {
                /* CLOSING THE FETCHED TICKET */
                $this->closeTroubleTicket($result->fields['ticketid']);
                $new_quantity = $quantity - $cf_645;
                /* CLOSING THE CURRENT CREATED TICKET */
                $this->closeTroubleTicket($ticketId);
                /* CREATE A NEW TICKET WITH NEW QUANTITY */
                $this->cloneATicketWithNewQuantity($result->fields['ticketid'], $new_quantity);
            } elseif ($quantity < $cf_645) {
                /*
                 * THIS CONDITION WILL CALLED WHEN REQUESTED DECREASE QUANTITY IS 
                 * GREATER THAN THE FETCHED TICKET QUANTITY.
                 */
                $this->closeTroubleTicket($result->fields['ticketid']);
                /*
                 * CLOSING THE CURRENT CREATED TICKET 
                 */
                $this->closeTroubleTicket($ticketId);
                /*
                 * NOW RE-CALL THE SAME FUNCTION TO MATCH FOR
                 * THE BALANCE QUANTITY.
                 */
                $new_quantity = $cf_645 - $quantity;
                $this->_operationDecreaseOrIncrease($ticketId, $product_id, $new_quantity, $type, ++$count);
            }
        } else {
            if ($count > 1) {
                /* CREATE A NEW TICKET WITH NEW QUANTITY */
                $this->cloneATicketWithNewQuantity($ticketId, $cf_645);
            }
        }
    }

    function _operationIncrease($ticketId, $product_id, $cf_645, $count = 1)
    {
        global $log, $adb;
        /*
         * GET THE FIRST TICKET WITH INCREASE REQUEST
         * FOR SAME PRODUCT.
         */
        $result = $this->getFirstTicketByProductId($product_id, 'Increase');
        /*
         * FETCH THE RECORDS TO MATCH 
         */
        $quantity = $result->fields['cf_645'];
        if (!empty($quantity)) {
            /*
             * IF QUANTITY EQUALS TO THE ORDERED QUANTITY 
             */
            if ($quantity == $cf_645) {
                $this->closeTroubleTicket($result->fields['ticketid']);
                /*
                 * CLOSING THE CURRENT CREATED TICKET 
                 */
                $this->closeTroubleTicket($ticketId);
            } elseif ($quantity > $cf_645) {
                $this->closeTroubleTicket($result->fields['ticketid']);
                $new_quantity = $quantity - $cf_645;
                $this->closeTroubleTicket($ticketId);
                /* CREATE A NEW TICKET WITH NEW QUANTITY */
                $this->cloneATicketWithNewQuantity($result->fields['ticketid'], $new_quantity);
            } elseif ($quantity < $cf_645) {
                /*
                 * THIS CONDITION WILL CALLED WHEN REQUESTED INCREASE QUANTITY IS 
                 * GREATER THAN THE FETCHED TICKET QUANTITY.
                 */
                $this->closeTroubleTicket($result->fields['ticketid']);
                /*
                 * CLOSING THE CURRENT CREATED TICKET 
                 */
                $this->closeTroubleTicket($ticketId);
                /*
                 * NOW RE-CALL THE SAME FUNCTION TO MATCH FOR
                 * THE BALANCE QUANTITY.
                 */
                if (($cf_645 - $quantity) > 0)
                    $this->_operationIncrease($ticketId, $product_id, ($cf_645 - $quantity));
            } else {
                $new_quantity = 0;
                if (!empty($quantity)) {
                    $this->closeTroubleTicket($result->fields['ticketid']);
                    $new_quantity = $quantity - $cf_645;
                } else {
                    $new_quantity = $cf_645;
                }
                /*
                 * CLOSING THE CURRENT CREATED TICKET 
                 */
                $this->closeTroubleTicket($ticketId);
                /*
                 * CREATE A NEW TICKET WITH BALANCE QUANTITY 
                 */
                if (!empty($new_quantity))
                    $this->cloneATicketWithNewQuantity($result, $new_quantity);
            }
        }else {
            
        }
    }

    function closeTroubleTicket($id)
    {
        global $log, $adb;
        $sql = "UPDATE vtiger_troubletickets 
            SET vtiger_troubletickets.status = 'Closed'
            WHERE ticketid = ?";
        $result = $adb->pquery($sql, array($id));
        if ($result) {
            $log->debug("CLOSED TICKET : $id");
            return true;
        } else {
            $log->debug("FAILED CLOSING TICKET : $id");
            return false;
        }
    }

    function cloneATicketWithNewQuantity($ticketId, $new_quantity)
    {
        global $log, $adb;
        $log->debug("CLONING TICKET : $ticketId WITH QUANTITY $new_quantity.");

        $_REQUEST['file'] = 'ListView';

        $ticket = $this->getTicketByTicketId($ticketId);

        $new_ticket = new HelpDesk();

        $new_ticket->column_fields['ticket_no'] = NULL;
        $new_ticket->column_fields['assigned_user_id'] = $ticket->fields['smownerid'];
        $new_ticket->column_fields['parent_id'] = $ticket->fields['parent_id'];
        $new_ticket->column_fields['ticketpriorities'] = $ticket->fields['priority'];
        $new_ticket->column_fields['product_id'] = $ticket->fields['product_id'];
        $new_ticket->column_fields['ticketseverities'] = $ticket->fields['severity'];
        $new_ticket->column_fields['ticketstatus'] = 'Open';
        $new_ticket->column_fields['ticketcategories'] = $ticket->fields['category'];
        $new_ticket->column_fields['update_log'] = $ticket->fields['update_log'];
        $new_ticket->column_fields['hours'] = $ticket->fields['hours'];
        $new_ticket->column_fields['days'] = $ticket->fields['days'];
        $new_ticket->column_fields['createdtime'] = ''; //$ticket->fields['createdtime'];
        $new_ticket->column_fields['modifiedtime'] = ''; //$ticket->fields['modifiedtime'];
        $new_ticket->column_fields['ticket_title'] = $ticket->fields['title'];
        $new_ticket->column_fields['description'] = $ticket->fields['description'];
        $new_ticket->column_fields['solution'] = $ticket->fields['solution'];
        $new_ticket->column_fields['comments'] = 'Auto creted by system.';
        $new_ticket->column_fields['modifiedby'] = $ticket->fields['modifiedby'];
        $new_ticket->column_fields['from_portal'] = $ticket->fields['from_portal'];
        $new_ticket->column_fields['cf_642'] = $ticket->fields['cf_642'];
        $new_ticket->column_fields['cf_644'] = $ticket->fields['cf_644'];
        $new_ticket->column_fields['cf_645'] = $new_quantity;


        $new_ticket->save("HelpDesk");
        $log->debug("Cloned Ticket $ticketId with ID : " . $new_ticket->id);
    }

    function getFirstTicketByProductId($product_id, $type)
    {
        global $log, $adb;
        $log->debug("Fetch Ticket with product id : $product_id and Type : $type");
        $query = "SELECT 
                    vtiger_crmentity.*,
                    vtiger_troubletickets.*,
                    vtiger_ticketcf.*
                FROM
                    vtiger_troubletickets
                        INNER JOIN
                    vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
                        INNER JOIN
                    vtiger_crmentity ON vtiger_troubletickets.ticketid = vtiger_crmentity.crmid
                        LEFT JOIN
                    vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
                        LEFT JOIN
                    vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid
                WHERE
                    vtiger_crmentity.deleted = 0 AND 
                    vtiger_troubletickets.ticketid > 0 AND
                    vtiger_troubletickets.status = 'Open' AND
                    vtiger_troubletickets.product_id = ? AND 
                    vtiger_troubletickets.product_id IS NOT NULL AND
                    vtiger_ticketcf.cf_642 IN ( ? ) AND
                    vtiger_ticketcf.cf_645 > 0
                ORDER BY vtiger_crmentity.createdtime ASC LIMIT 1";
        return $result = $adb->pquery($query, array($product_id, $type));
    }

    function getTicketByTicketId($id)
    {
        global $log, $adb;
        $log->debug("Fetch Ticket id : $id");
        $query = "SELECT 
                    vtiger_crmentity.*,
                    vtiger_troubletickets.*,
                    vtiger_ticketcf.*
                FROM
                    vtiger_troubletickets
                        INNER JOIN
                    vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid
                        INNER JOIN
                    vtiger_crmentity ON vtiger_troubletickets.ticketid = vtiger_crmentity.crmid
                        LEFT JOIN
                    vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
                        LEFT JOIN
                    vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid
                WHERE
                    vtiger_crmentity.deleted = 0 AND 
                    vtiger_troubletickets.ticketid = ?";
        return $result = $adb->pquery($query, array($id));
    }

}

?>