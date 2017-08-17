<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

use Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'Realisations Home' Block.
 *
 * @Block(
 *   id = "realisations_home",
 *   admin_label = @Translation("Realisations home block"),
 *   category = @Translation("Realisations Home block")
 * )
 */
class RealisationsHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */



    public function imageStyle($fid, $style) {
        $file = File::load($fid);
        $image_uri = ImageStyle::load($style)->buildUrl($file->getFileUri());
        return $image_uri;
    }

    function create_url($id) {
        $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
        $url = $url->toString();
        return $url;
    }

    public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    $html = '';

  	$query = \Drupal::entityQuery('node')
        ->condition('type', 'realisation') 
        ->range(0, 7)
        ->sort('field_ordre_realisation', 'ASC') 
	    ->condition('status', 1);
	    $nids = $query->execute();

    $html .= '<div class="list row margin-top-45">';
        $counter = 0;

        foreach ($nids as $key => $value) {
            $node_realisation = node_load($value);
            $col = ($counter == 0) ? 'col-md-6' : 'col-md-3';
            $html .= '<div class="col-xs-12 col-sm-6 '. $col .' visuel">';
                $html .= '<div class="overlay color-white">';
                   $html .= ' <div class="wrapp">';
                        $html .= '<h3 class="nom">'. $node_realisation->field_maitriseoeuvre_realisation->value .'<span class="size-3">'. $node_realisation->title->value .'</span></h3>';
                        $html .= '<a href="'. $this->create_url($node_realisation->nid->value) .'" class="btn-border-white">Découvrir la réalisation</a>';
                    $html .= '</div>';
                $html .= '</div>'; 
                if (isset($node_realisation->field_thumbnail_realisation->entity->fid->value)) {
                    $html .= '<div class="image-fit">';
                    $html .= '<img src="'. $this->imageStyle($node_realisation->field_thumbnail_realisation->entity->fid->value, 'resize_550x450') .'" alt="'. $node_realisation->field_thumbnail_realisation->alt .'">';
                    $html .= '</div>';
                }
            $html .= '</div>';     
            $counter++;             
        }

    $html .= '</div>';

    return array(
      '#markup' => $html,
      '#texteee' => 'Lorem upsumLorem upsumLorem upsumLorem upsumLorem upsumLorem upsum',
    );
  }

}


  