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
 * Provides a 'Slider Home' Block.
 *
 * @Block(
 *   id = "slider_home",
 *   admin_label = @Translation("Slider home block"),
 *   category = @Translation("Slider Home block")
 * )
 */
class SliderHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
 
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

    public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    $html = '';
   

    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "famille");
    $tids = $query->execute();
     
    $html .= '<div class="container">';
        $html .= '<div>';
            $html .= '<nav class="left-menu">';
                $html .= '<ul>'; 
                    foreach ($tids as $tid => $value) {
                        $term_object = taxonomy_term_load($value); 
                        $name = $term_object->get('name')->value;
                        $html .= '<li><a href="les-collections?tid='.$tid.'">'. $name .'</a></li>';
                    }
                $html .= '</ul>';
                $html .= '<div class="arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></div>';
            $html .= '</nav>';
        $html .= '</div>';
    $html .= '</div>';



   
    $query = \Drupal::entityQuery('node')
        ->condition('type', 'produit') 
        ->range(0, 6)
        ->sort('field_ordre_realisation', 'ASC') 
        ->condition('field_statut_slider_home', 1, "=")
        ->condition('status', 1);
        $nids = $query->execute();

    $html .= '<div class="slider-home">';
        foreach ($nids as $key => $value) {
            $node_produit = node_load($value); 
            $html .= '<div class="item">';
            $html .= '<div class="image-fit">';
                $html .= '<img src="'. $this->imageStyle($node_produit->field_visuel_slider_home->entity->fid->value, 'resize_1920x1000') .'" alt="'. $node_produit->field_visuel_slider_home->alt .'">';
                $html .= '</div>';
                $html .= '<div class="desc montserrat">';
                    $html .= '<div class="titre color-orange uppercase site-6">'. $node_produit->title->value .'</div>';
                    $html .= '<div class="sous-titre">Design - '. $node_produit->field_designer_produit->entity->title->value .'</div>';
                    $html .= '<a href="'. $this->create_url($node_produit->nid->value) .'" class="btn-border-orange right">Voir le projet</a>';
                $html .= '</div>';
            $html .= '</div>';
        }
    $html .= '</div>';
    $html .= '<div class="arrows"></div>';

    return array(
      '#markup' => $html,
    );
  }

}


  