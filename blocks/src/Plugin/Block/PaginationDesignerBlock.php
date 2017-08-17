<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Pagination Designer' Block.
 *
 * @Block(
 *   id = "pagination_Designer",
 *   admin_label = @Translation("Pagination Designer"),
 *   category = @Translation("Pagination Designer")
 * )
 */
class PaginationDesignerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
    
    $html = '';
    $node = \Drupal::routeMatch()->getParameter('node');
 

    $nid = $node->nid->value; 
    $query = \Drupal::entityQuery('node')
        ->condition('type', 'designer')  
        ->sort('field_ordre_designer', 'ASC') 
        ->condition('status', 1);
    $result = $query->execute();

    $firstNid = reset($result);  
    $LastNid = end($result); 
    
    $prev = "";
    $next = "";
    $find = false;
  

    foreach ($result as $value) {
        if ($find) {
            $next = $value;
            break;                                    
        }          
        if ($node->nid->value == $value){
            $find = true;
        }
        if (!$find){
            $prev = $value;
        }
    }      

    $nodeFirst = node_load($firstNid);
    $nodeLast = node_load($LastNid);
    $nodePrev = node_load($prev);
    $nodeNext = node_load($next);

     if($nodePrev && $nodeNext){
            $html .= '<a href="'. create_url($prev) .'" class="slick-prev slick-arrow"><i class="fa fa-angle-left" aria-hidden="true"></i> '. $nodePrev->title->value .'</a>';
            $html .= '<a href="'. create_url($next) .'" class="slick-next slick-arrow">'. $nodeNext->title->value .' <i class="fa fa-angle-right" aria-hidden="true"></i></a>';
      } else if (!$nodePrev) {
            $html .= '<a href="'. create_url($LastNid) .'" class="slick-prev slick-arrow"><i class="fa fa-angle-left" aria-hidden="true"></i> '. $nodeLast->title->value .'</a>';
            $html .= '<a href="'. create_url($next) .'" class="slick-next slick-arrow">'. $nodeNext->title->value .' <i class="fa fa-angle-right" aria-hidden="true"></i></a>';
      } else if (!$nodeNext){
            $html .= '<a href="'. create_url($prev) .'" class="slick-prev slick-arrow"><i class="fa fa-angle-left" aria-hidden="true"></i> '. $nodePrev->title->value .'</a>';
            $html .= '<a href="'. create_url($firstNid) .'" class="slick-next slick-arrow">'. $nodeFirst->title->value .' <i class="fa fa-angle-right" aria-hidden="true"></i> </a>';
      }

    return array(
      '#markup' => $html,
    );
  }

}


  