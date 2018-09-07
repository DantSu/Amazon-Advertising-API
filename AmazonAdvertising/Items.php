<?php

namespace AmazonAdvertising;

/**
 * AmazonAdvertising\Items contain the result of AmazonAdv request.
 * AmazonAdvertising\Items->TotalResults         : Number of products that Amazon returns
 * AmazonAdvertising\Items->TotalPages           : Number of pages of products that Amazon returns
 * AmazonAdvertising\Items->MoreSearchResultsUrl : URL of Amazon page which contain more products
 * AmazonAdvertising\Items->Items                : Array of AmazonAdvertising\Item objects
 *
 * @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
 * @version  1.0
 * @access   public
 */
class Items
{
    public $TotalResults = '';
    public $TotalPages = '';
    public $MoreSearchResultsUrl = '';
    public $Items = [];

    /**
     * Create an instance of AmazonAdvertising\Items with a SimpleXMLElement object.
     *
     * @param \SimpleXMLElement $XML
     * @return Items
     */
    public static function createWithXml($XML)
    {

        $AmazonItems = new Items();

        $XML = $XML->Items;

        if (isset($XML->TotalResults))
            $AmazonItems->TotalResults = (int)$XML->TotalResults;
        if (isset($XML->TotalPages))
            $AmazonItems->TotalPages = (int)$XML->TotalPages;
        if (isset($XML->MoreSearchResultsUrl))
            $AmazonItems->MoreSearchResultsUrl = (string)$XML->MoreSearchResultsUrl;

        foreach ($XML->Item as $XMLItem)
            $AmazonItems->Items[] = Item::createWithXml($XMLItem);

        return $AmazonItems;
    }

}
