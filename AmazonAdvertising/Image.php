<?php

namespace AmazonAdvertising;

/**
 * AmazonAdvertising\Image contain image informations of product.
 * AmazonAdvertising\Image->URL    : URL of the picture
 * AmazonAdvertising\Image->Width  : Width of the picture
 * AmazonAdvertising\Image->Height : Height of the picture
 *
 * @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
 * @version  1.0
 * @access   public
 */
class Image
{
    public $URL    = '';
    public $Width  = '';
    public $Height = '';

    /**
     * Create an instance of AmazonAdvertising\Image with a \SimpleXMLElement object. (->Items->ImageSets->ImageSet->(.*))
     *
     * @param \SimpleXMLElement $XML
     * @return Image
     */
    public static function createWithXml($XML)
    {
        $image = new Image();
        $image->URL = (string)$XML->URL;
        $image->Width = (int)$XML->Height;
        $image->Height = (int)$XML->Width;

        return $image;
    }
}