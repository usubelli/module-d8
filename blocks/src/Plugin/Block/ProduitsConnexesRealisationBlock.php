<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;


/**
 * Provides a 'Produits Connexes Realisation' Block.
 *
 * @Block(
 *   id = "produits_connexes_realisation",
 *   admin_label = @Translation("Produits Connexes Realisation"),
 *   category = @Translation("Produits Connexes Realisation")
 * )
 */
class ProduitsConnexesRealisationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

   public function imageStyle($fid, $style) {
        $file = File::load($fid);
        $image_uri = ImageStyle::load($style)->buildUrl($file->getFileUri());
        return $image_uri;
    }

  public function build() {
  	
    $html = '';
    $node = \Drupal::routeMatch()->getParameter('node');

    function create_url($id) {
      $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
      $url = $url->toString();
      return $url;
    }
 

  	$query = \Drupal::entityQuery('node')
      ->condition('type', 'realisation') 
	    ->condition('status', 1);
	  $nids = $query->execute();
    


    foreach ($nids as $key => $value) {
        if( $node->nid->value != $value ) {
 
            $node_realisation = node_load($value);
            $html .= '<div class="visuel col-xs-12 col-sm-6 col-md-4 col-lg-3">';
                $html .= '<div class="wrapp">';
                    $html .= '<div class="overlay color-white">';
                        $html .= '<div class="wrapp">';
                            $html .= '<h3 class="nom"><span class="size-3">'. $node_realisation->title->value .'</span></h3>';
                            $html .= '<a href="'. create_url($node_realisation->nid->value) .'" class="btn-border-white">'. t('Découvrir la réalisation') .'</a>';
                        $html .= '</div>';
                    $html .= '</div>'; 
                    if ($node_realisation->field_thumbnail_realisation->entity->fid->value) {
                        $html .= '<div class="image-fit">';
                            $html .= '<img src="'. $this->imageStyle($node_realisation->field_thumbnail_realisation->entity->fid->value, 'resize_300x180') .'" alt="'. $node_realisation->field_thumbnail_realisation->alt .'">';
                         $html .= '</div>';
                    }
                $html .= '</div>';
            $html .= '</div>';

        }
    }
 

    return array(
      '#markup' => $html,
    );
  }

}


  