<?php

/**
 * 
 * 
 * Created date : 04/07/2012
 * Created By : Anil Singh
 * @author Anil Singh <anil-singh@essindia.co.in>
 * Flow : The basic flow of this page is Create new trouble tickets.
 * Modify date : 27/04/2012
 */
class TroubleticketController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actionindex() {
        $module = "HelpDesk";
        $tickettype = "Survey";
        $model = new Troubleticket;
        $this->LoginCheck();
        $records = $model->findAll($module, $tickettype);
        $this->render('surveylist', array('model' => $model, 'result' => $records));
    }

    /**
     * This Action are display all Trouble Ticket base Record 
     */
    public function actionsurveylist() {
        $module = "HelpDesk";
        $tickettype = "all";
        $model = new Troubleticket;
        $this->LoginCheck();
        $Asset_List = $model->findAssets('Assets');
        $Asset_List = array("0" => "--All Trailers--") + $Asset_List;
        $records = $model->findAll($module, $tickettype, date("Y"), date("m"), '0');
        //$assetstatus = $model->findById('Assets', $firstkey);
        $this->render('surveylist', array('model' => $model, 'result' => $records, 'Assets' => $Asset_List));
    }

    /**
     * This Action are create new Trouble Ticket 
     */
    public function actionsurvey() {
        $model = new Troubleticket;
        $this->LoginCheck();
        if (isset($_POST['submit'])) {
            $model->Save($_POST['Troubleticket']);
        }
        $pickList_sealed = $model->getpickList('sealed');
        $pickList_category = $model->getpickList('ticketcategories');
        $pickList_damagetype = $model->getpickList('damagetype');
        $pickList_damagepostion = $model->getpickList('damageposition');
        $picklist_drivercauseddamage = $model->getpickList('drivercauseddamage');
        $picklist_reportdamage = $model->getpickList('reportdamage');
        $picklist_ticketstatus = $model->getpickList('ticketstatus');
        $Asset_List = $model->findAssets('Assets');
        $postdata = @$_POST['Troubleticket'];
        $this->render('survey', array('model' => $model, 'Sealed' => $pickList_sealed, 'category' => $pickList_category,
            'damagetype' => $pickList_damagetype, 'damagepos' => $pickList_damagepostion,
            'drivercauseddamageList' => $picklist_drivercauseddamage,
            'reportdamage' => $picklist_reportdamage, 'Assets' => $Asset_List,
            'ticketstatus' => $picklist_ticketstatus, 'postdata' => $postdata));
    }

    /* This Action are Filter Ajax base Record */

    public function actionsurveysearch() {
        $module = "HelpDesk";
        $year = $_POST['year'];
        $month = $_POST['month'];
        $trailer = $_POST['trailer'];
        if ($trailer == "--All Trailers--")
            $trailer = "0";
        $model = new Troubleticket;
        $this->LoginCheck();
        $records = $model->findAll($module, 'all', $year, $month, $trailer);
        $Asset_List = $model->findAssets('Assets');
        $Asset_List = array("0" => "--All Trailers--") + $Asset_List;
        $assetstatus = $model->findById('Assets', $trailer);
        $this->renderPartial('surveylist', array('model' => $model, 'result' => $records, 
            'Assets' => $Asset_List, 'currentasset' => $assetstatus, 
            'TR' => $_POST['trailer'], 'SYear' => $year, 'SMonth' => $month));
    }

    /**
     * This Action are display releted Trouble Ticket details depand on trouble ticket ID 
     */
    public function actionsurveydetails() {
        $model = new Troubleticket;
        $this->LoginCheck();
        $module = "HelpDesk";
        $urlquerystring = $_SERVER['QUERY_STRING'];
        $paraArr = explode("/", $urlquerystring);
        $ticketId = $paraArr['2'];
        $storedata = $model->findById($module, $ticketId);
        $this->render('surveydetails', array('result' => $storedata));
    }

    /*
     *  Change Mark damage required function
     */

    public function actionmarkdamagestatus() {
        $model = new Troubleticket;
        $this->LoginCheck();
        $module = "HelpDesk";
        $ticketID = $_POST['ticketid'];
        $storedata = $model->Markdamagerequired($module, $ticketID);
        echo $storedata['result']['ticketstatus'];
        //$this->render('surveydetails',array('result'=>$storedata));  
    }

    /**
     * This is the action to handle images.
     */
    public function actionimages() {
        $module = "DocumentAttachments";
        $urlquerystring = $_SERVER['QUERY_STRING'];
        $paraArr = explode("/", $urlquerystring);
        $ticketId = $paraArr['2'];
        $model = new Troubleticket;
        $imagedata = $model->getimage($module, $ticketId);
        header("Content-Type: image/jpeg");
        header("Content-Disposition: inline;filename=" . $imagedata['result']['filename']);
        echo base64_decode($imagedata['result'][filecontent]);
        die;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * This Action are check logged user. otherwise redirect to login poage  
     */
    public function LoginCheck() {
        $protocol = 'http://';
        if (Yii::app()->request->isSecureConnection)
            $protocol = 'https://';
        $servername = Yii::app()->request->getServerName();          
        $user = Yii::app()->session['username'];
        if (empty($user)) {
            $returnUrl = $protocol . $servername . Yii::app()->homeUrl;
            $this->redirect($returnUrl);
        }
    }

    /**
     * This Action are Update Asset Status on click on inprocess operation  
     */
    function actionchangeassets() {
        $model = new Troubleticket;
        $this->LoginCheck();
        $tickettype = $_POST['tickettype'];
        $currentasset = $_POST['trailer'];
        $records = $model->ChangeAssetStatus($tickettype, $currentasset);

        if ($records['success']) {
            echo "Successfully Changed.";
        } else {
            echo "UnSuccessfully Changed.";
        }
        //$this->render('surveylist', array('msg'=>$records));
    }

}

?>
