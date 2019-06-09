<?php
class Shopgo_EC_Model_Resources_Citiesdatasources
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'no_data_source', 'label' => 'No data source'),
            // CITIES INTEGRATION: START
            array('value' => 'cities_api', 'label' => 'Cities API'),
            // CITIES INTEGRATION: END
            array('value' => 'geonames_api', 'label' => 'Geonames API'),
            array('value' => 'aramex_api', 'label' => 'Aramex Location API - Custom account'),
            array('value' => 'aramex_api_shipping_module', 'label' => 'Aramex Location API - Shipping extension'),
            // array('value' => 'csv_table', 'label' => 'CSV table'),
          );
    }
}
