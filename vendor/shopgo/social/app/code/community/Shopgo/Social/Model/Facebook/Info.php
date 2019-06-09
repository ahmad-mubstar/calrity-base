<?php

class Shopgo_Social_Model_Facebook_Info extends Varien_Object
{
    protected $params = array(
        'id',
        'name',
        'first_name',
        'last_name',
        'link',
        'birthday',
        'gender',
        'email',
        'picture.type(large)'
    );

    /**
     * Facebook client model
     *
     * @var Shopgo_Social_Model_Facebook_Oauth2_Client
     */
    protected $client = null;

    public function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('shopgo_social/facebook_oauth2_client');
        if(!($this->client->isEnabled())) {
            return $this;
        }
    }

        /**
     * Get Facebook client model
     *
     * @return Shopgo_Social_Model_Facebook_Oauth2_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Shopgo_Social_Model_Facebook_Oauth2_Client $client)
    {
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    /**
     * Get Facebook client's access token
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
        $this->params['fields'] = 'first_name,last_name,email';
        try{
            $response = $this->client->api(
                '/me',
                'GET',
                $this->params
            );

            foreach ($response as $key => $value) {
                $this->{$key} = $value;
            }

        } catch(Shopgo_Social_Facebook_OAuth2_Exception $e) {
            $this->_onException($e);
        } catch(Exception $e) {
            $this->_onException($e);
        }
    }

    protected function _onException($e)
    {
        if($e instanceof Shopgo_Social_Facebook_OAuth2_Exception) {
            Mage::getSingleton('core/session')->addNotice($e->getMessage());
        } else {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

}