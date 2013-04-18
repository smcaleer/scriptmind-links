<?php
/**
# ######################################################################
# Project:     ScriptMind::Plugins: Version 0.0.1
# **********************************************************************
# Copyright (C) 2013 Bruce Clement. (http://www.clement.co.nz/)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public License
# as published by the Free Software Foundation; either version 3
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# ScriptMind::Links Forum
#
# @link           http://www.scriptmind.org/
# @copyright      2013 Bruce Clement. (http://www.clement.co.nz/)
# @license http://URL LGPLv3 or later
# @projectManager Bruce Clement
# @package        ScriptMind::Plugins
# ######################################################################
*/

/**
 # A convenient collection of values
 # provided by the package being extended
 #
 */

class PluginAnchor {
    /** @var ADOConnection Open database connection used to access persistant storage */
    public $db;
    /** @var string When plugins are part of a larger database this help with naming conventions */
    public $tablePrefix;
}

/**
 * Parameters passed to filters and returned to the calling process.
 * Filters validate or transform supplied parameters
 */
class PluginFilterParameters {
    /** @var array calling routines arguments, possibly altered by previous filters */
    public $callersArgs;
    /** @var bool A filter has changed a supplied parameter. Useful as a hint to the caller */
    public $changedArg = false;
    /** @var bool An error has occurred. */
    public $error = false;
    /** @var string the text of found error(s) */
    public $errorMessage = "";
    /** @var bool Stop running other filters. May be set by a
     * filter encountering an error or providing a definitive answer */
    public $stop = false;
    public function __construct( $callersArgs) {
        $this->callersArgs = $callersArgs;
    }
}

/**
 # Base class of plugins
 # provides skeletons for methods that derived classes will override
 #
 */
abstract class Plugin {
    /**
     * Loaded plugins that are registered to provide tasks
     */
    public static $providers=array();

    /**
     * Loaded plugins that are registered to provide filters
     */
    public static $filters=array();

    /**
     * Where plugins live. Defaults to our directory.
     * Each plugin will be in its own subdirectory matching the class name
     * with the main plugin file matching the class name:
     * i.e. plugin className will be in $pluginDir/className/className.plugin
     */
    public static $pluginDir = __DIR__;

    /**
     * Value of db field named by id_fieldName()
     * @var integer
     */
    public $ID;
    /**
     * a convenient class that contains pointers to the major objects of the
     * current query
     * @var PluginAnchor
     */
    public $anchor; // shared to the anchor table
    /**
     * Is this plugin currently active
     * The default is not
     * @var bool
     */
    public $Active;
    /**
     * Serialised & compressed version of this object
     * Only valid immediately before saving
     * @var string
     */
    public $Object;
    // public function ok_to_save() { return TRUE; }
    /**
     * Returns the plugin's name.
     * Provided as a usability aid.
     * By default returns the classname.
     */
    public function name( ) {
        return get_class($this);
    }
    /**
     * Can multiple copies of this plugin exist?
     * Used by configuration page
     * By default returns false.
     */
    public function AllowMultiple( ) {
        return false;
    }
    /**
     * Called before saving to the database to perform any required pre-save
     * processing
     */
    /**
     * Provides a description of the plugin for the admin plugins page
     */
    abstract public function describe( );
    /**
     * Called before saving to the database to perform any required pre-save
     * processing
     */
    public function before_save( &$state ) {}
    /**
     * Called after saving to the database to perform any required post-save
     * processing
     */
    public function after_save( $state_before, $state_now ) {}
    /**
     * Called after loading from database to complete the
     * initialisation of the object
     */
    public function after_load( $id, $anchor ) {
        $this->ID = (int)$id;
        $this->anchor = $anchor;
    }
    /**
     * Register this plugin as providing a service
     *
     * Call with either provides( serviceName, callable ) or
     * provides( array( array( serviceName, callable ), ... ) )
     *
     * **** USUALLY DOES NOT NEED OVERRIDING ****
     *
     * @param string $service The name of the provided service
     * @param callable $callback The routine to be called
     */
    public function provides( $service, $callback = null ) {
        if( $this->Active) {
            if( is_null( $callback ) ) {
                foreach( $service as $task ) {
                    $taskname = $task[0];
                    if(array_key_exists($taskname, self::$providers)) {
                        self::$providers[ $taskname ][] = $task[1];
                    } else {
                        self::$providers[ $taskname ] = array( $task[1] );
                    }
                }
            } else {
                if(array_key_exists($service, self::$providers)) {
                    self::$providers[ $service ][] = $callback;
                } else {
                    self::$providers[ $service ] = array( $callback );
                }
            }
        }
    }
    /**
     * Register this plugin as providing a service
     *
     * Call with either provides( serviceName, callable ) or
     * provides( array( array( serviceName, callable ), ... ) )
     *
     * **** USUALLY DOES NOT NEED OVERRIDING ****
     *
     * @param string $service The name of the provided service
     * @param callable $callback The routine to be called
     */
    public function RegisterFilter( $service, $callback = null ) {
        if( $this->Active) {
            if( is_null( $callback ) ) {
                foreach( $service as $task ) {
                    $taskname = $task[0];
                    if(array_key_exists($taskname, self::$filters)) {
                        self::$filters[ $taskname ][] = $task[1];
                    } else {
                        self::$filters[ $taskname ] = array( $task[1] );
                    }
                }
            } else {
                if(array_key_exists($service, self::$filters)) {
                    self::$filters[ $service ][] = $callback;
                } else {
                    self::$filters[ $service ] = array( $callback );
                }
            }
        }
    }
    /**
     * Called after loading plugins from the database to insert their callbacks
     * into the control structures used to implement fetchValues
     *
     * Each plugin with callbacks should override this and call provides() to
     * load its callbacks before calling parent::register_callbacks()
     */
    public function register_callbacks() {}

