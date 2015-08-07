<?php
namespace Tevben\Wishlist;

class Select {
    protected $amazonTld;
    protected $wishlistId;
    protected $wishlist;
    protected $budget = 0;
    protected $spent = 0;
    protected $selection = array();

    function __construct() {
        $this->wishlistId = getenv("WISHLIST_ID");
        $this->budget = getenv("WISHLIST_BUDGET");
        $this->amazonTold = getenv("AMAZON_TLD");
        
        $scraper = new \AppZap\AmazonWishLister\Scraper();
        $this->wishlist = $scraper->scrape('http://www.amazon.' .$this->amazonTld . '/registry/wishlist/' . $this->wishlistId);
    }

    public function pick() {
        while($this->spent < $this->budget && count($this->wishlist) > 0) {
            $item = $this->random();
            if ($this->inBudget($item)) {
                $this->addToSelection($item);
            }
        }

        return array(
            "selection" => $this->selection,
            "spent" => $this->spent);
    }
    
    protected function inBudget($item) {
        $price = $this->parsePrice($item['new-price']);
        if ($price > 0 && $this->spent+$price <= $this->budget) {
            return true;
        }
        
        return false;
    }
    
    protected function AddToSelection($item) {
        $price = $this->parsePrice($item['new-price']);
        $item["price"] = $price;
        $this->selection[] = $item;
        $this->spent += $price;
    }
    
    
    protected function random() {
        $index = rand(0, count($this->wishlist)-1);
        $item = $this->wishlist[$index];
        $this->removeFromWishlist($index);
        
        return $item;
    }
    
    protected function removeFromWishlist($index) {
        unset($this->wishlist[$index]);
        $this->wishlist = array_values($this->wishlist);        
    }
    
    protected function parsePrice($price) {
    	return floatval(filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
    }
}