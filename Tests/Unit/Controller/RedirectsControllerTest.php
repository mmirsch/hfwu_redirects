<?php
namespace HFWU\HfwuRedirects\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 
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
 * Test case for class HFWU\HfwuRedirects\Controller\RedirectsController.
 *
 */
class RedirectsControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \HFWU\HfwuRedirects\Controller\RedirectsController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('HFWU\\HfwuRedirects\\Controller\\RedirectsController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllRedirectssFromRepositoryAndAssignsThemToView()
	{

		$allRedirectss = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$redirectsRepository = $this->getMock('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository', array('findAll'), array(), '', FALSE);
		$redirectsRepository->expects($this->once())->method('findAll')->will($this->returnValue($allRedirectss));
		$this->inject($this->subject, 'redirectsRepository', $redirectsRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('redirectss', $allRedirectss);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenRedirectsToView()
	{
		$redirects = new \HFWU\HfwuRedirects\Domain\Model\Redirects();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('redirects', $redirects);

		$this->subject->showAction($redirects);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenRedirectsToRedirectsRepository()
	{
		$redirects = new \HFWU\HfwuRedirects\Domain\Model\Redirects();

		$redirectsRepository = $this->getMock('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository', array('add'), array(), '', FALSE);
		$redirectsRepository->expects($this->once())->method('add')->with($redirects);
		$this->inject($this->subject, 'redirectsRepository', $redirectsRepository);

		$this->subject->createAction($redirects);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenRedirectsToView()
	{
		$redirects = new \HFWU\HfwuRedirects\Domain\Model\Redirects();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('redirects', $redirects);

		$this->subject->editAction($redirects);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenRedirectsInRedirectsRepository()
	{
		$redirects = new \HFWU\HfwuRedirects\Domain\Model\Redirects();

		$redirectsRepository = $this->getMock('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository', array('update'), array(), '', FALSE);
		$redirectsRepository->expects($this->once())->method('update')->with($redirects);
		$this->inject($this->subject, 'redirectsRepository', $redirectsRepository);

		$this->subject->updateAction($redirects);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenRedirectsFromRedirectsRepository()
	{
		$redirects = new \HFWU\HfwuRedirects\Domain\Model\Redirects();

		$redirectsRepository = $this->getMock('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository', array('remove'), array(), '', FALSE);
		$redirectsRepository->expects($this->once())->method('remove')->with($redirects);
		$this->inject($this->subject, 'redirectsRepository', $redirectsRepository);

		$this->subject->deleteAction($redirects);
	}
}
