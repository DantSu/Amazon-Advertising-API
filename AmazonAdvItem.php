<?php
namespace AmazonAdv;

/**
* AmazonAdvItem contain one Amazon product.
* AmazonAdvItem->Author       : Author of the product (Exemple : J.K. Rowling...)
* AmazonAdvItem->Creator      : Creator of the product (Exemple : Nom Prénom, Nom Prénom....)
* AmazonAdvItem->Brand        : Brand of the product (Exemple : Nathan, Ubisoft...)
* AmazonAdvItem->Manufacturer : Manufacturer (Exemple : Ubisoft, EA Games...)
* AmazonAdvItem->ProductGroup : Product Group (Exemple : DVD, BOOKS...)
* AmazonAdvItem->Title        : Title of the product (Iron Man, The Lord Of The Ring : The fellowship of the ring, ...)
* AmazonAdvItem->URL          : URL of the Amazon page of the product (http://www.amazon.com/...)
* AmazonAdvItem->Binding      : Binding of Books (Exemple : Paperback (Broché en francais)) 
* AmazonAdvItem->Price        : Price of the product in cents (divide by 100 for get the reel price) (Exemple : 6550 = 65.50$)
* AmazonAdvItem->CurrencyCode : Contain the currency use by the price (EUR, USD, ...)
* 
* @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
* @version  1.0
* @access   public
*/
class AmazonAdvItem {
    public $Author = '';
    public $Creator = '';
    public $Brand = '';
    public $Manufacturer = '';
    public $ProductGroup = '';
    public $Title = '';
    public $URL = '';
    public $Binding = '';
    public $Price = '';
    public $CurrencyCode = '';
    private $Images = array();
    
    const IMAGE_SWATCH     = 'SwatchImage';
    const IMAGE_SMALL      = 'SmallImage';
    const IMAGE_THUMBNAIL  = 'ThumbnailImage';
    const IMAGE_TINY       = 'TinyImage';
    const IMAGE_MEDIUM     = 'MediumImage';
    const IMAGE_LARGE      = 'LargeImage';
    
    const CURRENCY_EURO = 'EUR';
    const CURRENCY_USD  = 'USD';
    const CURRENCY_GPB  = 'GPB';
    const CURRENCY_JPY  = 'JPY';
    
    /**
    * Create an instance of AmazonAdvItem with a SimpleXMLElement object. (->Items)
    * 
    * @param SimpleXMLElement $XML
    * @return AmazonAdvItems
    */
    public static function createWithXml($XML) {
        $ItemAttrubutes = $XML->ItemAttributes;
        
        $AmazonItem = new AmazonAdvItem();
        if(isset($ItemAttrubutes->Manufacturer))
        $AmazonItem->Manufacturer = (string) $ItemAttrubutes->Manufacturer;
        if(isset($ItemAttrubutes->Binding))
        $AmazonItem->Binding = (string) $ItemAttrubutes->Binding;
        if(isset($ItemAttrubutes->ProductGroup))
        $AmazonItem->ProductGroup = (string) $ItemAttrubutes->ProductGroup;
        if(isset($ItemAttrubutes->Title))
        $AmazonItem->Title = (string) $ItemAttrubutes->Title;
        if(isset($XML->DetailPageURL))
        $AmazonItem->URL = (string) $XML->DetailPageURL;
        if(isset($ItemAttrubutes->ListPrice->Amount))
        $AmazonItem->Price = (int) $ItemAttrubutes->ListPrice->Amount;
        if(isset($ItemAttrubutes->ListPrice->CurrencyCode))
        $AmazonItem->CurrencyCode = (string) $ItemAttrubutes->ListPrice->CurrencyCode;
        
        
        if(isset($ItemAttrubutes->Brand))
        {
            $AmazonItem->Brand = '';
            foreach($ItemAttrubutes->Brand as $auth)
            $AmazonItem->Brand .= ', '. (string) $auth;
            $AmazonItem->Brand = substr($AmazonItem->Brand, 2);
        }
        if(isset($ItemAttrubutes->Author))
        {
            $AmazonItem->Author = '';
            foreach($ItemAttrubutes->Author as $auth)
            $AmazonItem->Author .= ', '. (string) $auth;
            $AmazonItem->Author = substr($AmazonItem->Author, 2);
        }
        if(isset($ItemAttrubutes->Creator))
        {
            $AmazonItem->Creator = '';
            foreach($ItemAttrubutes->Creator as $auth)
            $AmazonItem->Creator .= ', '. (string) $auth;
            $AmazonItem->Creator = substr($AmazonItem->Creator, 2);
        }
        
        $AmazonImageSet = $XML->ImageSets->ImageSet;
        if(isset($XML->ImageSets->ImageSet->SwatchImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_SWATCH] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->SwatchImage);
        if(isset($XML->ImageSets->ImageSet->SmallImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_SMALL] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->SmallImage);
        if(isset($XML->ImageSets->ImageSet->ThumbnailImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_THUMBNAIL] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->ThumbnailImage);
        if(isset($XML->ImageSets->ImageSet->TinyImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_TINY] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->TinyImage);
        if(isset($XML->ImageSets->ImageSet->MediumImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_MEDIUM] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->MediumImage);
        if(isset($XML->ImageSets->ImageSet->LargeImage))
        $AmazonItem->Images[AmazonAdvItem::IMAGE_LARGE] = AmazonAdvImage::createWithXml($XML->ImageSets->ImageSet->LargeImage);
        
        return $AmazonItem;
    }
    
    /**
    * Return currency symbol of $this->CurrencyCode
    * 
    * @return string
    */
    public function getCurrencyChr() {
        switch($this->CurrencyCode) {
            case AmazonAdvItem::CURRENCY_EURO :
                return '&euro;';
            break;
            case AmazonAdvItem::CURRENCY_USD :
                return '$';
            break;
            case AmazonAdvItem::CURRENCY_JPY : 
                return '&yen;';
            break;
            case AmazonAdvItem::CURRENCY_GPB : 
                return '&pound;';
            break;
        }
        return '';
    }
    
    /**
    * Return $this->Price divide by 100. (Exemple : 16.2, 99.99)
    * 
    * @return string
    */
    public function getPrice() {
        if($this->Price == '')
        return '';
        
        return round($this->Price/100, 2);
    }
    /**
    * Return $this->getPrice(), with $this->getCurrencyChr() (Exemple : 16.5€, 9.33$)
    * 
    * @return string
    */
    public function getPriceWithCurrency() {
        return $this->getPrice().$this->getCurrencyChr();
    }
    
    /**
    * Return an AmazonAdvImage object.
    * 
    * @param string $size Use constant IMAGE_(.*) of AmazonAdvItem class
    * @return AmazonAdvImage
    */
    public function getImage($size) {
        return $this->Images[$size];
    }
    
    /**
    * Return the most appropriate author of the products. Selected in three field :
    * $this->Brand
    * $this->Author
    * $this->Creator
    * 
    * @return string
    */
    public function getAuthor() {
        if($this->Brand != '')
        return $this->Brand;
        if($this->Author != '')
        return $this->Author;
        if($this->Creator != '')
        return $this->Creator;
        
        return '';
    }
    
    public function __toString() {
        return 'AmazonAdvItem';
    }
}
?>