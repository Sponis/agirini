<?php

defined('_JEXEC') or die;

class JFormFieldProonly extends JFormField {
    
    public $type = 'ProOnly';

    protected function getInput() {
        return '<fieldset style="padding: 5px 5px 0 0"><em>Only available in Pro Version!</em></fieldset>';
    }

}
?>
