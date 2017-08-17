<?php

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

use Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;
 

class realisationsController extends ControllerBase {


	public function imageStyle($fid, $style) {
        $file = File::load($fid);
        $image_uri = ImageStyle::load($style)->buildUrl($file->getFileUri());
        return $image_uri;
    } 

    public function create_url($id) {
	    $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
	    $url = $url->toString();
	    return $url;
    }

	public function content() {
		return array(
	      	'#theme' => 'realisations',
	    );
	}




	public function searchRealisations(Request $request) {
		$search = $_POST['search'];	

		$searchResults = '';
		$query = \Drupal::entityQuery('node')
        ->condition('type', 'realisation')    
        ->condition('status', 1);
		$group = $query->orConditionGroup()
	    ->condition('title', $search, 'CONTAINS') // Title
	    ->condition('field_maitrise_realisation', $search, 'CONTAINS') // Contenu description 
	    ->condition('field_maitriseoeuvre_realisation', $search, 'CONTAINS'); // Designer 
		$nids = $query->condition($group)->execute();
 
	    foreach ($nids as $key => $value) {
	    	$node_realisations = node_load($value);
	            $searchResults .= '<div class="col-xs-12 col-sm-6 col-md-3 visuel">';
	                $searchResults .= '<div class="overlay color-white">';
	                    $searchResults .= '<div class="wrapp">';
	                        $searchResults .= '<h3 class="nom">'. $node_realisations->field_maitriseoeuvre_realisation->value .'<span class="size-3">'. $node_realisations->title->value .'</span></h3>';
	                        $searchResults .= '<a href="'. $this->create_url($node_realisations->nid->value) .'" class="btn-border-white">'. t('Découvrir la réalisation') .'</a>';
	                    $searchResults .= '</div>';
	                $searchResults .= '</div>';
	                if ($node_realisations->field_thumbnail_realisation->entity->fid->value) {
	                	$searchResults .= '<div class="image-fit">';
	                	$searchResults .= '<img src="'. $this->imageStyle($node_realisations->field_thumbnail_realisation->entity->fid->value, 'resize_550x450') .'" alt="'. $node_realisations->field_thumbnail_realisation->alt .'">';
	                	$searchResults .= '</div>';
	                }
	            $searchResults .= '</div>';
	    }

		$return = array(
			'searchResults' => $searchResults, 
		);

		return new JsonResponse($return); 
	}




	public function getRealisations(Request $request) {

		$index = $_POST['index'];
		$range = $_POST['range'];
		$rangeE = explode(',', $range );
 

 		// DISPLAY PRODUCT
 		$listProduits = '';
		$query = \Drupal::entityQuery('node')
        ->condition('type', 'realisation')          
        ->condition('status', 1);

	    $counterProducts = $query->execute();	    
       	if (!$range){
	      	$query->range(0, 2);
       	} else {
      		$query->range(intval($rangeE[0]), intval($rangeE[1]));
       	}
       	$query->sort('field_ordre_realisation', 'ASC');
	    $nids = $query->execute(); 
	    $counterNode = 0;
	    $counter = 0;


	    foreach ($nids as $key => $value) {
	    	if ($counter < 2) { 	
	            $node_realisations = node_load($value);
	            $listProduits .= '<div class="col-xs-12 col-sm-6 col-md-3 visuel">';
	                $listProduits .= '<div class="overlay color-white">';
	                    $listProduits .= '<div class="wrapp">';
	                        $listProduits .= '<h3 class="nom">'. $node_realisations->field_maitriseoeuvre_realisation->value .'<span class="size-3">'. $node_realisations->title->value .'</span></h3>';
	                        $listProduits .= '<a href="'. $this->create_url($node_realisations->nid->value) .'" class="btn-border-white">'. t('Découvrir la réalisation') .'</a>';
	                    $listProduits .= '</div>';
	                $listProduits .= '</div>';
	                if ($node_realisations->field_thumbnail_realisation->entity->fid->value) {
	                	$listProduits .= '<div class="image-fit">';
	                	$listProduits .= '<img src="'. $this->imageStyle($node_realisations->field_thumbnail_realisation->entity->fid->value, 'resize_550x450') .'" alt="'. $node_realisations->field_thumbnail_realisation->alt .'">';
	                	$listProduits .= '</div>';
	                }
	            $listProduits .= '</div>';
	  		}
            $counterNode++;
            $counter++;
      	}


      	// PAGINATION
      	$nbPage = round(count($counterProducts) / 2);
		$rangeStart = 0; 
		$rangeEnd = 2; 

 		$pagination = '';
 		$pagination .= '<div class="item-list">';
			$pagination .= '<ul class="pager">';
				for ($i = 1; $i <= $nbPage; $i++) { 

					$pagination .= '<li class="pager-item"><a title="Aller à la page '.$i.'" href="#" data-range="'.$rangeStart.', '.$rangeEnd.'" >'. $i .'</a></li>';
					$rangeStart += 2; 
					$rangeEnd += 2; 
				}
			$pagination .= '</ul>';
		$pagination .= '</div>';

 


		$return = array(
			'listProduits' =>  $listProduits,  
			'pagination' =>  $pagination, 
			'range' =>  $range,
			'index' =>  $index,
		);

		return new JsonResponse($return);
	}

 

	 
}
 