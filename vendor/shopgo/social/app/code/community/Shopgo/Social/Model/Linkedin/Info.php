<?php

class Shopgo_Social_Model_Linkedin_Info extends Varien_Object
{
    protected $params = array(
        '~' => ''
    );

    protected $fields = array(
        'id',
        'first-name',
        'last-name',
        'email-address',
        'picture-url',
        'public-profile-url',
        'site-standard-profile-request'
    );

    /**
     * LinkedIn client model
     *
     * @var Shopgo_Social_Model_Linkedin_Oauth2_Client
     */
    protected $client = null;

    public function _construct() {
        parent::_construct();
        
        $this->client = Mage::getSingleton('shopgo_social/linkedin_oauth2_client');
        if(!($this->client->isEnabled())) {
            return $this;
        }
    }

    /**
     * Get LinkedIn client model
     *
     * @return Shopgo_Social_Model_Linkedin_Oauth2_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Shopgo_Social_Model_Linkedin_Oauth2_Client $client)
    {
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    /**
     * Get LinkedIn client's access token
     *
     * @return stdClass
     */
    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }

    public function load($id = null)
    {
        $this->_load();

        return $this;
    }

    protected function _load()
    {
        try{
            $response = $this->client->api(
                '/people',
                'GET',
                $this->params,
                $this->fields
            );

            foreach ($response as $key => $value) {
                $this->{$key} = $value;
            }

        } catch(Shopgo_Social_Linkedin_Oauth2_Exception $e) {
            $this->_onException($e);
        } catch(Exception $e) {
            $this->_onException($e);
        }
    }

    protected function _onException($e)
    {
        if($e instanceof Shopgo_Social_Linkedin_Oauth2_Exception) {
            Mage::getSingleton('core/session')->addNotice($e->getMessage());
        } else {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

}