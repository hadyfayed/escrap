<?php
namespace EcomScrap;

use Goutte\Client as GoutteClient;
use League\Uri\Http;
use League\Uri\Components\Host;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Client
 * @package EcomScrap
 */
class Client{
    private $_url;
    private $_productData;
    private $_id;
    private $_name;
    private $_description;
    private $_salePrice;
    private $_originalPrice;
    private $_category;
    private $_availability;
    private $_image;
    private $_images;
    private $_breadcrumbs;
    private $_client;
    private $_crawler;
    private $_domain;
    private $_host;
    private $_uri;
    private $_support;
    private $_schema;


    public function getProductData()
    {
        return $this->_productData;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return mixed
     */
    public function getSalePrice()
    {
        return $this->_salePrice;
    }

    /**
     * @return mixed
     */
    public function getOriginalPrice()
    {
        return $this->_originalPrice;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->_availability;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->_images;
    }

    /**
     * @return mixed
     */
    public function getBreadcrumbs()
    {
        return $this->_breadcrumbs;
    }

    private function create(){
        $this->_support = Yaml::parseFile('./src/supports.yaml');
        $this->_schema = $this->_support['default'];
        if(isset($this->_support[$this->_domain])){
            $this->_schema = $this->_support[$this->_domain];
        }
        $this->collectBaseData();
    }


    public function addSupport($array){
        $this->_support = array_merge($this->_support, $array);
    }

    private function collectBaseData(){
        /** @var $XPATH_NAME string */
        /** @var $XPATH_DESCRIPTION string */
        /** @var $XPATH_IMAGE string */
        /** @var $XPATH_SALE_PRICE string */
        /** @var $XPATH_ORIGINAL_PRICE string */
        /** @var $XPATH_CATEGORY string */
        /** @var $XPATH_AVAILABILITY string */
        /** @var $XPATH_BREADCRUMBS string */
        /** @var $BREADCRUMBS_DELIMITER string */
        extract($this->_schema);
        if(isset($XPATH_NAME))
            $this->_name = $this->getData($XPATH_NAME);
        if(isset($XPATH_DESCRIPTION))
            $this->_description = $this->_crawler->filterXpath($XPATH_DESCRIPTION)->attr('content');
        if(isset($XPATH_IMAGE)) {
            $this->_images = $this->collectImages();
            $this->_image = $this->_images[0];
        }
        if(isset($XPATH_SALE_PRICE))
            $this->_salePrice = $this->getData($XPATH_SALE_PRICE);
        if(isset($XPATH_ORIGINAL_PRICE))
            $this->_originalPrice = $this->getData($XPATH_ORIGINAL_PRICE);
        if(isset($XPATH_CATEGORY))
            $this->_category = $this->getData($XPATH_CATEGORY);
        if(isset($XPATH_AVAILABILITY))
            $this->_availability = $this->getData($XPATH_AVAILABILITY);
        if(isset($XPATH_BREADCRUMBS))
            $this->_breadcrumbs = $this->getData($XPATH_BREADCRUMBS, true,true,$BREADCRUMBS_DELIMITER);
        $this->_productData = [
            'title' => $this->_name,
            'description' => $this->_description,
            'mainImage' => $this->_image,
            'images' => $this->_images,
            'salePrice' => $this->_salePrice,
            'originalPrice' => $this->_originalPrice,
            'category' => $this->_category,
            'availability' => $this->_availability,
            'breadcrumbs' => $this->_breadcrumbs,
        ];
    }

    private function getData($path, $trim = true, $explode = false, $delimiter = '›')
    {
        if(method_exists($this,$path)){
            $data = $this->$path();
        }
        else{
            $data = $this->_crawler->filterXPath($path)->text();
            if ($trim) {
                $data = preg_replace('/\s+/', ' ', $data);
            }
            if ($explode) {
                $data = explode($delimiter, $data);
            }
        }
        return $data;
    }

    private function collectColors(){

    }

    private function collectMeta(){

    }

    private function collectPrice(){

    }

    private function collectImages(){
        /** @var $XPATH_IMAGE string */
        extract($this->_schema);

        $pattern = '/<img[^>]+src="([^">]+)"/';

        $imagesHtml = $this->_crawler->filterXPath($XPATH_IMAGE)->html();
        preg_match_all($pattern, $imagesHtml, $imagesUrl);
        $imagesUrl = $imagesUrl[1];
        for ($i = 0; $i < count($imagesUrl); $i++) {
            if(isset($IMAGE_PART_TO_REMOVE) && !isset($IMAGE_PART_TO_ADD))
                $imagesUrl[$i] = str_replace($IMAGE_PART_TO_REMOVE, "", $imagesUrl[$i]);
            if(isset($IMAGE_PART_TO_REMOVE) && isset($IMAGE_PART_TO_ADD))
                $imagesUrl[$i] = str_replace($IMAGE_PART_TO_REMOVE, $IMAGE_PART_TO_ADD, $imagesUrl[$i]);
        }
        return $imagesUrl;
    }

    public function __construct($url)
    {
        $this->_url = $url;
        $this->_client = new GoutteClient();
        $this->_crawler = $this->_client->request('GET', $this->_url);
        $this->_uri = Http::createFromString($this->_url);
        $this->_host = new Host($this->_uri->getHost());
        $this->_domain = $this->_host->getLabel(1);
        $this->create();
    }
}