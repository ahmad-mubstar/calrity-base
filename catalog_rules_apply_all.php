<?php

require_once 'app/Mage.php';

umask(0);
Mage::app();

try {

    $collection = Mage::getModel('catalogrule/rule')
        ->getCollection();
        //->addFieldToFilter('is_active',1);

    $collection2 = Mage::getModel('salesrule/rule')
        ->getCollection()
        ->addFieldToFilter('is_active',1);

    //    date_default_timezone_set('America/Los_Angeles');
    //    $script_tz = date_default_timezone_get();

    $current_date = date('now');
    $path =  realpath(dirname(__FILE__));
    $now = new DateTime('now');
    print_r($now);

    foreach($collection as $rule) {
        print_r($rule->getRuleId() . " - " . $rule->getName() . " - " . $rule->getIsActive() . " - " . $rule->getFromDate() . " - " . $rule->getToDate());
        print_r("\n");

        $id =  $rule->getRuleId();
        $isactive = $rule->getIsActive();

        $start_date = DateTime::createFromFormat('Y-m-d', $rule->getFromDate());
        $end_date = DateTime::createFromFormat('Y-m-d', $rule->getToDate());

        $diff = date_diff($end_date, $now);

        print_r($diff);

        if ($diff->d > 0 && $diff->m == 0 && $diff->y == 0 && $isactive == "0") {
            $rule->setIsActive("1");
            $rule->save();
        }
        print_r("\n");
    }

    foreach($collection2 as $rule) {
        print_r($rule->getRuleId() . " - " . $rule->getName() . " - " . $rule->getIsActive() . " - " . $rule->getFromDate() . " - " . $rule->getToDate());
        print_r("\n");

        $id =  $rule->getRuleId();
        $isactive = $rule->getIsActive();

        $start_date = DateTime::createFromFormat('Y-m-d', $rule->getFromDate());
        $end_date = DateTime::createFromFormat('Y-m-d', $rule->getToDate());

        $diff = date_diff($end_date, $now);

        print_r($diff);

        if ($diff->d > 0 && $diff->m == 0 && $diff->y == 0 && $isactive == "0") {
            $rule->setIsActive("1");
            $rule->save();
        }
        //print_r("\n");
    }


    //print_r("\n");

    Mage::log(print_r("start applying\n"), null, 'customLogFile.log', true);
    Mage::getModel('catalogrule/rule')->applyAll();
    Mage::app()->removeCache('catalog_rules_dirty');
    Mage::log(print_r("promotion applied\n"), null, 'customLogFile.log', true);
} catch (Exception $e) {
    print_r($e);
    Mage::log(print_r("promotion not applied"), null, 'customLogFile.log', true);
}

