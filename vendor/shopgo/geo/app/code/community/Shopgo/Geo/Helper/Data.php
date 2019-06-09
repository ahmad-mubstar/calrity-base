<?php

class Shopgo_Geo_Helper_Data extends Mage_Core_Helper_Abstract 
{
    public function isEnabled() {
        return Mage::getStoreConfig('geo/general/active');
    }

	/**
	 * Get size of remote file
	 *
	 * @param $file
	 * @return mixed
	 */
	public function getSize($file) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		return curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	}

	/**
	 * Extracts single gzipped file. If archive will contain more than one file you will got a mess.
	 *
	 * @param $archive
	 * @param $destination
	 * @return int
	 */
	public function unGZip($archive, $destination) {
		$buffer_size = 4096;
		// read 4kb at a time
		$archive = gzopen($archive, 'rb');
		$dat = fopen($destination, 'wb');
		while (!gzeof($archive)) {
			fwrite($dat, gzread($archive, $buffer_size));
		}
		fclose($dat);
		gzclose($archive);
		return filesize($destination);
	}

	/* gets the data from a URL */
    public function getData($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Get IP Address
     *
     * @return string
     */
    public function getIpAddress() {          
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check whether the given IP Address is valid
     * 
     * @param string IP Address
     * @return boolean True/False
     */
    public function checkValidIp($ip) {           
        if(!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }       
        return true;
    }
    
    /**
     * Check whether the given IP Address is IPv6
     * 
     * @param string IP Address
     * @return boolean True/False
     */
    public function checkIpv6($ip) {           
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return false;
        }       
        return true;
    }

    public function getStoreByCode($storeCode) {
        $stores = array_keys(Mage::app()->getStores());
        
        foreach($stores as $id){
            $store = Mage::app()->getStore($id);
            if($store->getCode()==$storeCode) {
                return $store;
            }
        }
         return null; // if not found
     }


     public function getLocaleList() {
        return array(
            'aa_DJ' => 'Afar (Djibouti)',
            'aa_ER' => 'Afar (Eritrea)',
            'aa_ET' => 'Afar (Ethiopia)',
            'af_ZA' => 'Afrikaans (South Africa)',
            'sq_AL' => 'Albanian (Albania)',
            'sq_MK' => 'Albanian (Macedonia)',
            'am_ET' => 'Amharic (Ethiopia)',
            'ar_DZ' => 'Arabic (Algeria)',
            'ar_BH' => 'Arabic (Bahrain)',
            'ar_EG' => 'Arabic (Egypt)',
            'ar_IN' => 'Arabic (India)',
            'ar_IQ' => 'Arabic (Iraq)',
            'ar_JO' => 'Arabic (Jordan)',
            'ar_KW' => 'Arabic (Kuwait)',
            'ar_LB' => 'Arabic (Lebanon)',
            'ar_LY' => 'Arabic (Libya)',
            'ar_MA' => 'Arabic (Morocco)',
            'ar_OM' => 'Arabic (Oman)',
            'ar_QA' => 'Arabic (Qatar)',
            'ar_SA' => 'Arabic (Saudi Arabia)',
            'ar_SD' => 'Arabic (Sudan)',
            'ar_SY' => 'Arabic (Syria)',
            'ar_TN' => 'Arabic (Tunisia)',
            'ar_AE' => 'Arabic (United Arab Emirates)',
            'ar_YE' => 'Arabic (Yemen)',
            'an_ES' => 'Aragonese (Spain)',
            'hy_AM' => 'Armenian (Armenia)',
            'as_IN' => 'Assamese (India)',
            'ast_ES' => 'Asturian (Spain)',
            'az_AZ' => 'Azerbaijani (Azerbaijan)',
            'az_TR' => 'Azerbaijani (Turkey)',
            'eu_FR' => 'Basque (France)',
            'eu_ES' => 'Basque (Spain)',
            'be_BY' => 'Belarusian (Belarus)',
            'bem_ZM' => 'Bemba (Zambia)',
            'bn_BD' => 'Bengali (Bangladesh)',
            'bn_IN' => 'Bengali (India)',
            'ber_DZ' => 'Berber (Algeria)',
            'ber_MA' => 'Berber (Morocco)',
            'byn_ER' => 'Blin (Eritrea)',
            'bs_BA' => 'Bosnian (Bosnia and Herzegovina)',
            'br_FR' => 'Breton (France)',
            'bg_BG' => 'Bulgarian (Bulgaria)',
            'my_MM' => 'Burmese (Myanmar [Burma])',
            'ca_AD' => 'Catalan (Andorra)',
            'ca_FR' => 'Catalan (France)',
            'ca_IT' => 'Catalan (Italy)',
            'ca_ES' => 'Catalan (Spain)',
            'zh_CN' => 'Chinese (China)',
            'zh_HK' => 'Chinese (Hong Kong SAR China)',
            'zh_SG' => 'Chinese (Singapore)',
            'zh_TW' => 'Chinese (Taiwan)',
            'cv_RU' => 'Chuvash (Russia)',
            'kw_GB' => 'Cornish (United Kingdom)',
            'crh_UA' => 'Crimean Turkish (Ukraine)',
            'hr_HR' => 'Croatian (Croatia)',
            'cs_CZ' => 'Czech (Czech Republic)',
            'da_DK' => 'Danish (Denmark)',
            'dv_MV' => 'Divehi (Maldives)',
            'nl_AW' => 'Dutch (Aruba)',
            'nl_BE' => 'Dutch (Belgium)',
            'nl_NL' => 'Dutch (Netherlands)',
            'dz_BT' => 'Dzongkha (Bhutan)',
            'en_AG' => 'English (Antigua and Barbuda)',
            'en_AU' => 'English (Australia)',
            'en_BW' => 'English (Botswana)',
            'en_CA' => 'English (Canada)',
            'en_DK' => 'English (Denmark)',
            'en_HK' => 'English (Hong Kong SAR China)',
            'en_IN' => 'English (India)',
            'en_IE' => 'English (Ireland)',
            'en_NZ' => 'English (New Zealand)',
            'en_NG' => 'English (Nigeria)',
            'en_PH' => 'English (Philippines)',
            'en_SG' => 'English (Singapore)',
            'en_ZA' => 'English (South Africa)',
            'en_GB' => 'English (United Kingdom)',
            'en_US' => 'English (United States)',
            'en_ZM' => 'English (Zambia)',
            'en_ZW' => 'English (Zimbabwe)',
            'eo' => 'Esperanto',
            'et_EE' => 'Estonian (Estonia)',
            'fo_FO' => 'Faroese (Faroe Islands)',
            'fil_PH' => 'Filipino (Philippines)',
            'fi_FI' => 'Finnish (Finland)',
            'fr_BE' => 'French (Belgium)',
            'fr_CA' => 'French (Canada)',
            'fr_FR' => 'French (France)',
            'fr_LU' => 'French (Luxembourg)',
            'fr_CH' => 'French (Switzerland)',
            'fur_IT' => 'Friulian (Italy)',
            'ff_SN' => 'Fulah (Senegal)',
            'gl_ES' => 'Galician (Spain)',
            'lg_UG' => 'Ganda (Uganda)',
            'gez_ER' => 'Geez (Eritrea)',
            'gez_ET' => 'Geez (Ethiopia)',
            'ka_GE' => 'Georgian (Georgia)',
            'de_AT' => 'German (Austria)',
            'de_BE' => 'German (Belgium)',
            'de_DE' => 'German (Germany)',
            'de_LI' => 'German (Liechtenstein)',
            'de_LU' => 'German (Luxembourg)',
            'de_CH' => 'German (Switzerland)',
            'el_CY' => 'Greek (Cyprus)',
            'el_GR' => 'Greek (Greece)',
            'gu_IN' => 'Gujarati (India)',
            'ht_HT' => 'Haitian (Haiti)',
            'ha_NG' => 'Hausa (Nigeria)',
            'iw_IL' => 'Hebrew (Israel)',
            'he_IL' => 'Hebrew (Israel)',
            'hi_IN' => 'Hindi (India)',
            'hu_HU' => 'Hungarian (Hungary)',
            'is_IS' => 'Icelandic (Iceland)',
            'ig_NG' => 'Igbo (Nigeria)',
            'id_ID' => 'Indonesian (Indonesia)',
            'ia' => 'Interlingua',
            'iu_CA' => 'Inuktitut (Canada)',
            'ik_CA' => 'Inupiaq (Canada)',
            'ga_IE' => 'Irish (Ireland)',
            'it_IT' => 'Italian (Italy)',
            'it_CH' => 'Italian (Switzerland)',
            'ja_JP' => 'Japanese (Japan)',
            'kl_GL' => 'Kalaallisut (Greenland)',
            'kn_IN' => 'Kannada (India)',
            'ks_IN' => 'Kashmiri (India)',
            'csb_PL' => 'Kashubian (Poland)',
            'kk_KZ' => 'Kazakh (Kazakhstan)',
            'km_KH' => 'Khmer (Cambodia)',
            'rw_RW' => 'Kinyarwanda (Rwanda)',
            'ky_KG' => 'Kirghiz (Kyrgyzstan)',
            'kok_IN' => 'Konkani (India)',
            'ko_KR' => 'Korean (South Korea)',
            'ku_TR' => 'Kurdish (Turkey)',
            'lo_LA' => 'Lao (Laos)',
            'lv_LV' => 'Latvian (Latvia)',
            'li_BE' => 'Limburgish (Belgium)',
            'li_NL' => 'Limburgish (Netherlands)',
            'lt_LT' => 'Lithuanian (Lithuania)',
            'nds_DE' => 'Low German (Germany)',
            'nds_NL' => 'Low German (Netherlands)',
            'mk_MK' => 'Macedonian (Macedonia)',
            'mai_IN' => 'Maithili (India)',
            'mg_MG' => 'Malagasy (Madagascar)',
            'ms_MY' => 'Malay (Malaysia)',
            'ml_IN' => 'Malayalam (India)',
            'mt_MT' => 'Maltese (Malta)',
            'gv_GB' => 'Manx (United Kingdom)',
            'mi_NZ' => 'Maori (New Zealand)',
            'mr_IN' => 'Marathi (India)',
            'mn_MN' => 'Mongolian (Mongolia)',
            'ne_NP' => 'Nepali (Nepal)',
            'se_NO' => 'Northern Sami (Norway)',
            'nso_ZA' => 'Northern Sotho (South Africa)',
            'nb_NO' => 'Norwegian Bokmål (Norway)',
            'nn_NO' => 'Norwegian Nynorsk (Norway)',
            'oc_FR' => 'Occitan (France)',
            'or_IN' => 'Oriya (India)',
            'om_ET' => 'Oromo (Ethiopia)',
            'om_KE' => 'Oromo (Kenya)',
            'os_RU' => 'Ossetic (Russia)',
            'pap_AN' => 'Papiamento (Netherlands Antilles)',
            'ps_AF' => 'Pashto (Afghanistan)',
            'fa_IR' => 'Persian (Iran)',
            'pl_PL' => 'Polish (Poland)',
            'pt_BR' => 'Portuguese (Brazil)',
            'pt_PT' => 'Portuguese (Portugal)',
            'pa_IN' => 'Punjabi (India)',
            'pa_PK' => 'Punjabi (Pakistan)',
            'ro_RO' => 'Romanian (Romania)',
            'ru_RU' => 'Russian (Russia)',
            'ru_UA' => 'Russian (Ukraine)',
            'sa_IN' => 'Sanskrit (India)',
            'sc_IT' => 'Sardinian (Italy)',
            'gd_GB' => 'Scottish Gaelic (United Kingdom)',
            'sr_ME' => 'Serbian (Montenegro)',
            'sr_RS' => 'Serbian (Serbia)',
            'sid_ET' => 'Sidamo (Ethiopia)',
            'sd_IN' => 'Sindhi (India)',
            'si_LK' => 'Sinhala (Sri Lanka)',
            'sk_SK' => 'Slovak (Slovakia)',
            'sl_SI' => 'Slovenian (Slovenia)',
            'so_DJ' => 'Somali (Djibouti)',
            'so_ET' => 'Somali (Ethiopia)',
            'so_KE' => 'Somali (Kenya)',
            'so_SO' => 'Somali (Somalia)',
            'nr_ZA' => 'South Ndebele (South Africa)',
            'st_ZA' => 'Southern Sotho (South Africa)',
            'es_AR' => 'Spanish (Argentina)',
            'es_BO' => 'Spanish (Bolivia)',
            'es_CL' => 'Spanish (Chile)',
            'es_CO' => 'Spanish (Colombia)',
            'es_CR' => 'Spanish (Costa Rica)',
            'es_DO' => 'Spanish (Dominican Republic)',
            'es_EC' => 'Spanish (Ecuador)',
            'es_SV' => 'Spanish (El Salvador)',
            'es_GT' => 'Spanish (Guatemala)',
            'es_HN' => 'Spanish (Honduras)',
            'es_MX' => 'Spanish (Mexico)',
            'es_NI' => 'Spanish (Nicaragua)',
            'es_PA' => 'Spanish (Panama)',
            'es_PY' => 'Spanish (Paraguay)',
            'es_PE' => 'Spanish (Peru)',
            'es_ES' => 'Spanish (Spain)',
            'es_US' => 'Spanish (United States)',
            'es_UY' => 'Spanish (Uruguay)',
            'es_VE' => 'Spanish (Venezuela)',
            'sw_KE' => 'Swahili (Kenya)',
            'sw_TZ' => 'Swahili (Tanzania)',
            'ss_ZA' => 'Swati (South Africa)',
            'sv_FI' => 'Swedish (Finland)',
            'sv_SE' => 'Swedish (Sweden)',
            'tl_PH' => 'Tagalog (Philippines)',
            'tg_TJ' => 'Tajik (Tajikistan)',
            'ta_IN' => 'Tamil (India)',
            'tt_RU' => 'Tatar (Russia)',
            'te_IN' => 'Telugu (India)',
            'th_TH' => 'Thai (Thailand)',
            'bo_CN' => 'Tibetan (China)',
            'bo_IN' => 'Tibetan (India)',
            'tig_ER' => 'Tigre (Eritrea)',
            'ti_ER' => 'Tigrinya (Eritrea)',
            'ti_ET' => 'Tigrinya (Ethiopia)',
            'ts_ZA' => 'Tsonga (South Africa)',
            'tn_ZA' => 'Tswana (South Africa)',
            'tr_CY' => 'Turkish (Cyprus)',
            'tr_TR' => 'Turkish (Turkey)',
            'tk_TM' => 'Turkmen (Turkmenistan)',
            'ug_CN' => 'Uighur (China)',
            'uk_UA' => 'Ukrainian (Ukraine)',
            'hsb_DE' => 'Upper Sorbian (Germany)',
            'ur_PK' => 'Urdu (Pakistan)',
            'uz_UZ' => 'Uzbek (Uzbekistan)',
            've_ZA' => 'Venda (South Africa)',
            'vi_VN' => 'Vietnamese (Vietnam)',
            'wa_BE' => 'Walloon (Belgium)',
            'cy_GB' => 'Welsh (United Kingdom)',
            'fy_DE' => 'Western Frisian (Germany)',
            'fy_NL' => 'Western Frisian (Netherlands)',
            'wo_SN' => 'Wolof (Senegal)',
            'xh_ZA' => 'Xhosa (South Africa)',
            'yi_US' => 'Yiddish (United States)',
            'yo_NG' => 'Yoruba (Nigeria)',
            'zu_ZA' => 'Zulu (South Africa)'
            );
     }

     /**
      * Get Currency code by Country code
      * 
      * @return string
      */ 
     public function getCurrencyByCountry($countryCode) {
          $map = array( '' => '',
          "EU" => "EUR", "AD" => "EUR", "AE" => "AED", "AF" => "AFN", "AG" => "XCD", "AI" => "XCD", 
          "AL" => "ALL", "AM" => "AMD", "CW" => "ANG", "AO" => "AOA", "AQ" => "AQD", "AR" => "ARS", "AS" => "EUR", 
          "AT" => "EUR", "AU" => "AUD", "AW" => "AWG", "AZ" => "AZN", "BA" => "BAM", "BB" => "BBD", 
          "BD" => "BDT", "BE" => "EUR", "BF" => "XOF", "BG" => "BGL", "BH" => "BHD", "BI" => "BIF", 
          "BJ" => "XOF", "BM" => "BMD", "BN" => "BND", "BO" => "BOB", "BR" => "BRL", "BS" => "BSD", 
          "BT" => "BTN", "BV" => "NOK", "BW" => "BWP", "BY" => "BYR", "BZ" => "BZD", "CA" => "CAD", 
          "CC" => "AUD", "CD" => "CDF", "CF" => "XAF", "CG" => "XAF", "CH" => "CHF", "CI" => "XOF", 
          "CK" => "NZD", "CL" => "CLP", "CM" => "XAF", "CN" => "CNY", "CO" => "COP", "CR" => "CRC", 
          "CU" => "CUP", "CV" => "CVE", "CX" => "AUD", "CY" => "EUR", "CZ" => "CZK", "DE" => "EUR", 
          "DJ" => "DJF", "DK" => "DKK", "DM" => "XCD", "DO" => "DOP", "DZ" => "DZD", "EC" => "ECS", 
          "EE" => "EEK", "EG" => "EGP", "EH" => "MAD", "ER" => "ETB", "ES" => "EUR", "ET" => "ETB", 
          "FI" => "EUR", "FJ" => "FJD", "FK" => "FKP", "FM" => "USD", "FO" => "DKK", "FR" => "EUR", "SX" => "ANG",
          "GA" => "XAF", "GB" => "GBP", "GD" => "XCD", "GE" => "GEL", "GF" => "EUR", "GH" => "GHS", 
          "GI" => "GIP", "GL" => "DKK", "GM" => "GMD", "GN" => "GNF", "GP" => "EUR", "GQ" => "XAF", 
          "GR" => "EUR", "GS" => "GBP", "GT" => "GTQ", "GU" => "USD", "GW" => "XOF", "GY" => "GYD", 
          "HK" => "HKD", "HM" => "AUD", "HN" => "HNL", "HR" => "HRK", "HT" => "HTG", "HU" => "HUF", 
          "ID" => "IDR", "IE" => "EUR", "IL" => "ILS", "IN" => "INR", "IO" => "USD", "IQ" => "IQD", 
          "IR" => "IRR", "IS" => "ISK", "IT" => "EUR", "JM" => "JMD", "JO" => "JOD", "JP" => "JPY", 
          "KE" => "KES", "KG" => "KGS", "KH" => "KHR", "KI" => "AUD", "KM" => "KMF", "KN" => "XCD", 
          "KP" => "KPW", "KR" => "KRW", "KW" => "KWD", "KY" => "KYD", "KZ" => "KZT", "LA" => "LAK", 
          "LB" => "LBP", "LC" => "XCD", "LI" => "CHF", "LK" => "LKR", "LR" => "LRD", "LS" => "LSL", 
          "LT" => "LTL", "LU" => "EUR", "LV" => "LVL", "LY" => "LYD", "MA" => "MAD", "MC" => "EUR", 
          "MD" => "MDL", "MG" => "MGF", "MH" => "USD", "MK" => "MKD", "ML" => "XOF", "MM" => "MMK", 
          "MN" => "MNT", "MO" => "MOP", "MP" => "USD", "MQ" => "EUR", "MR" => "MRO", "MS" => "XCD", 
          "MT" => "EUR", "MU" => "MUR", "MV" => "MVR", "MW" => "MWK", "MX" => "MXN", "MY" => "MYR", 
          "MZ" => "MZN", "NA" => "NAD", "NC" => "XPF", "NE" => "XOF", "NF" => "AUD", "NG" => "NGN", 
          "NI" => "NIO", "NL" => "EUR", "NO" => "NOK", "NP" => "NPR", "NR" => "AUD", "NU" => "NZD", 
          "NZ" => "NZD", "OM" => "OMR", "PA" => "PAB", "PE" => "PEN", "PF" => "XPF", "PG" => "PGK", 
          "PH" => "PHP", "PK" => "PKR", "PL" => "PLN", "PM" => "EUR", "PN" => "NZD", "PR" => "USD", "PS" => "ILS", "PT" => "EUR", 
          "PW" => "USD", "PY" => "PYG", "QA" => "QAR", "RE" => "EUR", "RO" => "RON", "RU" => "RUB", 
          "RW" => "RWF", "SA" => "SAR", "SB" => "SBD", "SC" => "SCR", "SD" => "SDD", "SE" => "SEK", 
          "SG" => "SGD", "SH" => "SHP", "SI" => "EUR", "SJ" => "NOK", "SK" => "SKK", "SL" => "SLL", 
          "SM" => "EUR", "SN" => "XOF", "SO" => "SOS", "SR" => "SRG", "ST" => "STD", "SV" => "SVC", 
          "SY" => "SYP", "SZ" => "SZL", "TC" => "USD", "TD" => "XAF", "TF" => "EUR", "TG" => "XOF", 
          "TH" => "THB", "TJ" => "TJS", "TK" => "NZD", "TM" => "TMM", "TN" => "TND", "TO" => "TOP", "TL" => "USD",
          "TR" => "TRY", "TT" => "TTD", "TV" => "AUD", "TW" => "TWD", "TZ" => "TZS", "UA" => "UAH", 
          "UG" => "UGX", "UM" => "USD", "US" => "USD", "UY" => "UYU", "UZ" => "UZS", "VA" => "EUR", 
          "VC" => "XCD", "VE" => "VEF", "VG" => "USD", "VI" => "USD", "VN" => "VND", "VU" => "VUV",
          "WF" => "XPF", "WS" => "EUR", "YE" => "YER", "YT" => "EUR", "RS" => "RSD", 
          "ZA" => "ZAR", "ZM" => "ZMK", "ME" => "EUR", "ZW" => "ZWD",
          "AX" => "EUR", "GG" => "GBP", "IM" => "GBP", 
          "JE" => "GBP", "BL" => "EUR", "MF" => "EUR", "BQ" => "USD", "SS" => "SSP"
          );
          
          return $map[$countryCode];
     }
}
