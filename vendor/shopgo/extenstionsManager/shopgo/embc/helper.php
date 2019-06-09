<?php

    /**
     * Get all satis extensions list
     *
     * @return Array of satis extensions list.
     */
    function getSatisList()
    {
        $satisFile = file_get_contents('http://list.magento1.satis.shopgo.io');
        $satisList = json_decode($satisFile,true);
        $satisList = $satisList["packages"];

        return $satisList;
    }

    /**
     * Get all composer.json extensions list
     *
     * @return Array of composer.json extensions list
     */
    function getComList()
    {
        $compLFile = file_get_contents($_SERVER['DOCUMENT_ROOT']."/composer.json");
        $compList  = json_decode($compLFile, true);
        $compList  = $compList["require"];

        return $compList;
    }