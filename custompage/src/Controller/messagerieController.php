<?php
/**
 * @file
 * Contains Drupal\custompage\Controller.
 */

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;
 

class messagerieController extends ControllerBase {

	public function content() {

		$html = '';
 		$uid_current = \Drupal::currentUser()->id();

		$query_uid = \Drupal::database()->select('private_message_thread__members', 'uid'); 
		$query_uid->fields('uid', ['members_target_id', 'entity_id']); 
		$query_uid->condition('uid.members_target_id', $uid_current, '!=');
		$results_uid = $query_uid->execute()->fetchAll();
		$results_uid = array_reverse($results_uid);

 		foreach ($results_uid as $key => $value) {

 			$user = \Drupal\user\Entity\User::load($value->members_target_id);

	 			$query_msg = \Drupal::database()->select('private_messages', 'msg'); 
				$query_msg->fields('msg', ['created', 'owner']);  
				$query_msg->condition('msg.owner', $user->id(), '='); 
				$query_msg->orderBy('created', 'DESC');
				$query_msg->range(0, 1);
				$results_msg = $query_msg->execute()->fetchAll();


		  	$roles = $user->getRoles();
		  	$name = $user->getUsername();  
 			$entity_id = $value->entity_id;
			$date_msg = date('d/m/Y', $results_msg[0]->created);
 

 			$html .= '<div class="fav row">';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3 info title"><p><a href="/user/'.$user->id().'">'. $name .'</a></p></div>';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3 info"><p>Dernier message : '.$date_msg .'</p></div>'; 
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3"><a href="/private_messages/'. $entity_id .'" class="btn-border">'. t('Voir la conversation') .'</a></div>';
			$html .= '</div>';

 		}
 		 

		return array(
	       '#theme' => 'messagerie',
	       '#thread_message' => $html,
    	);
	}



}

?>