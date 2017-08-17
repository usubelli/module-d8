<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Menu Collection' Block.
 *
 * @Block(
 *   id = "menu_collection",
 *   admin_label = @Translation("Menu Collection block"),
 *   category = @Translation("Menu Collection block")
 * )
 */
class MenuCollectionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
  	
    $html = '';

    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "famille");
    $tids = $query->execute();
    $host = \Drupal::request()->getHost(); 
    $html .= '<ul class="menu dropdown-menu collection-menu">'; 
    $html .= '<li><a href="/les-collections">Tous</a></li>'; 
        foreach ($tids as $tid => $value) {
            $term_object = taxonomy_term_load($value); 
            $name = $term_object->get('name')->value;
            $html .= '<li><a href="/les-collections?tid='.$tid.'">'. $name .'</a></li>';
        }
    $html .= '</ul>'; 


    return array(
      '#markup' => $html
    );
  }

}
 


  