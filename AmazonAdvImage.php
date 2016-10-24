<?php
namespace AmazonAdv;

/**
* AmazonAdvItems contain image informations of product.
* AmazonAdvItems->URL    : URL of the picture
* AmazonAdvItems->Width  : Width of the picture
* AmazonAdvItems->Height : Height of the picture
*
* @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
* @version  1.0
* @access   public
*/
class AmazonAdvImage {
    public $URL = '';
    public $Width = '';
    public $Height = '';
    /**
    * Create an instance of AmazonAdvItem with a SimpleXMLElement object. (->Items->ImageSets->ImageSet->(.*))
    *
    * @param SimpleXMLElement $XML
    * @return AmazonAdvImage
    */
    public function createWithXml($XML) {
        $image = new AmazonAdvImage();
        $image->URL = (string) $XML->URL;
        $image->Width = (int) $XML->Height;
        $image->Height = (int) $XML->Width;

        return $image;
    }

    public function __toString() {
        return 'AmazonAdvImage';
    }
}
?>