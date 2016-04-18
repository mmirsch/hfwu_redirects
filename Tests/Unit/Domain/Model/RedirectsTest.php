<?php

namespace HFWU\HfwuRedirects\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \HFWU\HfwuRedirects\Domain\Model\Redirects.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class RedirectsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \HFWU\HfwuRedirects\Domain\Model\Redirects
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \HFWU\HfwuRedirects\Domain\Model\Redirects();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getShortUrlReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getShortUrl()
		);
	}

	/**
	 * @test
	 */
	public function setShortUrlForStringSetsShortUrl()
	{
		$this->subject->setShortUrl('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'shortUrl',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUrlCompleteReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getUrlComplete()
		);
	}

	/**
	 * @test
	 */
	public function setUrlCompleteForStringSetsUrlComplete()
	{
		$this->subject->setUrlComplete('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'urlComplete',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUrlHashReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getUrlHash()
		);
	}

	/**
	 * @test
	 */
	public function setUrlHashForStringSetsUrlHash()
	{
		$this->subject->setUrlHash('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'urlHash',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getSearchWordReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getSearchWord()
		);
	}

	/**
	 * @test
	 */
	public function setSearchWordForStringSetsSearchWord()
	{
		$this->subject->setSearchWord('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'searchWord',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getIsQrUrlReturnsInitialValueForBool()
	{
		$this->assertSame(
			FALSE,
			$this->subject->getIsQrUrl()
		);
	}

	/**
	 * @test
	 */
	public function setIsQrUrlForBoolSetsIsQrUrl()
	{
		$this->subject->setIsQrUrl(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'isQrUrl',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPageIdReturnsInitialValueFor()
	{	}

	/**
	 * @test
	 */
	public function setPageIdForSetsPageId()
	{	}
}