    /**
     * Static function to call all registered providers of a task
     * @return array(string) Result
     */
    public static function fetchValues( $taskName ) {
        $answer = array();
        if(array_key_exists($taskName, self::$providers)) {
            // NB $args[0] == $taskName & hence the first parameteter
            // of called routines is their taskName. I left this
            // in place to allow one callback to handle multiple tasks.
            $args = func_get_args();
            foreach( self::$providers[ $taskName ] as $provider ) {
                $val= call_user_func_array( $provider, $args );
                if( !is_null($val) ) {
                    $answer[] = $val;
                }
            }
        }
        return $answer;
    }

    /*
     * Static function to call all registered providers of a named filter
     * @return PluginFilterParameters Result
     */
    public static function runFiltersInternal( $taskName, PluginFilterParameters $args ) {
        if(array_key_exists($taskName, self::$filters)) {
            // NB $args->callersArgs[0] == $taskName. I left this
            // in place to allow one callback to handle multiple tasks.
            foreach( self::$filters[ $taskName ] as $filter ) {
                call_user_func($filter, $args);
                if( $args->stop) break;
            }
        }
        return $args;
    }

    /*
     * Static function to call all registered providers of a named filter
     * @return PluginFilterParameters Result
     */
    public static function runFilters( $taskName ) {
        return self::runFiltersInternal($taskName, new PluginFilterParameters(func_get_args()) );
    }

