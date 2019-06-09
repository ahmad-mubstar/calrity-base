<?php

class Shopgo_Social_Block_Twitter_Adminhtml_System_Config_Form_Field_Redirects
    extends Shopgo_Social_Block_Adminhtml_System_Config_Form_Field_Redirects
{

    protected function getAuthProvider() 
    {
        return 'twitter';
    }

}