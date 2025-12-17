<?php
/* Copyright ...
 * Licence GPL v3+
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Module HelloAssoVars
 * Adds email substitution variables for payment URL (usable in email templates).
 */
class modHelloAssoVars extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // Unique id (pick a free one in your instance; 62000+ often used for externals)
        $this->numero = 62080;

        $this->rights_class = 'helloassovars';
        $this->family = "other";
        $this->module_position = 500;

        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Adds substitution variable __HELLOASSO_PAYMENT_URL__ for email templates.";
        $this->version = '1.0.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

        $this->picto = 'bill';

        // Parts
        $this->module_parts = array(
            'substitutions' => 1,
        );

        // No menus, no rights needed
        $this->rights = array();
        $this->menu = array();

        // Dictionaries / cron / etc none
    }

    public function init($options = '')
    {
        // No DB changes
        return $this->_init(array(), $options);
    }

    public function remove($options = '')
    {
        return $this->_remove(array(), $options);
    }
}