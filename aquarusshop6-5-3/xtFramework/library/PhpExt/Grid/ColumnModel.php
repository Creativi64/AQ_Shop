<?php
/**
 * PHP-Ext Library
 * http://php-ext.googlecode.com
 * @author Sergei Walter <sergeiw[at]gmail[dot]com>
 * @copyright 2008 Sergei Walter
 * @license http://www.gnu.org/licenses/lgpl.html
 * @link http://php-ext.googlecode.com
 * 
 * Reference for Ext JS: http://extjs.com
 * 
 */

/**
 * @see PhpExt_Observable
 */
include_once'PhpExt/Observable.php';
/**
 * @see PhpExt_Grid_IColumnCollection
 */
include_once'PhpExt/Grid/IColumnCollection.php';

/**
 * This is the default implementation of a ColumnModel used by the Grid. This class is initialized with an Array of column config objects.
 * 
 * An individual column's config object defines the header string, the Ext.data.Record field the column draws its data from, an otional rendering function to provide customized data formatting, and the ability to apply a CSS class to all cells in a column through its id config option.
 * 
 * Usage:
 * <code>
 * $colModel = new PhpExt_Grid_ColumnModel();
 * $colModel->addColumn(PhpExt_Grid_ColumnConfigObject::getInstance('Ticker')
 * 			              	->setWidth(60)
 * 							->setSortable(true))
 * 			->addColumn(PhpExt_Grid_ColumnConfigObject::getInstance('Company Name')
 * 			              	->setWidth(150)
 * 							->setSortable(true))
 *			->addColumn(PhpExt_Grid_ColumnConfigObject::getInstance('Market Cap.')
 * 			              	->setWidth(100)
 * 							->setSortable(true))
 * 			);
 * 
 *  // Or like this...
 * 
 *  $salesCol = new PhpExt_Grid_ColumnConfigObject('$ Sales');
 *  $salesCol->setWidth(100)
 * 			 ->setSortable(true)
 * 			 ->setRenderer(PhpExt_JavascriptStm::variable('money'));
 *  $empCol = new PhpExt_Grid_ColumnConfigObject('Employees');
 *  $empCol->setWidth(100)
 * 		   ->setSortable(true)
 * 		   ->setResizable(false);
 *  
 *  $colModel->addColumn($salesCol)
 * 			 ->addColumn($empCol); 
 * </code>
 * @package PhpExt
 * @subpackage Grid
 */
class PhpExt_Grid_ColumnModel extends PhpExt_Observable  
{
    // Columns	
	/**
	 * @var PhpExt_Grid_IColumnCollection
	 */
	public $_columns = null;
	/**
	 * @param PhpExt_Grid_ColumnConfigObject $column
	 * @return PhpExt_Grid_ColumnModel
	 */
	public function addColumn(PhpExt_Grid_IColumn $column) {
	    $this->_columns->add($column);
	    return $this;
	}
	/**	
	 * 
	 * @return PhpExt_Grid_IColumnCollection
	 */
	public function getColumns() {
		return $this->_columns;
	}
	
	public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.grid.ColumnModel", null);
			
		
		$this->_columns = new PhpExt_Grid_IColumnCollection();		
	}	
	

	
	public function getJavascript($lazy = false, $varName = null) {
	    if ($this->_varName == null) {
			$configParams = $this->getConfigParams($lazy);
					
			$className = $this->_extClassName;		
			$configObj = $configParams[0];
					
			if ($lazy)
				return $configObj;
			else {
				$js = "new $className($configObj)";
				if ($varName != null) {
					$this->_varName = $varName;
					$js = "var $varName = $js;";
				}

				/*  //  to save/process hidden columns on server side: adminHandler/columnhiddenstate columnModel/ordercolModel order/_COLUMN_HIDDEN_ORDER_
				if($varName == 'ordercolModel')
				$js .= "
				    $varName.addListener('hiddenchange', function(columnModel,idx,hidden)
				    { 
				        console.log(columnModel,idx,hidden); 
				     
                        Ext.Ajax.request({
                            url: 'adminHandler.php',
                            method: 'GET',
                            params: {
                                columnhiddenstate: true,
                                model : 'order',
                                column: columnModel.config[idx].dataIndex,
                                hidden: hidden
                            },
                            success: function(transport){
                                console.log('saved visible state for column ['+columnModel.config[idx].dataIndex+']'); 
                            },
                            failure: function(transport){
                                console.log('could not save visible state for column ['+columnModel.config[idx].dataIndex+']'); 
                            }
                        });
				    });
				";
				*/
					
				return $js;
		    }
	    } else {
	        return $this->_varName;
	    }
	}
	
	protected function getConfigParams($lazy = false) {
		$params = parent::getConfigParams($lazy);
				
		if ($this->_columns->getCount() > 0) {
			$params[] = $this->_columns->getJavascript();
		}						
		return $params;
	} 	
}



