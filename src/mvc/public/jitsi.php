<?php
/*
 * Describe what it does!
 *
 * @var bbn\Mvc\Controller $ctrl 
 *
 */
if (defined('BBN_MEET_DOMAIN')) {
  $ctrl->combo(_("Meeting BBN"), ['domain' => BBN_MEET_DOMAIN]);
}
