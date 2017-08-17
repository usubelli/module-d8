<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */
namespace Drupal\blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Menu backend' Block.
 *
 * @Block(
 *   id = "menu_backend",
 *   admin_label = @Translation("Menu backend block"),
 *   category = @Translation("Menu backend block")
 * )
 */
class MenuBackendBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
  	
    $html = '';

    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles(); 
    $variables['role'] = $roles[1];
                
    
    $html .= '<div id="menu-client">';
      $admin_context = \Drupal::service('router.admin_context');
      if (!$admin_context->isAdminRoute()) {
        $html .= '<a href="'. base_path() .'admin/content">Administration</a>';
      } else {
        $html .= '<a href="'. base_path() .'">Retour au site</a>';
      }
      $html .= '<ul class="dropdown" id="admin-menu-account">';
          $html .= '<li class="admin-menu-action"><a href="'. base_path() .'user/logout">Se déconnecter</a></li>';
          $html .= '<li class="admin-menu-action admin-menu-account"><a href="'. base_path() .'user">Mon compte</a></li>';
      $html .= '</ul>';
    $html .= '</div>';
  
 

    return array(
      '#markup' => $html,
    );
  }

}


  