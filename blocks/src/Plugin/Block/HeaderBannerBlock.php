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
 * Provides a 'Header Banner' Block.
 *
 * @Block(
 *   id = "header_banner_block",
 *   admin_label = @Translation("Header Banner Block"),
 *   category = @Translation("Header Banner Block")
 * )
 */
class HeaderBannerBlock extends BlockBase {

    /**
        * {@inheritdoc}
    */

    public function build() {

        /* FUNCTION IMAGE STYLE */
        function imageStyle($fid, $style) {
            $file = File::load($fid);
            $image_uri = ImageStyle::load($style)->buildUrl($file->getFileUri());
            return $image_uri;
        };

        $node = \Drupal::routeMatch()->getParameter('node');
        $html = ''; 
        $currentPath = \Drupal::service('path.current')->getPath(); 

        // NODE
        if($node) {
            $banner = node_load($node->nid->value);
             
            switch ( $node->getType() ){

                case 'realisation':
                    $html .= '<div class="slider">';
                        foreach ($banner->field_slide_realisation as $key => $value) { 
                            $html .= '<div class="item image-fit"><img src="'. imageStyle($value->entity->fid->value, 'slider_page') .'" alt="'. $value->alt .'"></div>';  
                        }
                    $html .= '</div>';
                    $html .= '<div class="arrows produit"></div>';
                    break;
                    
                case 'produit':
                     $html .= '<div class="slider">';
                        foreach ($banner->field_slide_produit as $key => $value) { 
                            $html .= '<div class="item image-fit"><img src="'. imageStyle($value->entity->fid->value, 'slider_page') .'" alt="'. $value->alt .'"></div>';  
                        }
                    $html .= '</div>';
                    $html .= '<div class="arrows produit"></div>';
                    break;

                case 'designer':
                    $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_background_header_designer->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_background_header_designer->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

                case 'article':
                    $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_image_background_article->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_image_background_article->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

                case 'faq':
                     $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_arriere_plan_header_faq->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_arriere_plan_header_faq->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

                case 'showroom':
                     $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_showroom->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_showroom->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

                case 'page_unique':
                     $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_page->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_page->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

                case 'webform':
                     $html .= '<div class="banner-small">'; 
                        $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_contact->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_contact->alt .'"></div>';  
                    $html .= '</div>'; 
                    break;

            }


        // CUSTOM PAGE
        } elseif ( $currentPath == '/les-collections') {
            $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_produits->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_produits->alt .'" title="'. $banner->field_bg_header_produits->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        } elseif ( $currentPath == '/nos-realisations') {
            $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_realisations->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_realisations->alt .'" title="'. $banner->field_bg_header_realisations->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        } elseif ( $currentPath == '/revendeurs') {
            $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_revendeurs->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_blog->alt .'" title="'. $banner->field_bg_header_revendeurs->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        // VIEWS BLOG
        } elseif ( $currentPath == '/blog') {
            $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_blog->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_blog->alt .'" title="'. $banner->field_bg_header_blog->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        } elseif ( $currentPath == '/les-createurs') {
            $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_designers->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_designers->alt .'" title="'. $banner->field_bg_header_designers->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        } elseif ( $currentPath == '/favoris') {
            $banner = node_load(9);
            $html .= '<div class="banner-xs">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_favoris->entity->fid->value, 'resize_1920x300') .'" alt="'. $banner->field_bg_header_favoris->alt .'" title="'. $banner->field_bg_header_favoris->alt .'" >';
                $html .= '</div>';
            $html .= '</div>'; 

        } elseif ( $currentPath == '/user-login') {
                        $banner = node_load(9);
            $html .= '<div class="banner-small">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_login->entity->fid->value, 'resize_1920x520') .'" alt="'. $banner->field_bg_header_login->alt .'" title="'. $banner->field_bg_header_login->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';

        } elseif ( $currentPath == '/messagerie') {
            $banner = node_load(9);
            $html .= '<div class="banner-xs">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_login->entity->fid->value, 'resize_1920x300') .'" alt="'. $banner->field_bg_header_login->alt .'" title="'. $banner->field_bg_header_login->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';
            
        } elseif ( strpos($currentPath, 'user') != false) {
            $banner = node_load(9);
            $html .= '<div class="banner-xs">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_login->entity->fid->value, 'resize_1920x300') .'" alt="'. $banner->field_bg_header_login->alt .'" title="'. $banner->field_bg_header_login->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';
        
        }  elseif ( strpos($currentPath, 'private_messages') != false) {
            $banner = node_load(9);
            $html .= '<div class="banner-xs">';
                $html .= '<div class="item image-fit"><img src="'. imageStyle($banner->field_bg_header_login->entity->fid->value, 'resize_1920x300') .'" alt="'. $banner->field_bg_header_login->alt .'" title="'. $banner->field_bg_header_login->alt .'" >';
                $html .= '</div>';
            $html .= '</div>';
        }
        
            

    // Block appelé dans structure de bloc et non dans le template !

    return array(
        '#markup' => $html,
    );
  }

}


  