<?php

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node; 
use Drupal\Core\Link;
use Drupal\Core\Url;
 

class favorisController extends ControllerBase {

	public function create_url($id) {
	    $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
	    $url = $url->toString();
	    return $url;
    }
 

	public function content() {
 
		$uid = \Drupal::currentUser()->id(); 

		$query_produit = \Drupal::database()->select('favories', 'fav'); 
		$query_produit->addField('fav', 'entity_id_favories');
		$query_produit->condition('fav.entity_ref_favories', 'produit');
		$query_produit->condition('fav.id_user_favories', $uid);
		$results_produit = $query_produit->execute()->fetchAll();

		$html = '';

		foreach ($results_produit as $key => $value) {
			$node_produit = node_load( $value->entity_id_favories);
			// var_dump($node_produit->field_designer_produit->entity->title->value);die;
			$html .= '<div class="fav row">';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3 info title"><p>'. $node_produit->title->value .'</p></div>';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3 info"><p>'.$node_produit->field_designer_produit->entity->title->value .'</p></div>';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3 info"><p>'. $node_produit->field_famille_produit->entity->name->value .'</p></div>';
				$html .= '<div class="col-xs-12 col-sm-6 col-md-3"><a href="'. $this->create_url($node_produit->nid->value) .'" class="btn-border">'. t('Voir le produit') .'</a></div>';
			$html .= '</div>';
		}
  


		return array(
	       '#theme' => 'favoris',
 	       '#produit_favoris' => $html,
    	);
	}


	public function addLiked(Request $request) {
	 
		$uid = \Drupal::currentUser()->id();
		$query = \Drupal::database()->insert('favories');
		$query->fields([
		  'entity_ref_favories',
		  'entity_id_favories',
		  'id_user_favories'
		]);
		$query->values([
		  $_POST['type'],
		  $_POST['id'],
		  $uid
		]);
		$query->execute();

		$return = array(
			'query' => $query,
		);

		return new JsonResponse($return);
	}



	public function removeLiked(Request $request) {
 
			$uid = \Drupal::currentUser()->id();

			$query = \Drupal::database()->delete('favories');
			$query->condition('entity_ref_favories', $_POST['type']);
			$query->condition('entity_id_favories', $_POST['id']);
			$query->condition('id_user_favories', $uid);
			$query->execute();

			$return = array(
				'query' => $query,
			); 

		return new JsonResponse($return);
	}

}

?>