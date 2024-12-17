<?php


// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P'))
{
    e107::redirect('admin');
    exit;
}

// e107::lan('glyphs',true);


class glyphs_adminArea extends e_admin_dispatcher
{

    protected $modes = array(

        'main'    => array(
            'controller'     => 'glyphs_ui',
            'path'             => null,
            'ui'             => 'glyphs_form_ui',
            'uipath'         => null
        ),


    );


    protected $adminMenu = array(

        'main/prefs'         => array('caption' => LAN_PREFS, 'perm' => 'P'),
        'main/packs'         => array('caption' => "Check", 'perm' => 'P'),

        // 'main/div0'      => array('divider'=> true),
        // 'main/custom'		=> array('caption'=> 'Custom Page', 'perm' => 'P'),

    );

    protected $adminMenuAliases = array(
        'main/edit'    => 'main/list'
    );

    protected $menuTitle = 'Glyphs';
}





class glyphs_ui extends e_admin_ui
{

    protected $pluginTitle        = 'Glyphs';
    protected $pluginName        = 'glyphs';
    //	protected $eventName		= 'glyphs-'; // remove comment to enable event triggers in admin. 		
 
    protected $fieldpref = array();
 
    //	protected $preftabs        = array('General', 'Other' );
    protected $prefs = array(
        'packs'        => array('title' => 'Supported Packs', 'tab' => 0, 'type' => 'method', 'data' => 'json', 'help' => '', 'writeParms' => []),
    );


    public function init()
    {
 

    }

    /**
     * User defined before pref saving logic
     * @param $new_data
     * @param $old_data
     * @return null
     */
    public function afterPrefsSave()
    {
        $path = e_PLUGIN  . "glyphs/glyphs.xml";

        $xml = e107::getXml();

        $vars = $xml->loadXMLfile($path, 'advanced');

        $vars['glyphs'] = array();

        if (!empty($vars['glyphicons']['glyph']))
        {
            foreach ($vars['glyphicons']['glyph'] as $val)
            {
                if (isset($val['@attributes']['name']))  $name =  $val['@attributes']['name'];
                else continue;

                $vars['glyphs'][$name] = array(
                    'name'    => $name,
                    'pattern' => isset($val['@attributes']['pattern']) ? $val['@attributes']['pattern'] : '',
                    'path'    => isset($val['@attributes']['path']) ? e107::getParser()->replaceConstants($val['@attributes']['path'], 'full') : '',
                    'class'   => isset($val['@attributes']['class']) ? $val['@attributes']['class'] : '',
                    'prefix'  => isset($val['@attributes']['prefix']) ? $val['@attributes']['prefix'] : '',
                    'tag'     => isset($val['@attributes']['tag']) ? $val['@attributes']['tag'] : '',
                );
            }

            unset($vars['glyphicons']);
        }

        /* supported packs only */
        $curVal = e107::pref('glyphs', 'packs');
        $packs = e107::unserialize($curVal);
        $filteredPacks = array_filter($packs, function ($value)
        {
            return $value == 1; // Retain only keys with a value of 1
        });

        $glyphs =  $vars['glyphs'];
 
        $supportedPacks = array_intersect_key($glyphs, $filteredPacks);
        $config = e107::getConfig();
 
        $config->set('sitetheme_glyphicons', $supportedPacks);

        // Attempt to save changes and provide feedback
        
        if ($config->save())
        {
            // Clear the cache to reflect the latest changes
            e107::getCache()->clear('core_prefs');

            // Inform the user about the successful update
            e107::getMessage()->addSuccess('Core preferences updated successfully.');
        }

        return null;
    }


    public function packsPage()
    {
        $tmp = e107::getPref('sitetheme_glyphicons');
        echo "Actual Site preferences: <br>";
        print_a($tmp);

        /* supported packs only */
        $curVal = e107::pref('glyphs', 'packs');
        $packs = e107::unserialize($curVal);
        $filteredPacks = array_filter($packs, function ($value)
        {
            return $value == 1; // Retain only keys with a value of 1
        });
        echo "Actual supported packs by plugin:";
        print_a($filteredPacks);
 
        $path = e_PLUGIN  . "glyphs/glyphs.xml";

        $xml = e107::getXml();

        $vars = $xml->loadXMLfile($path, 'advanced');

        $vars['glyphs'] = array();

        if (!empty($vars['glyphicons']['glyph']))
        {
            foreach ($vars['glyphicons']['glyph'] as $val)
            {
                if (isset($val['@attributes']['name']))  $name =  $val['@attributes']['name'];
                else continue;

                $vars['glyphs'][$name] = array(
                    'name'    => $name,
                    'pattern' => isset($val['@attributes']['pattern']) ? $val['@attributes']['pattern'] : '',
                    'path'    => isset($val['@attributes']['path']) ? e107::getParser()->replaceConstants($val['@attributes']['path'], 'full') : '',
                    'class'   => isset($val['@attributes']['class']) ? $val['@attributes']['class'] : '',
                    'prefix'  => isset($val['@attributes']['prefix']) ? $val['@attributes']['prefix'] : '',
                    'tag'     => isset($val['@attributes']['tag']) ? $val['@attributes']['tag'] : '',
                );
            }

            unset($vars['glyphicons']);
        }

        $glyphs =  $vars['glyphs'];

        echo "Available packs:";
        print_a($glyphs);

        // $supportedPacks = array_intersect_key($glyphs, $filteredPacks);
        // $config = e107::getConfig();
 
        // $config->set('sitetheme_glyphicons', $supportedPacks);

        // // Attempt to save changes and provide feedback
        // if ($config->save())
        // {
        //     // Clear the cache to reflect the latest changes
        //     e107::getCache()->clear('core_prefs');

        //     // Inform the user about the successful update
        //     e107::getMessage()->addSuccess('Core preferences updated successfully.');
        // }
        
       
    }


    public function updateSiteGlyphs() {


    }

 
}



class glyphs_form_ui extends e_admin_form_ui
{
    public function packs($curVal, $mode)
    {

        switch ($mode)
        {
            case 'read': // Edit Page
                $text = "Are you cheating?";
                return $text;
                break;

            case 'write': // Edit Page
                $value = $curVal;
                $members_pages = self::getGlyphsPacks();

                $text = "<div class='e-container'>";
                $text .= "<table class='table table-striped table-bordered' style='margin-bottom:40px'>
					<colgroup>
						<col style='min-width:220px' />
						<col style='width:45%' />
						<col style='width:45%' />
					</colgroup>";

                $text .= "<tr><th>Available Pack</th><th>Supported</th><th> </th></tr>";

                foreach ($members_pages as $page => $val)
                {

                    $field = array('type' => 'boolean', 'writeParms' =>  []);

                    $actual_value = isset($value[$page]) ? $value[$page] : '';

                    $text .= "<tr><td><b>" . $page . "</b><br>(" . $val . ")" . ": </td><td>";
                    $text .= $this->renderElement('packs[' . $page . ']', $actual_value, $field);

                    $text .= "</td><td>";
                    $text .= "</td></tr>";
                }
                $text .= "</table>";
                return $text;
                break;
        }
    }


    static function getGlyphsPacks()
    {

        $array = [
            'ionicons'  => 'Ionicons, v2.0.1',
            'pixeden'  => 'Stroke 7 Icon Font Set',
            'lineicons' => 'Line Icons',
            'bootstrapicons'  => 'Bootstrap Icons v1.11.3',

        ];

        return $array;
    }
}


new glyphs_adminArea();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN . "footer.php");
exit;
