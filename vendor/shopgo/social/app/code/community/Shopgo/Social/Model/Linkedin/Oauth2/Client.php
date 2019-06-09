<?php

class Shopgo_Social_Model_Linkedin_Oauth2_Client
{
    const REDIRECT_URI_ROUTE = 'social/linkedin/connect';

    const XML_PATH_ENABLED = 'social/linkedin/enabled';
    const XML_PATH_CLIENT_ID = 'social/linkedin/client_id';
    const XML_PATH_CLIENT_SECRET = 'social/linkedin/client_secret';

    const OAUTH2_SERVICE_URI = 'https://api.linkedin.com/v1';
    const OAUTH2_AUTH_URI = 'https://www.linkedin.com/uas/oauth2/authorization';
    const OAUTH2_TOKEN_URI = 'https://www.linkedin.com/uas/oauth2/accessToken';

    protected $clientId = null;
    protected $clientSecret = null;
    protected $redirectUri = null;
    protected $state = '';
    protected $scope = array('r_basicprofile', 'r_emailaddress');

    protected $token = null;

    public function __construct($params = array())
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->clientId = $this->_getClientId();
            $this->clientSecret = $this->_getClientSecret();
            $this->redirectUri = Mage::getModel('core/url')->sessionUrlVar(
                Mage::getUrl(self::REDIRECT_URI_ROUTE)
            );

            if(!empty($params['scope'])) {
                $this->scope = $params['scope'];
            }

            if(!empty($params['state'])) {
                $this->state = $params['state'];
            }
        }
    }

    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setAccessToken($token)
    {
        $this->token = json_decode($token);
    }

    public function getAccessToken()
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        return json_encode($this->token);
    }

    public function createAuthUrl()
    {
        $url =
        self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                array(
                    'response_type' => 'code',
                    'client_id' => $this->clientId,
                    'redirect_uri' => $this->redirectUri,
                    'state' => $this->state,
                    'scope' => implode(',', $this->scope)
                    )
            );
        return $url;
    }

    public function api($endpoint, $method = 'GET', $params = array(), $fields = array())
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        $url = self::OAUTH2_SERVICE_URI.$endpoint;

        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '/'.$key;

                if(!empty($value)) {
                    $url .= '='.$value;
                }
            }
        }

        if(!empty($fields)) {
            $url .= ':(' .implode(',', $fields).')';
        }

        $method = strtoupper($method);

        $params = array_merge(array(
            'oauth2_access_token' => $this->token->access_token,
            'format' => 'json'
        ), $params);

        $response = $this->_httpRequest($url, $method, $params, $fields);

        return $response;
    }

    protected function fetchAccessToken()
    {
        if(!($code = Mage::app()->getRequest()->getParam('code'))) {
            throw new Exception(
                Mage::helper('shopgo_social')
                    ->__('Unable to retrieve access code.')
            );
        }

        $response = $this->_httpRequest(
            self::OAUTH2_TOKEN_URI,
            'POST',
            array(
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code'
            )
        );

        $this->token = $response;
    }

    protected function _httpRequest($url, $method = 'GET', $params = array())
    {
        $client = new Zend_Http_Client($url, array('timeout' => 60));

        switch ($method) {
            case 'GET':
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setParameterGet($params);
                break;
            default:
                throw new Exception(
                    Mage::helper('shopgo_social')
                        ->__('Required HTTP method is not supported.')
                );
        }

        $response = $client->request($method);

        Shopgo_Social_Helper_Data::log($response->getStatus().' - '. $response->getBody());

        $decodedResponse = json_decode($response->getBody());

        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401)) {
                if(isset($decodedResponse->error->message)) {
                    $message = $decodedResponse->error->message;
                } else {
                    $message = Mage::helper('shopgo_social')
                        ->__('Unspecified OAuth error occurred.');
                }

                throw new Shopgo_Social_Model_Linkedin_Oauth2_Exception($message);
            } else {
                $message = sprintf(
                    Mage::helper('shopgo_social')
                        ->__('HTTP error %d occurred while issuing request.'),
                    $status
                );

                throw new Exception($message);
            }
        }

        return $decodedResponse;
    }

    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    protected function _getClientId()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    protected function _getClientSecret()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }

    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }

}