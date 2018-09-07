<?php

namespace AmazonAdvertising;

/**
 * AmazonAdvertising\Item contain one Amazon product.
 * AmazonAdvertising\Item->Author       : Author of the product (Example : J.K. Rowling...)
 * AmazonAdvertising\Item->Creator      : Creator of the product (Example : Nom Prénom, Nom Prénom....)
 * AmazonAdvertising\Item->Brand        : Brand of the product (Example : Nathan, Ubisoft...)
 * AmazonAdvertising\Item->Manufacturer : Manufacturer (Example : Ubisoft, EA Games...)
 * AmazonAdvertising\Item->ProductGroup : Product Group (Example : DVD, BOOKS...)
 * AmazonAdvertising\Item->Title        : Title of the product (Iron Man, The Lord Of The Ring : The fellowship of the ring, ...)
 * AmazonAdvertising\Item->URL          : URL of the Amazon page of the product (http://www.amazon.com/...)
 * AmazonAdvertising\Item->Binding      : Binding of Books (Example : Paperback (Broché en francais))
 * AmazonAdvertising\Item->Price        : Price of the product in cents (divide by 100 for get the reel price) (Example : 6550 = 65.50$)
 * AmazonAdvertising\Item->CurrencyCode : Contain the currency use by the price (Example : EUR, USD, ...)
 *
 * @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
 * @version  1.0
 * @access   public
 */
class Item
{
    public $Author       = '';
    public $Creator      = '';
    public $Brand        = '';
    public $Manufacturer = '';
    public $ProductGroup = '';
    public $Title        = '';
    public $URL          = '';
    public $Binding      = '';
    public $Price        = '';
    public $CurrencyCode = '';
    private $Images      = [];

    const IMAGE_SWATCH    = 'SwatchImage';
    const IMAGE_SMALL     = 'SmallImage';
    const IMAGE_THUMBNAIL = 'ThumbnailImage';
    const IMAGE_TINY      = 'TinyImage';
    const IMAGE_MEDIUM    = 'MediumImage';
    const IMAGE_LARGE     = 'LargeImage';

    const CURRENCY_EURO = 'EUR';
    const CURRENCY_USD  = 'USD';
    const CURRENCY_GPB  = 'GPB';
    const CURRENCY_JPY  = 'JPY';

    /**
     * Create an instance of AmazonAdvertising\Item with a \SimpleXMLElement object. (->Items)
     *
     * @param \SimpleXMLElement $XML
     * @return Item
     */
    public static function createWithXml($XML)
    {
        $ItemAttributes = $XML->ItemAttributes;

        $AmazonItem = new Item();
        if (isset($ItemAttributes->Manufacturer))
            $AmazonItem->Manufacturer = (string)$ItemAttributes->Manufacturer;
        if (isset($ItemAttributes->Binding))
            $AmazonItem->Binding = (string)$ItemAttributes->Binding;
        if (isset($ItemAttributes->ProductGroup))
            $AmazonItem->ProductGroup = (string)$ItemAttributes->ProductGroup;
        if (isset($ItemAttributes->Title))
            $AmazonItem->Title = (string)$ItemAttributes->Title;
        if (isset($XML->DetailPageURL))
            $AmazonItem->URL = (string)$XML->DetailPageURL;
        if (isset($ItemAttributes->ListPrice->Amount))
            $AmazonItem->Price = (int)$ItemAttributes->ListPrice->Amount;
        if (isset($ItemAttributes->ListPrice->CurrencyCode))
            $AmazonItem->CurrencyCode = (string)$ItemAttributes->ListPrice->CurrencyCode;


        if (isset($ItemAttributes->Brand)) {
            $AmazonItem->Brand = '';
            foreach ($ItemAttributes->Brand as $auth)
                $AmazonItem->Brand .= ', ' . (string)$auth;
            $AmazonItem->Brand = substr($AmazonItem->Brand, 2);
        }
        if (isset($ItemAttributes->Author)) {
            $AmazonItem->Author = '';
            foreach ($ItemAttributes->Author as $auth)
                $AmazonItem->Author .= ', ' . (string)$auth;
            $AmazonItem->Author = substr($AmazonItem->Author, 2);
        }
        if (isset($ItemAttributes->Creator)) {
            $AmazonItem->Creator = '';
            foreach ($ItemAttributes->Creator as $auth)
                $AmazonItem->Creator .= ', ' . (string)$auth;
            $AmazonItem->Creator = substr($AmazonItem->Creator, 2);
        }

        $AmazonImageSet = $XML->ImageSets->ImageSet;
        if (isset($XML->ImageSets->ImageSet->SwatchImage))
            $AmazonItem->Images[Item::IMAGE_SWATCH] = Image::createWithXml($XML->ImageSets->ImageSet->SwatchImage);
        if (isset($XML->ImageSets->ImageSet->SmallImage))
            $AmazonItem->Images[Item::IMAGE_SMALL] = Image::createWithXml($XML->ImageSets->ImageSet->SmallImage);
        if (isset($XML->ImageSets->ImageSet->ThumbnailImage))
            $AmazonItem->Images[Item::IMAGE_THUMBNAIL] = Image::createWithXml($XML->ImageSets->ImageSet->ThumbnailImage);
        if (isset($XML->ImageSets->ImageSet->TinyImage))
            $AmazonItem->Images[Item::IMAGE_TINY] = Image::createWithXml($XML->ImageSets->ImageSet->TinyImage);
        if (isset($XML->ImageSets->ImageSet->MediumImage))
            $AmazonItem->Images[Item::IMAGE_MEDIUM] = Image::createWithXml($XML->ImageSets->ImageSet->MediumImage);
        if (isset($XML->ImageSets->ImageSet->LargeImage))
            $AmazonItem->Images[Item::IMAGE_LARGE] = Image::createWithXml($XML->ImageSets->ImageSet->LargeImage);

        return $AmazonItem;
    }

    /**
     * Return currency symbol of $this->CurrencyCode
     *
     * @return string
     */
    public function getCurrencyChr()
    {
        switch ($this->CurrencyCode) {
            case Item::CURRENCY_EURO :
                return '&euro;';
                break;
            case Item::CURRENCY_USD :
                return '$';
                break;
            case Item::CURRENCY_JPY :
                return '&yen;';
                break;
            case Item::CURRENCY_GPB :
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
    public function getPrice()
    {
        if ($this->Price == '')
            return '';

        return round($this->Price / 100, 2);
    }

    /**
     * Return $this->getPrice(), with $this->getCurrencyChr() (Exemple : 16.5€, 9.33$)
     *
     * @return string
     */
    public function getPriceWithCurrency()
    {
        return $this->getPrice() . $this->getCurrencyChr();
    }

    /**
     * Return an AmazonAdvertising\Image object.
     *
     * @param string $size Use constant IMAGE_(.*) of Item class
     * @return Image
     */
    public function getImage($size)
    {
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
    public function getAuthor()
    {
        if ($this->Brand != '')
            return $this->Brand;
        if ($this->Author != '')
            return $this->Author;
        if ($this->Creator != '')
            return $this->Creator;

        return '';
    }

}