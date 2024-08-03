<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtLink;

/** @var $size string */

if (XT_REVIEWS_ENABLED)
{
if (isset($xtPlugin->active_modules['xt_master_slave']))
{
	switch (XT_REVIEWS_MASTER_SLAVE)
	{
		case 'master_only':
			// If this is a slave - don't display ratings
			if (empty($this->data['products_master_flag']) && ! empty($this->data['products_master_model']))
			{
				return;
			}

			break;
		case 'slave_only':
			// If this is a master - don't display ratings
			if ($this->data['products_master_flag'] OR empty($this->data['products_master_model']))
			{
				return;
			}

			break;
		default:
			break;
	}
}
if ($size!='price') {
	$reviews = new xt_reviews();
	$this->data['review_stars_rating'] = $reviews->getStars($this->data['products_id']);
	$this->data['products_rating_count'] = $reviews->getReviewsSum($this->data['products_id']);
	$this->data['link_reviews_write'] = $xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info='.$this->data['products_id']));
    $this->data['link_reviews_list'] = $xtLink->_link(array('page' => 'reviews', 'paction' => 'show', 'params' => 'info='.$this->data['products_id']));
}
}

