<?php

class Shopgo_Social_Block_Twitter_Adminhtml_System_Config_Form_Field_Links
    extends Shopgo_Social_Block_Adminhtml_System_Config_Form_Field_Links
{

    protected function getAuthProviderLink()
    {
        return 'Twitter Developers';
    }

    protected function getAuthProviderLinkHref()
    {
        return 'https://dev.twitter.com/';
    }
    
}