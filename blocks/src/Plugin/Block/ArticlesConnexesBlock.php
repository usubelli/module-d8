<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Articles Connexes' Block.
 *
 * @Block(
 *   id = "articles_connexes",
 *   admin_label = @Translation("Articles Connexes block"),
 *   category = @Translation("Articles Connexes block")
 * )
 */
class ArticlesConnexesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
  	
    $html = '';
    $node = \Drupal::routeMatch()->getParameter('node');

    function create_url($id) {
      $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
      $url = $url->toString();
      return $url;
    }

  	$query = \Drupal::entityQuery('node')
      ->condition('type', 'article') 
	    ->condition('status', 1);
	  $nids = $query->execute();

    foreach ($nids as $key => $value) {
      if ($value != $node->nid->value){
        $node_article = node_load($value);
        $html .= '<div class="col-md-4 actu">';
            $html .= '<div class="image-fit">';
            $html .= '<img class="" src="'. file_create_url($node_article->field_image_miniature_article->entity->uri->value) .'" alt="'. $node_article->field_image_miniature_article->alt .'">';
            $html .= '</div>';
            $html .= '<div class="date">Le '. date('m/d/Y',$node_article->created->value) .'</div>';
            $html .= '<div class="titre">'. $node_article->title->value .'</div>';
            $html .= '<div class="resume">'. $node_article->body->summary .'</div>';
            $html .= '<a href="'. create_url($node_article->nid->value) .'" class="btn-border">'. t('Lire la suite') .'</a>';                 
        $html .= '</div>';
      }
    }
 

    return array(
      '#markup' => $html,
      '#texteee' => 'Lorem upsumLorem upsumLorem upsumLorem upsumLorem upsumLorem upsum',
    );
  }

}


  