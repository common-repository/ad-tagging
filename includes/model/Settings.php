<?php

namespace SYBAT\Model;

/**
 * Ad Tagging plugin settings using Options API
 */
class Settings {

    private $adTagName = null;
    private $affiliateUrlList = [];
    private $updateOnSave = false;

	public function __construct() {
        $this->checkUpdate();

        $defaultAffiliateUrls = json_encode([
            'https://amzn.to/',
            'https://www.amazon.co.jp/',
            'https://hb.afl.rakuten.co.jp/',
            'https://px.a8.net/',
            'https://ck.jp.ap.valuecommerce.com/',
            'https://h.accesstrade.net/',
            'https://t.afi-b.com/',
            'https://www.rentracks.jp/adx/',
            'https://click.linksynergy.com/',
            'https://smart-c.jp/c?',
            'https://c2.cir.io/',
            'https://click.j-a-net.jp/',
            'https://www.tcs-asp.net/alink',
            'https://www.infotop.jp/click.php',
            'https://ad2.trafficgate.net/',
        ]);

        $this->adTagName = get_option('sybat.ad_tag_name', 'AD');
        $affiliateUrls = get_option('sybat.affiliate_urls', $defaultAffiliateUrls);
        $this->affiliateUrlList = json_decode($affiliateUrls);
        $this->updateOnSave = boolval(get_option('sybat.update_on_save', 'true'));
	}

    private function checkUpdate() {
        $optionNames = [
            'ad_tag_name',
            'affiliate_urls',
            'update_on_save',
        ];
        
        $oldPrefix = 'syb_ad_tagging.';
        $newPrefix = 'sybat.';

        foreach ($optionNames as $optionName) {
            $oldOptionName = $oldPrefix . $optionName;
            $newOptionName = $newPrefix . $optionName;
            if (get_option($oldOptionName) !== false &&
                get_option($newOptionName) === false) {
                $value = get_option($oldOptionName);
                add_option($newOptionName, $value);
                delete_option($oldOptionName);
            }
        }
    }

    public function getAdTagName() {
        return $this->adTagName;
    }

    public function getAffiliateUrlList() {
        return $this->affiliateUrlList;
    }

    public function getUpdateOnSave() {
        return $this->updateOnSave;
    }

    public function setAdTagName($adTagName) {
        $this->adTagName = $adTagName;
    }

    public function setAffiliateUrlList($affiliateUrlList) {
        $this->affiliateUrlList = $affiliateUrlList;
    }

    public function setUpdateOnSave($updateOnSave) {
        $this->updateOnSave = $updateOnSave;
    }

    public function store() {
        if (get_option('sybat.ad_tag_name') === false) {
			add_option('sybat.ad_tag_name', $this->adTagName);
		} else {
			update_option('sybat.ad_tag_name', $this->adTagName);
		}

		if (get_option('sybat.affiliate_urls') === false) {
			add_option('sybat.affiliate_urls', json_encode($this->affiliateUrlList));
		} else {
			update_option('sybat.affiliate_urls', json_encode($this->affiliateUrlList));
		}

        if (get_option('sybat.update_on_save') === false) {
			add_option('sybat.update_on_save', $this->updateOnSave);
		} else {
			update_option('sybat.update_on_save', $this->updateOnSave);
		}
    }

    public function delete() {
        delete_option('sybat.ad_tag_name');
        delete_option('sybat.affiliate_urls');
        delete_option('sybat.update_on_save');
    }
   
}
