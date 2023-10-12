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

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'conf/config_search.php';

class search_query extends getProductSQL_query
{
    protected $placeholderParams = array();

    function getSQL_query($sql_cols = 'p.products_id', $filter_type='string')
    {
        return array('sql' => parent::getSQL_query($sql_cols, $filter_type), 'placeholderParams' => $this->placeholderParams);
    }


    function F_Keywords($data = array())
    {
        global $xtPlugin;
        $sdesc='';
        $desc='';
		($plugin_code = $xtPlugin->PluginCode('class.search_query.php:F_Keywords_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
        {
		return $plugin_return_value;
        }
        if((!empty($sdesc) || !empty($desc)) && count($this->placeholderParams) == 0 )
        {
            // forbid usage without placeholder
            $sdesc='';
            $desc='';
        }

        $split_keywords_raw = explode(' ', $data['keywords']);
        $split_keywords = array();

        foreach ($split_keywords_raw as $key => $val)
        {
            $val = trim($val);
            if (_SYSTEM_SEARCH_SPLIT == 'true' && (empty($val) || mb_strlen($val, 'utf-8') < SEARCH_MIN_LENGTH))
            {
                continue;
            }
            $split_keywords[] = $val;
        }

        if (_SYSTEM_SEARCH_SPLIT == 'true')
        {
            // wir verwenden die ermittelten split_keywords
        }
        else
        {
            // wir machen ein concat der gesäuberten split_keywords
            if (count($split_keywords)){
                $split_keywords = array(implode(' ', $split_keywords));
            }
            // oder wollen wir die rohe eingabe verwenden ? dann ohne if count()
            // $split_keywords = array( $data['keywords'] );
        }

        if (!_SYSTEM_SEARCH_SPLIT && (empty($split_keywords[0]) || mb_strlen($split_keywords[0], 'utf-8') < SEARCH_MIN_LENGTH) )
        {
            $this->setSQL_WHERE(" AND 1=0");
        }
        else if(count($split_keywords))
        {
            $pname_like = array();
            $pkeywords_like = array();
            $pmodel_like = array();
            $pean_like = array();

            /** in name suchen wir mit auch mit htmlentities, wenn nötig */
            foreach ($split_keywords as $key => $val)
            {
                $this->placeholderParams[] = '%' . $val . '%';
                $pname_like_part = "pd.products_name LIKE ?";
                $valHtml = htmlentities($val);
                if ($valHtml != $val)
                {
                    $this->placeholderParams[] = '%' . htmlentities($val) . '%';
                    $pname_like_part = "(". $pname_like_part." OR pd.products_name LIKE ? )";
                }
                $pname_like[] = $pname_like_part;
            }


            /** für keywords, model, ean verwenden wir keine htmlentities */
            if(SEARCH_USE_KEYWORDS === true)
            {
                foreach ($split_keywords as $key => $val)
                {
                    $this->placeholderParams[] = '%' . $val . '%';
                $pkeywords_like[]="pd.products_keywords LIKE ?";
                }
            }
            if(SEARCH_USE_MODEL === true)
            {
                foreach ($split_keywords as $key => $val)
                {
                    if(mb_strlen($val) < SEARCH_MODEL_MIN_LENGTH) continue;
                    $this->placeholderParams[] = '%' . $val . '%';
                $pmodel_like[]="p.products_model LIKE ?";
                }
            }
            if(SEARCH_USE_EAN === true)
            {
                foreach ($split_keywords as $key => $val)
                {
                    if (mb_strlen($val) < SEARCH_EAN_MIN_LENGTH)
                    {
                        continue;
                    }
                    $this->placeholderParams[] = '%' . $val . '%';
                $pean_like[]="p.products_ean LIKE ?";
            }
            }

            $pname     = "    (" . implode(SEARCH_CONDITION_CONNECTOR, $pname_like) . ")";
            $pkeywords = count($pkeywords_like) ? " or (" . implode(SEARCH_CONDITION_CONNECTOR, $pkeywords_like) . ")" : '';
            $pmodel    = count($pmodel_like) ? " or (" . implode(' OR ', $pmodel_like) . ")" : '';
            $pean      = count($pean_like) ?   " or (" . implode(' OR ', $pean_like) . ")"   : '';

            /** in desc und sdesc suchen wir mit auch mit htmlentities, wenn nötig */
            if ($data['sdesc'] == 'on')
            {
                $like = array();
                foreach ($split_keywords as $key => $val)
                {
                    $this->placeholderParams[] = '%' . $val . '%';
                    $like_part = "pd.products_short_description LIKE ? ";
                    $valHtml = htmlentities($val);
                    if ($valHtml != $val)
                    {
                        $this->placeholderParams[] = '%' . htmlentities($val) . '%';
                        $like_part = "( ". $like_part ." OR pd.products_short_description LIKE ? ) ";
                    }
                    $like[] = $like_part;
                }

                $sdesc = " or (" . implode(SEARCH_CONDITION_CONNECTOR, $like) . ")";
            }

            if ($data['desc'] == 'on')
            {
                $like = array();
                foreach ($split_keywords as $key => $val)
                {
                    $this->placeholderParams[] = '%' . $val . '%';
                    $like_part = "pd.products_description LIKE ? ";
                    $valHtml = htmlentities($val);
                    if ($valHtml != $val)
                    {
                        $this->placeholderParams[] = '%' . htmlentities($val) . '%';
                        $like_part = "( ". $like_part ." OR pd.products_description LIKE ? ) ";
                    }
                    $like[] = $like_part;
                }

                $desc = " or (" . implode(SEARCH_CONDITION_CONNECTOR, $like) . ")";
            }

            $sql_where = "AND 
            (
            " . $pname . $pkeywords . $pmodel . $pean . $sdesc . $desc . "
            )
            ";
            
            $this->setSQL_WHERE($sql_where);

            $sort_direction = 'DESC';
            if (defined('SEARCH_SORT_DIRECTION'))
            {
                if(strtoupper(trim(SEARCH_SORT_DIRECTION)) == 'ASC')
                {
                    $sort_direction = 'ASC';
                }
            }

            switch(SEARCH_SORT)
            {
                case SEARCH_SORT_ID:
                    $this->setSQL_SORT(' p.products_id '.$sort_direction);
                    break;
                case SEARCH_SORT_ADDED:
                    $this->setSQL_SORT(' p.date_added '.$sort_direction);
                    break;
                case SEARCH_SORT_NAME:
                    $this->setSQL_SORT(' pd.products_name '.$sort_direction);
                    break;
                case SEARCH_SORT_ORDER:
                    $this->setSQL_SORT(' p.products_ordered '.$sort_direction);
                    break;
                default:
                    $this->setSQL_SORT(' p.products_id '.$sort_direction);
            }


        }else {
            $this->setSQL_WHERE(" AND 1=0");
        }
	}
	
    function F_MultiCheck($params = '')
    {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
	}		

}

