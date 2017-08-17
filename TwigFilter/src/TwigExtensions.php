<?php
namespace Drupal\TwigFilter;

/**
 * Class DefaultService.
 *
 * @package Drupal\TwigExtensions
 */

class TwigExtensions extends \Twig_Extension {

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'TwigFilter.twig_extension';
  }

  /**
   * Generates a list of all Twig filters that this extension defines.
   */
 public function getFilters() {
    $filters = [
      new \Twig_SimpleFilter('replaceTags', array($this, 'replaceTags'), array('is_safe' => array('html'))),
      new \Twig_SimpleFilter('replaceTags2', array($this, 'replaceTags2'), array('is_safe' => array('html'))),
    ];
 
    return $filters;
  }


  /**
   * Filter Replace tags
   */
  public static function replaceTags($txt) {
    $str = $txt;
    $str = str_replace(array('<p>', '</p>'), array('<span>', '</span>'), $str);
    $str = preg_replace("~<(/)?p>~", "<\\1span>", $str);
    $str = preg_replace("~<p>~", '<span>', $str);
    $str = preg_replace("~</p>~", '</span>', $str);
    return $str;
  }

  public static function replaceTags2($txt) {
    $str = $txt;
    $str = 'Hello '.$txt;
    return $str;
  }

}