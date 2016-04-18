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
 * RedirectCalls
 */
class RedirectCalls extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * count
     *
     * @var int
     */
    protected $count = 0;
    
    /**
     * redirect
     *
     * @var int
     */
    protected $redirect = 0;
    
    /**
     * Returns the count
     *
     * @return int $count
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * Sets the count
     *
     * @param int $count
     * @return void
     */
    public function setCount($count)
    {
        $this->count = $count;
    }
    
    /**
     * Returns the redirect
     *
     * @return int $redirect
     */
    public function getRedirect()
    {
        return $this->redirect;
    }
    
    /**
     * Sets the redirect
     *
     * @param int $redirect
     * @return void
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

}