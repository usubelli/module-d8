<?php
/**
 * @file
 * Contains Drupal\custompage\Controller.
 */

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;


class revendeursController extends ControllerBase {
  	
	public function displayRevendeurs($map) {

    	$query = \Drupal::entityQuery('node')
	        ->condition('type', 'revendeur')
	        ->sort('field_ordre_vendeur', 'ASC') 
	        ->condition('status', 1);
	    $nids = $query->execute();

	    if ($map) { 

	    	$mapRevendeur = array();
	    	foreach ($nids as $key => $value) {  
	            $node_revendeur = node_load($value);   

	            $geocode = explode("(", $node_revendeur->field_geofield->value);
	            $geocode = explode(")", $geocode[1]);
	            $geocode = explode(" ", $geocode[0]);
	            $lon = $geocode[0];
	            $lat = $geocode[1];

		    	$array = array(
					'nid' => $node_revendeur->nid->value, 
					'title' => $node_revendeur->title->value, 
					'telephone' => $node_revendeur->field_telephone_vendeur->value, 
					'fax' => $node_revendeur->field_fax_vendeur->value, 
					'email' => $node_revendeur->field_email_vendeur->value, 
					'siteweb' => $node_revendeur->field_site_web_vendeur->value,
					'postal_code' => $node_revendeur->field_adresse_vendeur->postal_code,
					'country_code' => $node_revendeur->field_adresse_vendeur->country_code,
					'address_line1' => $node_revendeur->field_adresse_vendeur->address_line1,
					'locality' => $node_revendeur->field_adresse_vendeur->locality,
					'siteweb' => $node_revendeur->field_adresse_vendeur->field_site_web_vendeur,
					'lon' => $lon,
					'lat' => $lat,
				);
	    		array_push($mapRevendeur, $array);
	        }
	        // var_dump($mapRevendeur);die;
	    	return $mapRevendeur;

	    } else {

			$listRevendeur = '';
		    foreach ($nids as $key => $value) {  
	            $node_revendeur = node_load($value);
	            $listRevendeur .= '<div class="info-revendeur">';
                    $listRevendeur .= '<div class="container">';
			            $listRevendeur .= '<div class="row">';
			                $listRevendeur .= '<div class="col-md-3 col-sm-6 col-xs-12">';
			                    $listRevendeur .= '<h3 class="montserrat size-4 name">'. $node_revendeur->title->value .'</h3>'; 
			                $listRevendeur .= '</div>';
			                $listRevendeur .= '<div class="col-md-3 col-sm-6 col-xs-12">';
			                    $listRevendeur .= '<ul>';
			                        $listRevendeur .= '<li><span class="ico ico-adress"></span><div class="infos">'. $node_revendeur->field_adresse_vendeur->address_line1 .'<br />'. $node_revendeur->field_adresse_vendeur->postal_code .' '. $node_revendeur->field_adresse_vendeur->locality .'</div></li>';
			                        $listRevendeur .= '<li><span class="ico ico-tel"></span><div class="infos">'. $node_revendeur->field_telephone_vendeur->value .'</div></li>'; 
			                    $listRevendeur .= '</ul>';
			                $listRevendeur .= '</div>';
			                $listRevendeur .= '<div class="col-md-3 col-sm-6 col-xs-12 mail">';
			                    $listRevendeur .= '<ul>';
			                        $listRevendeur .= '<li><span class="ico ico-mail"></span><div class="infos">'. $node_revendeur->field_email_vendeur->value .'</div></li>';
			                        $listRevendeur .= '<li><span class="ico ico-website"></span><div class="infos">'. $node_revendeur->field_site_web_vendeur->value .'</div></li>';
			                    $listRevendeur .= '</ul>';
			                $listRevendeur .= '</div>';
			                $listRevendeur .= '<div class="col-md-3 col-sm-6 col-xs-12">';
			                    $listRevendeur .= '<a href="#" class="btn btn-border">Voir le revendeur <i class="fa fa-angle-right"></i></a>';  
			                $listRevendeur .= '</div>';
			            $listRevendeur .= '</div>';
			    	$listRevendeur .= '</div>';
                $listRevendeur .= '</div>';
	      	}
			return $listRevendeur;
	    }
	}


	public function franceMap() {

		$query = \Drupal::entityQuery('node')
	        ->condition('type', 'revendeur');
	        // ->condition('status', 1);
	    $nids = $query->execute();
 
    	$mapDepartement = array();

    	foreach ($nids as $key => $value) {  
            $node_revendeur = node_load($value);  
             
        	foreach ($node_revendeur->field_departement as $key => $valueDept) {
		    	$array = array(
		    		'nid' => $value, 
					'color' => $node_revendeur->field_color_map->color,
					'title' => $node_revendeur->title->value,
				);
    			$mapDepartement[$valueDept->value] = $array;
	    	} 
        }  
        var_dump($mapDepartement);die;
        return $mapDepartement;
	}



	public function colorDept() {

		$query = \Drupal::entityQuery('node')
	        ->condition('type', 'revendeur');
	        // ->condition('status', 1);
	    $nids = $query->execute();
 
    	$colorDept = '';

    	foreach ($nids as $key => $value) {  
            $node_revendeur = node_load($value);  
          	
        	foreach ($node_revendeur->field_departement as $key => $valueDept) {
				$colorDept .= '"'. $valueDept->value .'" : "'. $node_revendeur->field_color_map->color .'",';
	    	} 
        }  
        // var_dump($colorDept);die;
        return $colorDept;
	}


	public function nameRevendeur() {

		$query = \Drupal::entityQuery('node')
	        ->condition('type', 'revendeur');
	        // ->condition('status', 1);
	    $nids = $query->execute();
 
    	$nameRevendeur = '';

    	foreach ($nids as $key => $value) {  
            $node_revendeur = node_load($value);  
       

        	foreach ($node_revendeur->field_departement as $key => $valueDept) { 
				$nameRevendeur .= '"'. $valueDept->value .'" : "'. $node_revendeur->title->value .'",';
				// $nameRevendeur .= '"'. $valueDept->value .'" : "<span>'. $node_revendeur->title->value .'</span> <br/> '. $node_revendeur->field_adresse_vendeur->address_line1 .' <br/> '. $node_revendeur->field_adresse_vendeur->locality .' - '. $node_revendeur->field_adresse_vendeur->postal_code .' ",'; 
	    	} 
        }   
        return $nameRevendeur;
	}


	public function content() {
 		
		return array(
	      	'#theme' => 'revendeurs',
	      	'#revendeursList' =>  $this->displayRevendeurs(false), 
	      	'#mapList' =>  $this->displayRevendeurs(true), 
	      	'#colorDept' =>  $this->colorDept(), 
	      	'#nameRevendeur' =>  $this->nameRevendeur(), 
	      	// '#mapDepartement' =>  $this->franceMap(), 
	    );
	} 
 
}
 