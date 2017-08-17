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

class produitsController extends ControllerBase {


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
	      	'#theme' => 'produits',
	    );
	}


	public function searchProduits(Request $request) {
		$search = $_POST['search'];	

		$searchResults = '';
		$query = \Drupal::entityQuery('node')
        ->condition('type', 'produit')    
        ->condition('status', 1);
		$group = $query->orConditionGroup()
	    ->condition('title', $search, 'CONTAINS') // Title
	    ->condition('field_contenu_description_prod', $search, 'CONTAINS') // Contenu description 
	    ->condition('field_designer_produit.entity.title.value', $search, 'CONTAINS'); // Designer
	    // ->condition('field_collection_produit.entity.name', $search, 'CONTAINS' ) // Taxo Collection
	    // ->condition('field_materiaux_produit.entity.name', $search, 'CONTAINS' ) // Taxo Materiaux
	    // ->condition('field_famille_produit.entity.name', $search, 'CONTAINS' ); // Taxo Famille
		$nids = $query->condition($group)->execute();
 
	    foreach ($nids as $key => $value) {
	    	$node_produit = node_load($value);
	            $searchResults .= '<div class="col-xs-12 col-sm-6 col-md-3 visuel">';
	                $searchResults .= '<div class="overlay color-white">';
	                    $searchResults .= '<div class="wrapp">';
	                        $searchResults .= '<h3 class="nom">'. $node_produit->field_famille_produit->entity->name->value .'<span class="size-3">'. $node_produit->title->value .'</span></h3>';
	                        $searchResults .= '<a href="'. $this->create_url($node_produit->nid->value) .'" class="btn-border-white">'. t('Découvrir le produit') .'</a>';
	                    $searchResults .= '</div>';
	                $searchResults .= '</div>';
	                if ($node_produit->field_thumbnail_produit->entity->fid->value) {
	                	$searchResults .= '<div class="image-fit">';
	                	$searchResults .= '<img src="'. $this->imageStyle($node_produit->field_thumbnail_produit->entity->fid->value, 'resize_550x450') .'" alt="'. $node_produit->field_thumbnail_produit->alt .'">';
	                	$searchResults .= '</div>';
	                }
	            $searchResults .= '</div>';
	    }


		$filterList = $this->filterInit();
		$filterListCollection = $filterList[1];
		$filterListMateriaux = $filterList[2];
		$filterListFamille = $filterList[0];
 

		$return = array(
			'searchResults' => $searchResults, 
			'filterListMateriaux' =>  $filterListMateriaux, 
			'filterListCollection' =>  $filterListCollection, 
			'filterListFamille' =>  $filterListFamille, 
		);

		return new JsonResponse($return);
	}




	public function getProduits(Request $request) {

		$tidFamille = $_POST['tidFamille'];
		$tidCollection = $_POST['tidCollection'];
		$tidMateriaux = $_POST['tidMateriaux'];
		$taxonomie = $_POST['taxonomie'];
		$index = $_POST['index'];
		$range = $_POST['range'];
		$rangeE = explode(',', $range );


		// DISPLAY TITLE
		if ($tidFamille != 'all') {  
	    	$titleProduct = taxonomy_term_load($tidFamille)->get('name')->value;
       	} else if ($tidCollection != 'all') { 
        	$titleProduct = taxonomy_term_load($tidCollection)->get('name')->value;
       	} else if ($tidMateriaux != 'all') {
        	 $titleProduct = taxonomy_term_load($tidMateriaux)->get('name')->value;
       	}

 		// DISPLAY PRODUCT
 		$listProduits = '';
		$query = \Drupal::entityQuery('node')
        ->condition('type', 'produit')    
        ->condition('status', 1);
        if ($tidFamille != 'all') {
        	$query->condition('field_famille_produit.entity', $tidFamille, '=' );	
       	}
       	if ($tidCollection != 'all') {
        	$query->condition('field_collection_produit.entity', $tidCollection, '=' );	
       	}
       	if ($tidMateriaux != 'all') {
        	$query->condition('field_materiaux_produit.entity', $tidMateriaux, '=' );
       	}
	    $counterProducts = $query->execute();

       	if (!$range){
	      	$query->range(0, 2);
       	} else {
      		$query->range(intval($rangeE[0]), intval($rangeE[1]));
       	}
       	$query->sort('field_ordre_produit', 'ASC');
	    $nids = $query->execute();
	    $counterNode = 0;
	    $counter = 0;

	    foreach ($nids as $key => $value) {
	    	if ($counter < 2) { 	
	            $node_produit = node_load($value);
	            $listProduits .= '<div class="col-xs-12 col-sm-6 col-md-3 visuel">';
	                $listProduits .= '<div class="overlay color-white">';
	                    $listProduits .= '<div class="wrapp">';
	                        $listProduits .= '<h3 class="nom">'. $node_produit->field_famille_produit->entity->name->value .'<span class="size-3">'. $node_produit->title->value .'</span></h3>';
	                        $listProduits .= '<a href="'. $this->create_url($node_produit->nid->value) .'" class="btn-border-white">'. t('Découvrir le produit') .'</a>';
	                    $listProduits .= '</div>';
	                $listProduits .= '</div>';
	                if ($node_produit->field_thumbnail_produit->entity->fid->value) {
	                	$listProduits .= '<div class="image-fit">';
	                	$listProduits .= '<img src="'. $this->imageStyle($node_produit->field_thumbnail_produit->entity->fid->value, 'resize_550x450') .'" alt="'. $node_produit->field_thumbnail_produit->alt .'">';
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



    	// FILTER
      	$filterListCollection = NULL;
      	$filterListFamille = NULL;
      	$filterListMateriaux = NULL;

        if ($taxonomie == 'materiaux'){
			$filterList = $this->filterUpdate('materiaux', $tidMateriaux, $tidMateriaux, $tidCollection, $tidFamille);
			$filterListCollection = $filterList[0];
			$filterListFamille = $filterList[1];
		}
		if ($taxonomie == 'collection'){
        	$filterList = $this->filterUpdate('collection', $tidCollection, $tidMateriaux, $tidCollection, $tidFamille);
			$filterListMateriaux = $filterList[0];
			$filterListFamille = $filterList[1];
		} 
		if ($taxonomie == 'famille'){
        	$filterList = $this->filterUpdate('famille', $tidFamille, $tidMateriaux, $tidCollection, $tidFamille);
			$filterListCollection = $filterList[0];
			$filterListMateriaux = $filterList[1];
		}
		if($taxonomie == NUll) {
			$filterList = $this->filterInit();
			$filterListCollection = $filterList[1];
			$filterListMateriaux = $filterList[2];
			$filterListFamille = $filterList[0];
		}


		$return = array(
			'listProduits' =>  $listProduits, 
			'filterListMateriaux' =>  $filterListMateriaux, 
			'filterListCollection' =>  $filterListCollection, 
			'filterListFamille' =>  $filterListFamille, 
			'pagination' =>  $pagination, 
			'titleProduct' => $titleProduct,
			'range' =>  $range,
			'index' =>  $index,
		);

		return new JsonResponse($return);
	}

 

	public function filterUpdate($taxonomie, $tid, $tidMateriaux, $tidCollection, $tidFamille) {
		$selected1 = '';
		$selected2 = '';
		$query = \Drupal::entityQuery('node')
	        ->condition('type', 'produit');
	        if ($taxonomie == 'materiaux'){
	        	if($tid != "all") $query->condition('field_materiaux_produit.entity.tid.value', $tid, '=' );
	        	else {
	        		$selected1 = 'selected="selected"';
	        		$selected2 = 'selected="selected"';
	        	}
	        	$taxo1 = "collection";
	        	$taxo2 = "famille";
			}
			if ($taxonomie == 'collection'){
	        	if($tid != "all") $query->condition('field_collection_produit.entity.tid.value', $tid, '=' );
	        	else {
	        		$selected1 = 'selected="selected"';
	        		$selected2 = 'selected="selected"';
	        	}	
	        	$taxo1 = "materiaux";
	        	$taxo2 = "famille";
			}
			if ($taxonomie == 'famille'){
	        	if($tid != "all") $query->condition('field_famille_produit.entity.tid.value', $tid, '=' );
	        	else {
	        		$selected1 = 'selected="selected"';
	        		$selected2 = 'selected="selected"';
	        	}
	        	$taxo1 = "collection";
	        	$taxo2 = "materiaux";
			}
	    $query->condition('status', 1);
	    $nids = $query->execute();

	    $filterX = '';
	    $filterX .= '<option ' . $selected1 .' value="all">Filtrer par '.$taxo1.' ...</option>';
	    $filterY = '';
	    $filterY .= '<option ' . $selected2 .' value="all">Filtrer par '.$taxo2.' ...</option>';

	    $return_taxo1 = array();
	    $return_taxo2 = array();

	    foreach ($nids as $key => $value) {
	    	$node_produit = node_load($value);
	    	if($taxo1 == "materiaux") {
	    		foreach ($node_produit->get('field_materiaux_produit')->getValue() as $key => $value) {
	    			$term_object = taxonomy_term_load($value['target_id']);
	    			$return_taxo1[$value['target_id']] = $term_object->get('name')->value;
	    		}
	    	}
	    	if($taxo1 == "collection") {
	    		$term_object = taxonomy_term_load($node_produit->field_collection_produit->entity->tid->value);
	    		$return_taxo1[$node_produit->field_collection_produit->entity->tid->value] = $term_object->get('name')->value;
	    	} 
	    	if($taxo1 == "famille") {
	    		$term_object = taxonomy_term_load($node_produit->field_famille_produit->entity->tid->value);
	    		$return_taxo1[$node_produit->field_famille_produit->entity->tid->value] = $term_object->get('name')->value;
	    	} 
	    	if($taxo2 == "materiaux") {
	    		foreach ($node_produit->get('field_materiaux_produit')->getValue() as $key => $value) {
	    			$term_object = taxonomy_term_load($value['target_id']);
	    			$return_taxo2[$value['target_id']] = $term_object->get('name')->value;
	    		}
	    	} 
	    	if($taxo2 == "collection") {
	    		$term_object = taxonomy_term_load($node_produit->field_collection_produit->entity->tid->value);
	    		$return_taxo2[$node_produit->field_collection_produit->entity->tid->value] = $term_object->get('name')->value;
	    	} 
	    	if($taxo2 == "famille") {
	    		$term_object = taxonomy_term_load($node_produit->field_famille_produit->entity->tid->value);
	    		$return_taxo2[$node_produit->field_famille_produit->entity->tid->value] = $term_object->get('name')->value;
	    	} 
	    }
	    $selected = '';
	  	foreach ($return_taxo1 as $key => $value) {
	    	$selected = '';
	  		if($taxo1 == 'materiaux' && $key == $tidMateriaux) $selected = 'selected="selected"';
	  		if($taxo1 == 'collection' && $key == $tidCollection) $selected = 'selected="selected"';
	  		if($taxo1 == 'famille' && $key == $tidFamille) $selected = 'selected="selected"';
	  		$filterX .= '<option ' . $selected . ' value="'.$key.'">'.$value.'</option>';
	  		// var_dump($taxo1 == 'Materiaux' && $key == $tidMateriaux);
	  		// var_dump('tidMateriaux : ' . $tidMateriaux);
	  		// var_dump('tidColleciton : ' . $tidCollection);
	  		// var_dump('tidFamille : ' . $tidFamille);
	  		// var_dump('key : ' . $key);
	  		// var_dump('taxo1 : ' . $taxo1);
	  	} //die;
	  	foreach ($return_taxo2 as $key => $value) {
	    	$selected = '';
	  		if($taxo2 == 'materiaux' && $key == $tidMateriaux) $selected = 'selected="selected"';
	  		if($taxo2 == 'collection' && $key == $tidCollection) $selected = 'selected="selected"';
	  		if($taxo2 == 'famille' && $key == $tidFamille) $selected = 'selected="selected"';
	  		$filterY .= '<option ' . $selected . ' value="'.$key.'" >'.$value.'</option>';
	  	}
	  
		$return = array($filterX, $filterY);
	    return $return;
	}

	public function filterInit() {
		$query = \Drupal::entityQuery('node')
	        ->condition('type', 'produit')
	        ->condition('status', 1);
	    $nids = $query->execute();

	    $filterX = '';
	    $filterX .= '<option value="all">Filtrer par famille ...</option>';
	    $filterY = '';
	    $filterY .= '<option value="all">Filtrer par  collection ...</option>';
	    $filterZ = '';
	    $filterZ .= '<option value="all">Filtrer par  materiaux...</option>';

	    $return_taxo1 = array();
	    $return_taxo2 = array();
	    $return_taxo3 = array();

	    foreach ($nids as $key => $value) {
	    	$node_produit = node_load($value);
	    	foreach ($node_produit->get('field_materiaux_produit')->getValue() as $key => $value) {
    			$term_object = taxonomy_term_load($value['target_id']);
    			$return_taxo3[$value['target_id']] = $term_object->get('name')->value;
	    	}
    		$term_object = taxonomy_term_load($node_produit->field_collection_produit->entity->tid->value);
    		$return_taxo2[$node_produit->field_collection_produit->entity->tid->value] = $term_object->get('name')->value;
    		$term_object = taxonomy_term_load($node_produit->field_famille_produit->entity->tid->value);
    		$return_taxo1[$node_produit->field_famille_produit->entity->tid->value] = $term_object->get('name')->value;
	    }

	  	foreach ($return_taxo1 as $key => $value) {
	  		$filterX .= '<option value="'.$key.'">'.$value.'</option>';
	  	}
	  	foreach ($return_taxo2 as $key => $value) {
	  		$filterY .= '<option value="'.$key.'" >'.$value.'</option>';
	  	}
	  	foreach ($return_taxo3 as $key => $value) {
	  		$filterZ .= '<option value="'.$key.'" >'.$value.'</option>';
	  	}
	  
		$return = array($filterX, $filterY, $filterZ);

	    return $return;
	}
}
 