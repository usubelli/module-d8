<?php
namespace Drupal\TwigFilter;


use Drupal\node\Entity\Node; 
use Drupal\Core\Url; 

class LoadNode extends \Twig_Extension {

    /**
    * Generates a list of all Twig functions that this extension defines.
    */
    public function getFunctions(){
        return array(
          new \Twig_SimpleFunction('loadNode', array($this, 'loadNode'), array('is_safe' => array('html'))),
        );
    }

     
    public function getName() {
        return 'twig_extender.twig.loadNode';
    }
 
    public static function loadNode($nid) {
       $LoadNode = \Drupal\node\Entity\Node::load($nid);
       return $LoadNode; 
    }
    
}