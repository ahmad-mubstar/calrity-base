<?php

class Shopgo_EC_Adminhtml_EcController extends Mage_Adminhtml_Controller_Action
{
    public function checkUpdateAction()
    {
        $extensions = json_decode(file_get_contents('http://downloader.devshopgo.me/'), true);
        if(isset($extensions['Shopgo_EC'])) {
            $extension_data = $extensions['Shopgo_EC'];
        }
    }

    public function updateAction()
    {
        if($extension_data = Mage::helper('ec')->checkUpdate()) {
            
            $git_repo = $extension_data['git_repo'];
            $package_url = 'http://downloader.devshopgo.me/download.php?filename=' . $git_repo;

            $package_path = Mage::getBaseDir() . DS . 'var' . DS . 'ec' . DS . 'releases' . DS . $extension_data['version'] . DS . 'ec_' . str_replace('.', '_', $extension_data['version']) . '.tar.gz';

            $package_path_str = $package_path;

            $dirname = dirname($package_path);
            if (!is_dir($dirname))
            {
                mkdir($dirname, 0755, true);
            }

            $package_path = fopen($package_path, 'wb');

            try {
                set_time_limit(0); // unlimited max execution time
                

                // $options = array(
                //     CURLOPT_FILE    => Mage::getBaseDir() . DS . 'var' . DS . 'ec' . DS . 'releases' . DS . $extension_data['version'] . DS . 'ec_' . str_replace('.', '_', $extension_data['version']) . '.tar.gz',
                //     CURLOPT_TIMEOUT =>  28800, // set this to 8 hours so we dont timeout on big files
                //     // CURLOPT_URL     => $package_url,
                //     CURLOPT_FTP_CREATE_MISSING_DIRS  => true,
                //     CURLOPT_RETURNTRANSFER  => true,
                //     // CURLOPT_SSLVERSION = 3,
                //     CURLOPT_SSL_VERIFYPEER  => false,
                //     CURLOPT_HEADER => false,
                //     CURLOPT_GET => true,
                // );

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, 1);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                curl_setopt($ch, CURLOPT_TIMEOUT, 28800);
                
                curl_setopt($ch, CURLOPT_HEADER, 0);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                // set url 
                curl_setopt($ch, CURLOPT_URL, $package_url);

                // set file
                curl_setopt($ch, CURLOPT_FILE, $package_path);

                // curl_setopt_array($ch, $options);
                curl_exec($ch);

                $error = curl_error($ch);

                curl_close($ch);

                $output = shell_exec('tar -xvf ' . $package_path_str . ' --strip 1 -C ' . Mage::getBaseDir() . DS);

                // remove package
                shell_exec('rm -rf ' . $package_path_str);

                // clean the cache
                Mage::app()->cleanCache();

                $result = array(
                    'status' => $error ? 'error' : 'success',
                    'message' => $error ? $error : $this->__('Updated successfully!')
                );
                
                print json_encode($result);

            } catch(Mage_Exception $e) {

                $result = array(
                'status' => 'error',
                'message' => $e->getMessage()
                );
                
                print json_encode($result);
            }  
        }
    }
}
