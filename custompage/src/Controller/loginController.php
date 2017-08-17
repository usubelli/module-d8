<?php
/**
 * @file
 * Contains Drupal\custompage\Controller.
 */

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;


class loginController extends ControllerBase {
  
	public function content() {
		$form_login = \Drupal::formBuilder()->getForm('Drupal\user\Form\UserLoginForm');
		//$form_register = \Drupal::formBuilder()->getForm('Drupal\user\RegisterForm');
		$entity = \Drupal::entityManager()
		  ->getStorage('user')
		  ->create(array());

		$formObject = \Drupal::entityManager()
		  ->getFormObject('user', 'register')
		  ->setEntity($entity);

		$form_register = \Drupal::formBuilder()->getForm($formObject);
		return array(
	      '#theme' => 'login',
	      '#user_login_form' => $form_login,
	      '#register_form' => $form_register,
	    );
	} 
 
}
 