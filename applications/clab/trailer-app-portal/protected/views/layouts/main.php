<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container" id="page">

	<div id="header">
<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
		<?php
		 $user = Yii::app()->session['username'];
		 
		 if(!empty($user))
		 {
		 ?>
   <div style="float:right"> Welcome &nbsp;
	<?php echo CHtml::encode($user); ?></div>
   <?php } ?>
	</div><!-- header -->

	<div id="mainmenu">
		<?php
		 /*
		 if(empty($user))
		 {
		  $returnUrl=Yii::app()->homeUrl;
		  $this->redirect($returnUrl);
		  exit;
		  }
		  */ 
		if(!empty($user))
		{
		 $loginstatus=0;
		 } else { $loginstatus=1; }
		 
		?>
		<?php
		if($user!='Guest' && !empty($user)){ $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Survey', 'url'=>array('/troubleticket/')),
				array('label'=>'Damage Report', 'url'=>array('/troubleticket/damage/')),
				array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>$loginstatus),
				array('label'=>'Logout' , 'url'=>array('/site/logout'), 'visible'=>!$loginstatus)
			),
		)); }?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>