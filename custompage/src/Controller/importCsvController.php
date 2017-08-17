<?php

namespace Drupal\custompage\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node; 
use \Drupal\file\Entity\File;


class importCsvController extends ControllerBase {

 
	public function content() {
		return array(
	       	'#theme' => 'upload',
    	);
	}

	public function getFile($name, $dir) {
		$content = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/sites/default/files/uploads/'. $name);
		$file = file_save_data($content, "public://". $name ."", FILE_EXISTS_REPLACE);
		return $file;
	}

 
	public function getCsv(Request $request) {

		// Recuperation du fichiers CSV
		$name = $_FILES['myfile']['tmp_name'];
		$destination = $_SERVER["DOCUMENT_ROOT"] . '/sites/default/files/uploads/' . $_FILES['myfile']['name'];


		// Enregistrement du fichier CSV dans le dossier upload
		if( move_uploaded_file($name, $destination) ){
			move_uploaded_file($name, $destination);
		} else {
			var_dump('Erreur...');die;
		}

		// Ouverture et Parse du fichier CSV -> array
		$arrayCSV = null;
		if (($handle = fopen($destination, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$arrayCSV[] = $data;
			}
			fclose($handle);
		}


		// Parcours du tableau pour récuperer chaques champs
		foreach ($arrayCSV as $key => $value) {

			$arrayProduits = explode(';', $value[0]);

			// $Variable
			$title = $arrayProduits[0];
			$famille = $arrayProduits[1];
			$collection = $arrayProduits[2];
			$materiaux = $arrayProduits[3];
			$designer = $arrayProduits[4];
			$ordre = $arrayProduits[5];
			$description = $arrayProduits[6];
			$img_dimensions = $arrayProduits[7];
			$img_slider = $arrayProduits[8];
			$img_slider = $arrayProduits[9];
			$pdf_fiche = $arrayProduits[10];
			$pdf_nuancier = $arrayProduits[11];
			$pdf_transport = $arrayProduits[12];
			$thumbnail = $arrayProduits[13];


			// ********************** SLIDER ********************** //
			// Creation d'un tableau contenant tous les fichiers du repertoire
			$dir = $_SERVER["DOCUMENT_ROOT"] . '/sites/default/files/uploads';
			$array_dir = array();
			if (is_dir($dir)) {
			    if ($dh = opendir($dir)) {
			        while (($file = readdir($dh)) !== false) {
			            array_push($array_dir, $file);
			        }
			        closedir($dh);
			    }
			}

			// Recherche et création d'une liste sous forme de tableau de tous les fichiers contenant le nom de l'image	 
			$searchword = $img_slider;
			$array_img = array();
			foreach($array_dir as $key => $value) {
			    if(preg_match("/\b$searchword\b/i", $value)) {
			        $array_img[$key] = $value;
			    }
			}	

			// Creation du tableau contenant les ID de toutes les images
			$array_slider = array();
			$count = 0;
			foreach ($array_img as $key => $valueImage) {
				$content_slider = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/sites/default/files/uploads/'. $valueImage);
				$file_slider = file_save_data($content_slider, "public://". $valueImage ."", FILE_EXISTS_REPLACE);

				$array_slider[$count] = ['target_id' => $file_slider->id(), 'alt' => $title];
				$count++;
 			} 


 			// ********************** TAXONOMIES ********************** //
			// famille
			$tid_famille = \Drupal::entityTypeManager()
			      ->getStorage('taxonomy_term')
			      ->loadByProperties(['name' => $famille]);
			// collection
			$tid_collection = \Drupal::entityTypeManager()
			      ->getStorage('taxonomy_term')
			      ->loadByProperties(['name' => $famille]);
			// Matériaux
			$tid_materiaux = \Drupal::entityTypeManager()
			      ->getStorage('taxonomy_term')
			      ->loadByProperties(['name' => $famille]);


			// designer
			$query = \Drupal::entityQuery('node')
		        ->condition('type', 'designer') 
		        ->condition('title', $designer);
		    $designer_id = $query->execute();


		    // ********************** FILES ********************** //
 			// img dimensions  
			$file_img_dimensions = $this->getFile($img_dimensions);
			// img thumbnail
			$file_img_thumbnail = $this->getFile($thumbnail); 
			// pdf fiche technique 
			$file_pdf_fiche = $this->getFile($pdf_fiche);
			// pdf nuancier  
			$file_pdf_nuancier = $this->getFile($pdf_nuancier);
			// pdf tarif transport  
			$file_pdf_transport = $this->getFile($pdf_transport); 



			// ********************** CREATE NODE ********************** // 
			$node = Node::create([
		       'type' => 'produit',
		       'title' => $title,
		       'field_ordre_produit' => $ordre,
		       'field_designer_produit' => $designer_id,
		       'field_famille_produit' => $tid_famille,
		       'field_materiaux_produit' => $tid_materiaux,
		       'field_collection_produit' => $tid_collection,
		       'field_contenu_description_prod' => $description, 
		       'field_visuel_dimension_prod' => [ 'target_id' => $file_img_dimensions->id(), 'alt' => $title ],
		       'field_pdf_fiche_technique' => [ 'target_id' => $file_pdf_fiche->id(), 'alt' => $title ],
		       'field_pdf_nuancier' => [ 'target_id' => $file_pdf_nuancier->id(), 'alt' => $title ],
		       'field_pdf_tarif_transport' => [ 'target_id' => $file_pdf_transport->id(), 'alt' => $title ],
		       'field_thumbnail_produit' => [ 'target_id' => $file_img_thumbnail->id(), 'alt' => $title ],
			   'field_slide_produit' => $array_slider,
			]);  
			$node->save();

		}	
		 	

		$return = array(
			'query' => $arrayProduits,
		);

		return new JsonResponse($return);
	}

}

 

?>

