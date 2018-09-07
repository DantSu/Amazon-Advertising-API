<?php

namespace AmazonAdvertising;

/**
 * AmazonAdvertising\Request (Amazon Advertising) get products from Amazon for display it into your Website.
 *
 * @author   Franck Alary <http://www.developpeur-web.dantsu.com/>
 * @version  1.0
 * @access   public
 */
class Request
{
    private static $HTTP   = 'http://';
    private static $HOST   = 'webservices.amazon.fr';
    private static $METHOD = 'GET';
    private static $URI    = '/onca/xml';

    private static $Key          = 'CLE_IDENTIFIANT_API_AMAZON';
    private static $SecretKey    = 'CLE_IDENTIFIANT_SECRET_API_AMAZON';
    private static $AssociateTag = 'ASSOCIATE_TAG';

    private $Keywords = [];
    private $SearchIndex = 'All';

    const ALL                   = "All";
    const APPAREL               = "Apparel";
    const AUTOMOTIVE            = "Automotive";
    const BABY                  = "Baby";
    const BEAUTY                = "Beauty";
    const BLENDED               = "Blended";
    const BOOKS                 = "Books";
    const CLASSICAL             = "Classical";
    const DVD                   = "DVD";
    const ELECTRONICS           = "Electronics";
    const FOREIGN_BOOKS         = "ForeignBooks";
    const HEALTH_PERSONAL_CARE  = "HealthPersonalCare";
    const JEWELRY               = "Jewelry";
    const KINDLE_STORE          = "KindleStore";
    const KITCHEN               = "Kitchen";
    const LIGHTING              = "Lighting";
    const MP3_DOWNLOADS         = "MP3Downloads";
    const MUSIC                 = "Music";
    const MUSICAL_INSTRUMENTS   = "MusicalInstruments";
    const MUSIC_TRACKS          = "MusicTracks";
    const OFFICE_PRODUCTS       = "OfficeProducts";
    const PC_HARDWARE           = "PCHardware";
    const PET_SUPPLIES          = "PetSupplies";
    const SHOES                 = "Shoes";
    const SOFTWARE              = "Software";
    const SOFTWARE_VIDEO_GAMES  = "SoftwareVideoGames";
    const SPORTING_GOODS        = "SportingGoods";
    const TOYS                  = "Toys";
    const VHS                   = "VHS";
    const VIDEO                 = "Video";
    const VIDEO_GAMES           = "VideoGames";
    const WATCHES               = "Watches";

    /**
     * Create a new instance of AmazonAdvertising\Request
     *
     */
    public static function Create()
    {
        return new Request();
    }

    /**
     * Add keyword to the products search.
     *
     * @param string $keywords Separate keywords with space.
     * @return Request
     */
    public function addKeyword($keywords)
    {
        $ArrayWords = explode(' ', $keywords);
        foreach ($ArrayWords as $word) {
            if ($word != '')
                $this->Keywords[] = $word;
        }
        return $this;
    }

    /**
     * Reset the keyword list
     *
     * @return Request
     */
    public function resetKeyword()
    {
        $this->Keywords = [];
        return $this;
    }

    /**
     * Encode the variable for URL
     *
     * @param string $string
     * @return string
     */
    public static function url_encode($string)
    {
        return str_replace("%7E", "~", rawurlencode($string));
    }

    /**
     * Set the searchIndex value (DVD, BOOKS, SOFTWARE...)
     *
     * @param string $SI Use the constant of AmazonAdvertising\Request class
     * @return Request
     */
    public function setSearchIndex($SI)
    {
        $this->SearchIndex = $SI;
        return $this;
    }

    /**
     * Execute the request to amazon. It will return a AmazonAdvertising\Items object.
     *
     * @return Items
     */
    public function request()
    {
        $parameter = [
            'AWSAccessKeyId' => self::$Key,
            'AssociateTag' => self::$AssociateTag,
            'Keywords' => implode('+', $this->Keywords),
            'Operation' => 'ItemSearch',
            'ResponseGroup' => 'Images,ItemAttributes',
            'SearchIndex' => $this->SearchIndex,
            'Service' => 'AWSECommerceService',
            'Timestamp' => date("Y-m-d\TH:i:s\Z"),
            'Version' => '2011-08-01'
        ];

        ksort($parameter);

        $queryString = '';
        foreach ($parameter as $k => $v)
            $queryString .= '&' . Request::url_encode($k) . '=' . Request::url_encode($v);
        $queryString = substr($queryString, 1);

        $signature = Request::url_encode(
            base64_encode(
                hash_hmac(
                    "sha256",
                    self::$METHOD . "\n" . self::$HOST . "\n" . self::$URI . "\n" . $queryString,
                    self::$SecretKey,
                    true
                )
            )
        );

        $queryString = self::$HTTP . self::$HOST . self::$URI . '?' . $queryString . '&Signature=' . $signature;

        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $queryString);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $XML = curl_exec($curl);
            curl_close($curl);
        } else {
            $XML = file_get_contents($queryString);
        }

        return Items::createWithXml(simplexml_load_string($XML));
    }
}
