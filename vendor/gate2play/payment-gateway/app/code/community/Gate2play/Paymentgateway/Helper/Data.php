<?php

class Gate2play_Paymentgateway_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getTestUrl()
    {
        return "https://test.ctpe.net/frontend/GenerateToken";
    }
    
    public function getLiveUrl()
    {
        return "https://ctpe.net/frontend/GenerateToken";
    }    

    public function getJsTestUrl()
    {
        return "https://test.ctpe.net/frontend/widget/v3/widget.js?language=en&style=";
    }
    
    public function getJsLiveUrl()
    {
        return "https://ctpe.net/frontend/widget/v3/widget.js?language=en&style=";
    }   
    
    public function getStatusTestUrl()
    {
        return "https://test.ctpe.net/frontend/GetStatus;jsessionid=";
    }
    
    public function getStatusLiveUrl()
    {
        return "https://ctpe.net/frontend/GetStatus;jsessionid=";
    }    
}
