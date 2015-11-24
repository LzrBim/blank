<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Newsletter/modal_email_test.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

$content  = $form->hidden('newsletterID', $newsletter->getId());
$content .= $form->input('email', 'Email Address', '', array('required' => true));

$adminView->displayModal('emailNewsletterBlockModal', 'Email A Newsletter Test', $content, 'ajax/emailNewsletterTest.php');