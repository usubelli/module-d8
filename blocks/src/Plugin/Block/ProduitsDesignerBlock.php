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
 * Provides a 'Produits Designer' Block.
 *
 * @Block(
 *   id = "produits_Designer",
 *   admin_label = @Translation("Produits Designer"),
 *   category = @Translation("Produits Designer")
 * )
 */
class ProduitsDesignerBlock extends BlockBase {

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

    $nid = $node->nid->value; 
    $query = \Drupal::entityQuery('node')
        ->condition('type', 'produit') 
        ->condition('field_designer_produit', $nid, '=' )
        ->condition('status', 1);
    $nids = $query->execute();


    $html .= '<div class="slider-produit">';  
      foreach ($nids as $key => $value) {
          if( $node->nid->value != $value ) {
   
              $node_produit = node_load($value);
              $html .= '<div class="visuel item col-xs-12 col-sm-6 col-md-4 col-lg-3">';
                  $html .= '<div class="wrapp">';
                      $html .= '<div class="overlay color-white">';
                          $html .= '<div class="wrapp">';
                              $html .= '<h3 class="nom"><span class="size-3">'. $node_produit->title->value .'</span></h3>';
                              $html .= '<a href="'. create_url($node_produit->nid->value) .'" class="btn-border-white">'. t('Découvrir le produit') .'</a>';
                          $html .= '</div>';
                      $html .= '</div>';
                      if ($node_produit->field_thumbnail_produit->entity->fid->value) {
                          $html .= '<img src="'. $this->imageStyle($node_produit->field_thumbnail_produit->entity->fid->value, 'resize_550x450') .'" alt="'. $node_produit->field_thumbnail_produit->alt .'">';
                      }
                  $html .= '</div>';
              $html .= '</div>'; 

          }
      }
    $html .= '</div>';

    return array(
      '#markup' => $html,
    );
  }

}


  