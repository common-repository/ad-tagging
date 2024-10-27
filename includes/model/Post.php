<?php

namespace SYBAT\Model;

/**
 * WordPress Post
 */
class Post {

    private $content;

    /**
     * 
     * @param $content string 
     */
	public function __construct($content) {
        $this->content = $content;
	}

    /**
     * 
     * @param $settings Settings
     */
    public function isAffiliateContent($settings) {
        $links = $settings->getAffiliateUrlList();

        $pattern = '/';
        foreach ($links as $i => $link) {
            if ($i != 0) {
                $pattern .= '|';
            }
            $pattern .= preg_quote($link, '/');
            $pattern .= '|';
            $pattern .= preg_quote(str_replace('https://', '"//', $link), '/');
        }
        $pattern .= '/';
    
        // remove html comment
        $content = preg_replace('/<!--[\s\S]*?-->/s', '', $this->content);
    
        if (preg_match($pattern, $content) === 1) {
            return true;
        } else {
            return false;
        }
    }
}
