<?php
$lessphp = Mage::getBaseDir('lib') . DS . 'Shopgo' . DS . 'styleeditor' . DS . 'less.php' . DS . 'Less.php';
require_once($lessphp);

class Shopgo_StyleEditor_Model_Styleeditor extends Mage_Core_Model_Abstract
{
    private $style_editor_variables_path;
    private $style_editor_variables_custom_path;
    private $all = array();


    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'key1',
                'label' => 'Value 1',
            ),
            array(
                'value' => 'key2',
                'label' => 'Value 3',
            ),
        );
    }

    public function getThemeData()
    {
        Mage::getDesign()->setArea('frontend');
        // Mage::app()->getStore()->setStoreId(1);

        $data = array();

        $sections = array();

        $package = Mage::getStoreConfig('design/package/name');
        $skin_name = Mage::getStoreConfig('design/theme/skin');

        // $package_name = Mage::getDesign()->getThemeName();

        // $theme_name = Mage::getSingleton('core/design_package')->getTheme('frontend');
        // $theme_name = Mage::getDesign()->getTheme();
        $theme_name = Mage::getStoreConfig('design/theme/default', 1);

        $style_editor_variables_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';
        $style_editor_variables_custom_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . '_custom' . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';

        if (!file_exists($style_editor_variables_path))
            return (object)array(
                'status' => 'error',
                'message' => 'missing OSEV'
            );

        if (!file_exists($style_editor_variables_custom_path)) {
            $custom_file = fopen($style_editor_variables_custom_path, 'w');
        }

        $this->style_editor_variables_path = $style_editor_variables_path;
        $this->$style_editor_variables_custom_path = $style_editor_variables_custom_path;

        $theme_data = $this->less2array($style_editor_variables_path);

        if (file_exists($style_editor_variables_custom_path)) {
            $theme_data_custom = $this->less2array($style_editor_variables_custom_path);
            $data = array_replace_recursive($theme_data, $theme_data_custom);
            $data['theme_name'] = $theme_data['theme_name'];
            $data['theme_version'] = $theme_data['theme_version'];
        } else {
            $data = $theme_data;
        }

        $data['fonts'] = $this->_getFonts();
        $data['colors'] = $this->_getColors();

        return (object)$data;
    }

    public function saveThemeData($data)
    {
        Mage::getDesign()->setArea('frontend');
        // Mage::app()->getStore()->setStoreId(1);

        $result = array();
        $options = array('compress' => true);
        $less = new Less_Parser($options);

        $package = Mage::getStoreConfig('design/package/name');
        $skin_name = Mage::getStoreConfig('design/theme/skin');

        // $package_name = Mage::getDesign()->getThemeName();

        // $theme_name = Mage::getSingleton('core/design_package')->getTheme('frontend');
        // $theme_name = Mage::getDesign()->getTheme();
        $theme_name = Mage::getStoreConfig('design/theme/default', 1);

        // reading style_editor_variables files original | custom.
        $original_style_editor_variables_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';
        if (!file_exists($original_style_editor_variables_path))
            return array(
                'status' => 'error',
                'message' => 'missing OSEV'
            );

        $custom_style_editor_variables_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . '_custom' . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';
        // if(!file_exists($custom_style_editor_variables_path))
        //     return print json_encode(array(
        //         'status' => 'error',
        //         'message' => 'missing CSEV'
        //     ));

        //$style_editor_variables_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';

        // comparing both files and update the changes.
        // $osev = $this->less2array($original_style_editor_variables_path);
        // $csev = $this->less2array($custom_style_editor_variables_path);

        // comparing user changes and saving the new style_editor_variables file.
        $new_style_editor_variables = '';

        // lines to array
        $style_editor_variables_content = array();

        $handle = fopen($original_style_editor_variables_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                $style_editor_variables_content[] = $line;
            }
            fclose($handle);
        } else {
            // error opening the file.
        }

        // building new lines
        foreach ($style_editor_variables_content as $key => $line) {
            # code...
            foreach ($data as $var => $value) {
                # code...
                if (strstr($line, $var . ':')) {
                    $line = $var . ':       ' . $value . ';';
                }
            }
            if (strstr($line, PHP_EOL) == '')
                $line = $line . PHP_EOL;
            $new_style_editor_variables .= $line;
        }

        $new_style_editor_variables_file_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . '_custom' . DS . 'src' . DS . 'less' . DS . 'style-editor-variables.less';

        // $new_style_editor_variables_file = fopen($new_style_editor_variables_file_path, "w");
        // fwrite($new_style_editor_variables_file, $new_style_editor_variables);
        // fclose($new_style_editor_variables_file);
        file_put_contents($new_style_editor_variables_file_path, $new_style_editor_variables);

        //
        if (!file_exists($custom_style_editor_variables_path)) {

        } else {

        }

        // compiling theme.less
        $theme_less = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . DS . 'src' . DS . 'less' . DS . 'theme.less';
        $theme_css_path = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'shopgo' . DS . $theme_name . '_custom' . DS;
        $theme_css_file = $theme_css_path . 'css' . DS . 'theme.css';
        $theme_images = Mage::getBaseUrl('skin') . 'frontend' . DS . 'shopgo' . DS . Mage::getStoreConfig('design/theme/default', 1) . '_custom' . DS . 'images' . DS;

        try {
            $less->parseFile($theme_less, $theme_images);
            $less->parse( '@skin-path:' . '"' . $theme_css_path . '";' );
            $css = $less->getCss();
            file_put_contents($theme_css_file, $css);
            return array(
                'status' => 'success',
                'message' => 'Changes were successfully saved!'
            );
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        return $result;
    }

    public function uploadThemeImage()
    {
        $result = array('status' => false);
        if (isset($_FILES['file']['name'])
            && ($_FILES['file']['name'] != '')
            && ($_FILES['file']['size'] < 1048576)
        ) {
            try {
                $uploader = new Varien_File_Uploader('file');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));


                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $theme_name = Mage::getStoreConfig('design/theme/default', 1);
                $path = Mage::getBaseDir('skin') . DS . 'frontend'
                    . DS . 'shopgo' . DS . $theme_name . '_custom'
                    . DS . 'images' . DS;

                $uploader->save($path, $_FILES['file']['name']);

                $result['status'] = true;
                $result['file'] = $_FILES['file'];
            } catch (Exception $e) {

            }
        }
        return $result;
    }

    protected function less2array($file_path)
    {

        $result = array();
        if (file_exists($file_path)) {
            $file = file_get_contents($file_path);

            //theme name
            preg_match('/(?<=theme_name:\s).*/', $file, $theme_name);
            $result['theme_name'] = $theme_name[0];

            //theme version
            preg_match('/(?<=theme_version:\s).*/', $file, $theme_version);
            $result['theme_version'] = $theme_version[0];
            // section
            preg_match_all("/section:\s((.|\n)*?)endsection/", $file, $sections);

            foreach ($sections[1] as $section_key => $section) {

                // section title
                preg_match("/[^\@]*/", $section, $section_title);
                $result['sections'][$section_key]['title'] = $section_title[0];

                // section fields
                preg_match_all("/(?<=@)((.)*?)(?=;)/", $section, $fields);
                foreach ($fields[0] as $field_key => $field) {

                    //field type (color, font, setting, YesNo)
                    preg_match('/.*?(?=-)/', $field, $field_type);
                    $result['sections'][$section_key]['fields'][$field_key]['type'] = $field_type[0];

                    //field machine name
                    preg_match('/(?<=-)[^:]*/', $field, $field_name);
                    $result['sections'][$section_key]['fields'][$field_key]['machine_name'] = $field_name[0];

                    //field human name
                    $field_name_human = ucwords(str_replace("-", " ", $field_name[0]));
                    $result['sections'][$section_key]['fields'][$field_key]['human_name'] = $field_name_human;

                    // field value
                    preg_match('/:\s*(.*)/', $field, $field_value);
                    $result['sections'][$section_key]['fields'][$field_key]['value'] = $field_value[1];

                    // field less name
                    $result['sections'][$section_key]['fields'][$field_key]['less_name'] = '@' . $field_type[0] . '-' . $field_name[0];
                }
            }

        }

        return $result;

    }

    protected function _getThemeData($file_path)
    {
        $handle = fopen($file_path, "r");

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $reg = preg_quote('! @');

                $pattern = '/^.*?' . $reg . '(.*)$/m';
                preg_match($pattern, $line, $match);

                if (null == $match)
                    continue;

                $entry = $match[1];
                $entry = explode(':', $entry);

                $entry_data = array();

                $name = $entry[0];
                $value = trim($entry[1]);

                if ($name == 'endsection')
                    continue;

                if ($name == 'theme_name')
                    $data['theme_name'] = $value;

                if ($name == 'theme_version')
                    $data['theme_version'] = $value;

                if ($name == 'section') {
                    $section_flag = false;
                    $section_data = array();
                    $handle1 = fopen($file_path, "r");
                    if ($handle1) {
                        while (($line = fgets($handle1)) !== false) {
                            if (strstr($line, $name . ': ' . $value))
                                $section_flag = true;
                            elseif (strstr($line, 'endsection')) {
                                $section_flag = false;
                                array_shift($section_data);
                                $sections[$value] = $section_data;
                            }
                            if ($section_flag)
                                $section_data[] = $line;
                        }
                        fclose($handle1);
                    }
                }
            }
            fclose($handle);
        } else {
            // error opening the file.
        }

        $handle2 = fopen($file_path, "r");
        if ($handle2) {
            while (($line = fgets($handle2)) !== false) {

                if (strpos($line, 'font-declaration') == 1) {
                    $line = trim(str_replace(';', '', $line));
                    $line = trim(str_replace("'", '', $line));
                    $line = str_replace('@font-declaration: ', '', $line);
                    //
                    $data['font-declaration'] = json_decode($line, true);
                }

                if (strpos($line, 'color-declaration') == 1) {
                    $line = trim(str_replace(';', '', $line));
                    $line = trim(str_replace("'", '', $line));
                    $line = str_replace('@color-declaration: ', '', $line);
                    //
                    $data['color-declaration'] = json_decode($line, true);
                }
            }
            fclose($handle2);
        }
        $data['sections'] = $sections;

        return (object)$data;
    }

    protected function _getFonts()
    {
        //fonts
        $file_contents = file_get_contents($this->style_editor_variables_path);
        preg_match("/(?<=@font-declaration:\s')((.)*?)(?=';)/", $file_contents, $fonts);
        $fonts = json_decode($fonts[0], ture);
        return $fonts;
    }

    protected function _getColors()
    {
        //colors
        $file_contents = file_get_contents($this->style_editor_variables_path);
        preg_match_all("/(?<=@color-declaration:\s')((.)*?)(?=';)/", $file_contents, $colors);
        $colors = str_replace('"', "'", $colors[0][1]);
        return $colors;
    }

}