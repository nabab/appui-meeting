<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
if (defined('BBN_MEET_DOMAIN')) {
  $ctrl->combo(_("Meeting BBN"), ['domain' => BBN_MEET_DOMAIN]);
}
