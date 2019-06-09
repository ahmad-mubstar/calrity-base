<?php

    require 'helper.php';
    require_once($_SERVER['DOCUMENT_ROOT'].'/app/Mage.php'); //Path to Magento
    umask(0);
    Mage::app();

    $compList  = getComList();
    $extCount  = count($compList);

    $composer = "/usr/bin/composer";
    $php      = "/opt/nexcess/php55u/root/usr/bin/php";

    putenv("COMPOSER_HOME=".$composer);

    $result = array();

    //detect the requested action IF it {Install || Delete || Update || Delete modgit extension || Delete modman extension}
    switch ($_GET["action"]) {
        case "install":
            install($_GET['ext'],$_GET['version']);
            break;
        case "delete":
            delete($_GET['ext']);
            break;
        case "update":
            action($_GET['ext'],"update");
            break;
        case "removemodgit":
            action($_GET['ext'],"Modgit");
            break;
        case "removemodman":
            action($_GET['ext'],"Modman");
            break;
        case "allowSymlink":
            allowSymlink();
            break;
        default:
            $result[0]="No Thing To Do It";
            echo json_encode($result);
    }

/**
 * Install New Extension By Composer
 *
 * @param string $extName
 * @param string $extVersion
 * @return Output of install command line execution with subtotal composer extensions.
 */
function install($extName, $extVersion)
{
    global $compList,$extCount,$php,$composer;

    $satisList = getSatisList();
    $satisList = $satisList[$extName]["dev-master"]["require"];

    foreach ($satisList as $key => $val) {
        if (!array_key_exists($key, $compList)) {
            $result[0] = $extName . " Extension Depend on <a href=#".$key."> <p  style='color:#FF6611; display: inline;'> ".$key."<p></a>";
            $result[1] = "null";
            echo json_encode($result);
            exit;
        }
    }

    try {

        chdir($_SERVER['DOCUMENT_ROOT']);

        $cmd           = $php.' '.$composer.' require '.$extName.':'.$extVersion." --no-interaction 2>&1";
        $result[0]     = shellCommand($cmd);
        $extCountAfter = extCount();

        if ($extCountAfter>$extCount) {
            $result[1] = $extCountAfter;
            $result[2] = count(getSatisList())-$extCountAfter;
        }
        else
            $result[1] = "null";

        echo json_encode($result);
    }
    catch (Exception $e) {
        $result[0]= 'Caught exception: '. $e->getMessage(). "\n";
        echo json_encode($result);
    }
}

/**
 * Delete Extension By Composer
 *
 * @param string $extName
 * @return Output of remove command line execution with subtotal composer extensions.
 */
function delete($extName)
{
    global $extCount,$php,$composer;

    try {
        chdir($_SERVER['DOCUMENT_ROOT']);

        $cmd           = $php.' '.$composer.' remove '.$extName." --no-interaction 2>&1";
        $result[0]     = shellCommand($cmd);
        $extCountAfter = extCount();

        if ($extCountAfter<$extCount) {
            $result[1] = $extCountAfter;
            $result[2] = count(getSatisList())-$extCountAfter;

            shell_exec('find . ! -path "./app/etc/modules/disabled/*" -type l -xtype l -delete');
        }
        else
            $result[1] = "null";

        echo json_encode($result);

    } catch (Exception $e) {
        $result[0]='Caught exception: '. $e->getMessage(). "\n";
        echo json_encode($result);
    }
}

/**
 * Remove old Modgit Extension
 *
 * @param string $extName
 * @return Output of remove command line execution.
 */
function action($extName,$type)
{
    global $php,$composer;
    try {
        chdir($_SERVER['DOCUMENT_ROOT']);

        switch ($type) {
            case "Modgit":
                $cmd = "./modgit rm ".$extName ." 2>&1";
                break;
            case "Modman":
                $cmd = "~/bin/modman remove ".$extName ." 2>&1";
                break;
            case "update":
                $cmd       = $php.' '.$composer.' update '.$extName." --no-interaction 2>&1";
                break;
        }

        $result[0] = shellCommand($cmd);

        echo json_encode($result);

    } catch (Exception $e) {
        $result[0]='Caught exception: '. $e->getMessage(). "\n";
        echo json_encode($result);
    }
}

/**
 * Set Allow Symlinks Option in BackEnd To On.
 */
function allowSymlink()
{
    Mage::getModel('core/config')->saveConfig('dev/template/allow_symlink', 1, 'default', 0);
    Mage::app()->cleanCache();

    echo " Symlinks has been changed to on";
}

/**
 * Execute Shell Command Line
 *
 * @param string $cmd
 * @return Output of shell command line execution.
 */
function shellCommand($cmd){

    while (@ ob_end_flush()); // end all output buffers if any
    $proc = popen($cmd, 'r');
    $res='<p>';
    while (!feof($proc)) {
        $res= $res. fread($proc, 4096);
        $res= $res. "</br>";
        @ flush();
    }
    $res = $res.'</p>';
    return $res;
}

/**
 * Count all extensions was installed by composer
 *
 * @return subtotal of composer  extensions.
 */
function extCount()
{
    $compFile = file_get_contents("composer.json");
    $compList = json_decode($compFile, true);
    $compList = $compList['require'];
    return $extCountAfter=count($compList);

}