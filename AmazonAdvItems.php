<?php
namespace AmazonAdv;

/**
* AmazonAdvItems contain the result of AmazonAdv request.
* AmazonAdvItems->TotalResults         : Number of products that Amazon returns
* AmazonAdvItems->TotalPages           : Number of pages of products that Amazon returns
* AmazonAdvItems->MoreSearchResultsUrl : URL of Amazon page which contain more products
* AmazonAdvItems->Items                : Array of AmazonAdvItem objects
* 
* @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
* @version  1.0
* @access   public
*/
class AmazonAdvItems {
    public $TotalResults = '';
    public $TotalPages = '';
    public $MoreSearchResultsUrl = '';
    public $Items = array();
    
    /**
    * Create an instance of AmazonAdvItems with a SimpleXMLElement object.
    * 
    * @param SimpleXMLElement $XML
    * @return AmazonAdvItems
    */
    public static function createWithXml($XML) {
        
        $AmazonItems = new AmazonAdvItems();
        
        $XML = $XML->Items;
        
        if(isset($XML->TotalResults))
        $AmazonItems->TotalResults = (int) $XML->TotalResults;
        if(isset($XML->TotalPages))
        $AmazonItems->TotalPages = (int) $XML->TotalPages;
        if(isset($XML->MoreSearchResultsUrl))
        $AmazonItems->MoreSearchResultsUrl = (string) $XML->MoreSearchResultsUrl;
        
        foreach($XML->Item as $XMLItem)
        $AmazonItems->Items[] = AmazonAdvItem::createWithXml($XMLItem);
        
        return $AmazonItems;
    }
    
    public function __toString() {
        return 'AmazonAdvItems';
    }
}
?>