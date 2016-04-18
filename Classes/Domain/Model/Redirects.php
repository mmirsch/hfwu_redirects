<?php
namespace HFWU\HfwuRedirects\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Redirects
 */
class Redirects extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * shortUrl
     *
     * @var string
     */
    protected $shortUrl = '';

    /**
     * urlComplete
     *
     * @var string
     */
    protected $urlComplete = '';
    
    /**
     * urlHash
     *
     * @var string
     */
    protected $urlHash = '';
    
    /**
     * searchWord
     *
     * @var string
     */
    protected $searchWord = '';
    
    /**
     * isQrUrL
     *
     * @var bool
     */
    protected $isQrUrL = false;

    /**
     * pageId
     *
     * @var integer
     */
    protected $pageId = 0;

    /**
     * pageTitle
     *
     * @var string
     */
    protected $pageTitle = '';

    /**
     * pageId
     *
     * @var integer
     */
    protected $redirectCount = 0;

   /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the shortUrl
     *
     * @return string $shortUrl
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }
    
    /**
     * Sets the shortUrl
     *
     * @param string $shortUrl
     * @return void
     */
    public function setShortUrl($shortUrl)
    {
        $this->shortUrl = $shortUrl;
    }
    
    /**
     * Returns the urlComplete
     *
     * @return string $urlComplete
     */
    public function getUrlComplete()
    {
        return $this->urlComplete;
    }
    
    /**
     * Sets the urlComplete
     *
     * @param string $urlComplete
     * @return void
     */
    public function setUrlComplete($urlComplete)
    {
        $this->urlComplete = $urlComplete;
    }
    
    /**
     * Returns the urlHash
     *
     * @return string $urlHash
     */
    public function getUrlHash()
    {
        return $this->urlHash;
    }
    
    /**
     * Sets the urlHash
     *
     * @param string $urlHash
     * @return void
     */
    public function setUrlHash($urlHash)
    {
        $this->urlHash = $urlHash;
    }
    
    /**
     * Returns the searchWord
     *
     * @return string $searchWord
     */
    public function getSearchWord()
    {
        return $this->searchWord;
    }
    
    /**
     * Sets the searchWord
     *
     * @param string $searchWord
     * @return void
     */
    public function setSearchWord($searchWord)
    {
        $this->searchWord = $searchWord;
    }
    
    /**
     * Returns the pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }
    
    /**
     * Sets the pageId
     *
     * @param integer $pageId
     * @return void
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
    
    /**
     * Returns the boolean state of isqRuRL
     *
     * @return bool
     */
    public function isIsqRuRL()
    {
        return $this->isqRuRL;
    }
    
    /**
     * Returns the isQrUrL
     *
     * @return bool isQrUrL
     */
    public function getIsQrUrL()
    {
        return $this->isQrUrL;
    }
    
    /**
     * Sets the isQrUrL
     *
     * @param bool $isQrUrL
     * @return void
     */
    public function setIsQrUrL($isQrUrL)
    {
        $this->isQrUrL = $isQrUrL;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return int
     */
    public function getRedirectCount()
    {
        return $this->redirectCount;
    }

    /**
     * @param int $redirectCount
     */
    public function setRedirectCount($redirectCount)
    {
        $this->redirectCount = $redirectCount;
    }


}