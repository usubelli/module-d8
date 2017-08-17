<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Article Home' Block.
 *
 * @Block(
 *   id = "article_home",
 *   admin_label = @Translation("Article home block"),
 *   category = @Translation("Article Home block")
 * )
 */
class ArticleHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
  	
    $html = '';

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
        $node_article = node_load($value);
        $html .= '<div class="col-md-4 actu">';
            $html .= '<div class="date">Le '. date('m/d/Y',$node_article->created->value) .'</div>';
            $html .= '<div class="titre">'. $node_article->title->value .'</div>';
            $html .= '<div class="resume">'. $node_article->body->summary .'</div>';
            $html .= '<a href="'. create_url($node_article->nid->value) .'" class="btn-border">'. t('Lire la suite') .'</a>';                 
        $html .= '</div>';

    }
 

    return array(
      '#markup' => $html,
      '#texteee' => 'Lorem upsumLorem upsumLorem upsumLorem upsumLorem upsumLorem upsum',
    );
  }

}


  