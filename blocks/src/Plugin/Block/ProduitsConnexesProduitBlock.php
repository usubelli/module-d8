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
 * Provides a 'Produits Connexes Produit' Block.
 *
 * @Block(
 *   id = "produits_connexes_Produit",
 *   admin_label = @Translation("Produits Connexes Produit"),
 *   category = @Translation("Produits Connexes Produit")
 * )
 */
class ProduitsConnexesProduitBlock extends BlockBase {

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

    $famille = $node->field_famille_produit->entity->name->value;
    $query = \Drupal::entityQuery('node')
        ->condition('type', 'produit') 
        ->condition('field_famille_produit.entity.name.value', $famille, '=' )
        ->range(0, 6)
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
                          $html .= '<div class="image-fit">';
                            $html .= '<img src="'. $this->imageStyle($node_produit->field_thumbnail_produit->entity->fid->value, 'resize_300x180') .'" alt="'. $node_produit->field_thumbnail_produit->alt .'">';
                          $html .= '</div>';
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


  