    /**
    * Create the serialised data
    * Derrived classes that have private data that shouldn't be saved should process it:
    * <ul><li>save class data fields to temporary variables</li>
    * <li>clear class data fields (Unset or Assign to NULL)</li>
    * <li>call parent::set_serialised_data</li>
    * <li>restore  class data fields</li></ul>
    * It is (of course) also possible to use __sleep() & __wakeup()
    */
    public function set_serialised_data() {
        $id = $this->ID;
        $anchor = $this->anchor;
        unset($this->anchor);
        unset($this->ID);
        unset($this->Object);
        $this->Object = gzcompress(serialize( $this ), 9);
        $this->anchor = $anchor;
        $this->ID = $id;
    }
    /**
     * Get a list of options this plugin provides
     * Child classes should add their options to this array
     * @return array One line per option, each line is an array:
     *         'Name': option name,
     *         'Title': option title
     *         'Value': current value
     *         'Type': Bool, Integer, Url, Text, or Enumeration array
     *         'Description': Text
     */
    public function enumerate_options() {
        return array( array( 'Active', 'Active', (int)$this->Active, 'Bool', "Will this plugin run") );
    }
    /**
     * Set an option
     * Child classes should update their options from recognised options
     * and pass anything they don't understand to their parent classes
     */
    public function set_option( $name, $value ) {
        switch( $name ) {
            case 'Active' : $this->Active = (bool)$value;     break;
            default: $this->$name = $value;
        }
    }
    /**
     * Serialise the class
     */
    public function serialise() {
        $state_before='';
        $state_after='';
        $this->before_save($state_before);
        $this->set_serialised_data();
        $this->after_save($state_before, $state_after);
        return $this->Object;
    }
    static public function autoload( $classname ) {
        (@include self::$pluginDir."/$classname.plugin") ||
        (include self::$pluginDir."/$classname/$classname.plugin");
    }
    /**
     * Static method to reconstitute the class
     * @return Plugin
     */
    static public function unserialise( $classname, $data, $id, $anchor) {
        /** @var $object Plugin the reconstituted plugin */
        $object = unserialize(gzuncompress($data));
        $object->after_load( $id, $anchor );
        return $object;
    }
    /**
     * Static factory method create an instance of the class
     * @return Plugin
     */
    static public function create( $pluginName, $anchor = NULL) {
        /** @var Plugin the new Plugin */
        $object = new $pluginName( $anchor );
        return $object;
    }
    /**
     * Static factory method to load plugins
     */
    static public function load( PluginAnchor $anchor, $where, $register_callbacks ){
        $sql = "SELECT * FROM `{$anchor->tablePrefix}PLUGIN` where $where";
        $anchor->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $anchor->db->Execute($sql);
        // we load all plugins before registering callbacks in case of interactions
        $plugins = array();
        while (!$rs->EOF)
        {
            /** @var Plugin */
            $plugin = self::unserialise($rs->Fields('CLASS_NAME'),
                                        $rs->Fields('CLASS_DATA'),
                                        $rs->Fields('ID'),  $anchor);
            $plugins[] = $plugin;
           $rs->MoveNext();
        }
        if( $register_callbacks) {
            foreach( $plugins as $plugin ) {
                $plugin->register_callbacks();
            }
        }
        return $plugins;
    }
    /**
     * save plugin to database
     */
    public function save() {
        $data = array(
            'ADMIN_HOOKS' => 1,
            'NORMAL_HOOKS' => 1,
            'ACTIVE' => $this->Active,
            'CLASS_NAME' => get_class($this)
        );
        $db = $this->anchor->db;
        $table = $this->anchor->tablePrefix.'PLUGIN';
        if( $this->ID > 0 ) {
           $operation = 'UPDATE';
           $blobWhere = $where = 'ID = '.$this->ID;
        } else {
           $operation = 'INSERT';
           $where = false;
           $data['ID'] = $this->ID = $db->GenID( $table.'_SEQ');
           $blobWhere = 'ID = '.$this->ID;
        }
        if ($db->AutoExecute( $table, $data, $operation, $where)) {
            $val = $this->serialise();
            $db->UpdateBlob($table, 'CLASS_DATA', $val, $blobWhere);
        } else {
            $err = $db->ErrorMsg();
        }
    }
    /**
     * Remove plugin from database
     */
    public function delete() {
        if( $this->ID > 0) {
            $db = $this->anchor->db;
            $table = $this->anchor->tablePrefix.'PLUGIN';
            $sql = "DELETE FROM $table WHERE ID=$this->ID";
            $db->Execute($sql);
        }
    }
    /**
     * Returns an array of arrays of plugins:
     * [0] = array of active plugins
     * [1] = array of loaded but inactive plugins
     * [2] = array of unloaded but available plugins
     * [3] = array of broken plugins (Name + error message)
     * [0..2] contain loaded Plugin objects
     * @return array(array(Plugin))
     */
    public static function allAvailablePlugins(PluginAnchor $anchor) {
        $activePlugins = array();
        $inactivePlugins = array();
        $installedPlugins=array();
        $pluginNames = array();
        $unusedPlugins = array();
        $failedPlugins = array();
        $plugins = Plugin::load( $anchor, "1=1", false );
        foreach( $plugins as $plugin) {
            $id=$plugin->ID;
            if( $plugin->Active) {
                $activePlugins[ $id ] = $plugin;
            } else{
                $inactivePlugins[ $id ] = $plugin;
            }
            $installedPlugins[ $id ] = $plugin;
            $pluginName=get_class($plugin);
            if( $plugin->AllowMultiple() && !array_key_exists($pluginName, $pluginNames)) {
                $unusedPlugins[] = Plugin::create($pluginName, $anchor);
            }
            $pluginNames[$pluginName] = $plugin;
        }
        foreach( array(Plugin::$pluginDir."/*.plugin", Plugin::$pluginDir."/*/*.plugin") as $dir ) {
            $ans = glob( $dir );
            foreach ( $ans as $filename) {
                $filename=substr($filename, strlen(Plugin::$pluginDir)+1 );
                $pluginName = substr($filename, strpos($filename, '/'));
                $pluginName = substr( $filename, 0, strpos( $pluginName, '.plugin'));
                if(!array_key_exists($pluginName, $pluginNames)) {
                    try {
                        $unusedPlugins[] = Plugin::create($pluginName, $anchor);
                    }
                    catch (Exception $ex)
                    {
                        $failedPlugins[] = $pluginName . " - ". $ex->getMessage();
                    }
                }
            }
        }
        return array( $activePlugins, $inactivePlugins, $unusedPlugins, $failedPlugins, $installedPlugins);
    }

    /**
     * Initialise the plugin
     * Called when the plugin is activated, normal runs just unserialise
     * a copy saved in the database
     */
    public function __construct( $anchor = null) {
        $this->ID = 0;
        $this->Object = '';
        $this->anchor = is_null($anchor) ? new stdClass() : $anchor;
        $this->Active = false;
    }
}

spl_autoload_register('Plugin::autoload');
