<?php

require_once 'app/Mage.php';


umask(0);
Mage::app();

try {


//    $collection = Mage::getModel('catalogrule/rule')
//        ->getCollection();

    $collection = Mage::getModel('catalogrule/rule')
        ->getCollection();
        //->addFieldToFilter('is_active',1);

//    $collection2 = Mage::getModel('salesrule/rule')
//        ->getCollection()
//        ->addFieldToFilter('is_active',1);


//    date_default_timezone_set('America/Los_Angeles');
//    $script_tz = date_default_timezone_get();

    $current_date = date();
    $path =  realpath(dirname(__FILE__));
    $now = new DateTime('now');

    //$now = new DateTime();
    //$now = datetime.now();
    //$now = date("Y-m-d");
    //echo gettype($now);
    //    $timestamp = Mage::app()->getLocale()->date(null, null, null, true)->get(Zend_Date::TIMESTAMP);
    //    //$timestamp2 = $coreDate->gmtTimestamp('Today');
    //    echo $timestamp;

    print_r($now);
    //echo "\n";

    //    $date1=date_create("2013-03-15");
    //    $date2=date_create("2013-12-12");
    //    $diff=date_diff($date1,$date2);

//    $csv = array_map('str_getcsv', file("$path/last_promo.csv"));
//    $file_exist = false;
//
//    if ($csv != NULL) {
//        $file_exist = true;
//    }
//
//    echo $csv[0][0];
//    echo "\n";

    foreach($collection as $rule) {
        print_r($rule->getRuleId() . " - " . $rule->getName() . " - " . $rule->getIsActive() . " - " . $rule->getFromDate() . " - " . $rule->getToDate());
        print_r("\n");

        $id =  $rule->getRuleId();
        $isactive = $rule->getIsActive();

        $start_date = DateTime::createFromFormat('Y-m-d', $rule->getFromDate());
        $end_date = DateTime::createFromFormat('Y-m-d', $rule->getToDate());

        $diff = date_diff($end_date, $now);

        if ($diff > 0 && $isactive == "0") {
            $rule->setIsActive("1");
            $rule->save();
        }

        print_r("\n");
    }

    echo "\n";

    //$info = date_parse("2018-06-29");
    //var_dump($info['day']);



//    $myfile = fopen("$path/last_promo.csv", "w") or die("Unable to open file!");
//    fwrite($myfile, "hello");
//    fclose($myfile);

    print_r("\n");

    //print_r(basename(__FILE__));
    ///print_r(basename(__DIR__));



    // $csv = array_map('str_getcsv', file('data.csv'));

//    print_r($collection);

    print_r("\n");




    Mage::log(print_r("start applying\n"), null, 'customLogFile.log', true);
    Mage::getModel('catalogrule/rule')->applyAll();
    Mage::app()->removeCache('catalog_rules_dirty');
    Mage::log(print_r("promotion applied\n"), null, 'customLogFile.log', true);
} catch (Exception $e) {
    print_r($e);
    Mage::log(print_r("promotion not applied"), null, 'customLogFile.log', true);
}

