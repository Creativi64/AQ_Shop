<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

interface IExecutableScript {
	
	/**
	 * Array of pages available for this script
	 */
	public function getPages();
	
	/**
	 * Check if there is next page
	 * @param string $currentPage
	 */
	public function hasNextPage($currentPage);
	
	/**
	 * Get next page
	 * @param WizardPage $currentPage
	 */
	public function getNextPage($currentPage);
	
	/**
	 * Get page
	 * @param WizardPage $currentPage
	 */
	public function getPage($currentPage);
	
	/**
	 * Get the type of the wizard that this script is for.
	 * Returns Wizard::WIZARD_TYPE_INSTALL or Wizard::WIZARD_TYPE_UPDATE
	 */
	public function getWizardType();
	
	/**
	 * Needed only for update scripts. Needed to determine if the script can be applied
	 * to the current shop version
	 */
	public function getAppliableShopVersion();
	
    public function getTargetShopVersion();
    public function getSkippableShopVersion();

	/**
	 * Get unique id for this script.
	 */
	public function getUniqueId();
	
	/**
	 * Get script title that will be show in the listing
	 */
	public function getScriptTitle();
}