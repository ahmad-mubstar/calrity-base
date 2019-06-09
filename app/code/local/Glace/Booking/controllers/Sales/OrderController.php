<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Sales_OrderController
extends Mage_Adminhtml_Controller_Action
{
	
	public function viewAction(){
		$response = Mage::app()->getResponse();
		$URL = Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/", 
				array(
						"order_id" => $this->getRequest()->getParam('order_id'),
						"key" => $this->getRequest()->getParam('key')
						
				)
		);
		
		$response->setRedirect(
				$URL
		);
	}
		
		public function exportCsvAction()
		{
			$fileName = 'bookind_data.csv';
			$grid = $this->getLayout()->createBlock('booking/adminhtml_book_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}
		public function exportExcelAction()
		{
			$fileName = 'bookind_data.xml';
			$grid = $this->getLayout()->createBlock('booking/adminhtml_book_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
		
	
}