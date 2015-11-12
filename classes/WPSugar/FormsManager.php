<?php

namespace WPSugar;

class FormsManager {
    private $fields;

    function __construct($fields = array()) {
        $this->fields = $fields;
    }

    public function register() {
        add_action('wp_ajax_' . $this->fields['name'], array($this, 'registerFormSaveAction'));
        add_action('wp_ajax_nopriv_' . $this->fields['name'], array($this, 'registerFormSaveAction'));
        add_action('admin_menu', array($this, 'registerAdminMenu'));
    }
    
    public function registerFormSaveAction() {
        global $wpdb;

        date_default_timezone_set('Europe/Moscow');

        $res = 0;
        $formFields = $this->getFormFields();

        $params = array();
        $values = array();
        $keys = array();
        foreach($formFields as &$item) {
            $value = (isset($_REQUEST[$item['name']])) ? $_REQUEST[$item['name']] : '';
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $value = strip_tags($value);
            $item['value'] = $value;
            $keys[] = $item['name'];
            $params[] = "'%s'";
            $values[] = $value;
        }

        $errors = $this->validate($formFields);
        if ($errors) {
            \http_response_code(400);
            echo json_encode($errors);
            exit;
        }
        try {
            $wpdb->query( 
                $wpdb->prepare( 
                    "INSERT INTO " . $wpdb->prefix . $this->fields['name'] .
                    "(" . implode(", ", $keys) . ")
                    VALUES (" . implode(', ', $params) . ")",
                    $values
                )
            );
            $res = $wpdb->insert_id;

            if (isset($this->fields['mail_from']) && isset($this->fields['mail_to']) 
                    && isset($this->fields['mail_subject']) && isset($this->fields['mail_template'])) {

                $from = $this->fields['mail_from'];
                $headers = '';
                $headers .= "From: $from\n";
                $headers .= "Reply-to: $from\n";
                $headers .= "Return-Path: $from\n";
                $headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
                $headers .= "MIME-Version: 1.0\n";
                $headers .= "Content-type: text/html; charset=utf-8\n";
                $headers .= "Date: " . date('r', time()) . "\n";
                
                $params = array();
                foreach ($formFields as $field) {
                    $params[$field['name']] = $field['value'];
                }
                $params['admin_list'] = 'http://' . $_SERVER['HTTP_HOST'] . '/wp-admin/admin.php?page=' . $this->fields['name'];
                $body = stripslashes(View::render($this->fields['mail_template'], $params, false));

                mail($this->fields['mail_to'], $this->fields['mail_subject'], $body, $headers);
            }            
        } catch ( Exception $e ) {
            $res = false;
        }

        if (!$res) {
            http_response_code(400);
            exit(Localization::getMessage('error_request'));
        } else {
            exit(Localization::getMessage('success_request'));
        }
    }

    public function registerAdminMenu() {
        add_menu_page(
            $this->fields['admin_title'],
            $this->fields['menu_item'],
            'administrator',
            $this->fields['name'],
            array($this, 'renderAdminPage')
        );
    }

    public function renderAdminPage() {
        global $wpdb;

        $sql = "SELECT * FROM " . $wpdb->prefix . $this->fields['name'];
        $results = $wpdb->get_results($sql, ARRAY_A);
        View::render('wpsugar-form-admin', array('results' => $results, 'form' => $this->fields, 'fields' => $this->getFormFields()), true);
    }
    
    private function getFormFields() {
        if (isset($this->fields['fields']) && $this->fields['fields']) {
            return $this->fields['fields'];
        }

        if (isset($this->fields['fieldsets']) && $this->fields['fieldsets']) {
            $fields = array();
            foreach ($this->fields['fieldsets'] as $fieldset) {
                if (isset($fieldset['fields']) && $fieldset['fields']) {
                    $fields = array_merge($fields, $fieldset['fields']);
                }
            }
            return $fields;
        }
    }
    
    private function validate($fields) {
        $errors = array();
        $formats = array(
            'PHONE' => array("/^\+7 \([0-9]{3}\) [0-9]{3} [0-9]{2} [0-9]{2}$/"),
            'EMAIL' => array("/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/")
        );
        foreach ($fields as $field) {
            if (isset($field['required']) && $field['required'] && (!isset($field['value']) || !$field['value'])) {
                $errors[$field['name']] = sprintf(
                    Localization::getMessage('field_is_required'),
                    Localization::getMessage($field['label'])
                );
            }
            if (isset($field['format']) && $field['format'] && $field['value'] && isset($formats[$field['format']])) {
                $valid = false;
                foreach ($formats[$field['format']] as $format) {
                    if (preg_match($format, $field['value'])) {
                        $valid = true;
                    }                    
                }
                if (!$valid) {
                    $errors[$field['name']] = sprintf(
                        Localization::getMessage('field_is_invalid'),
                        Localization::getMessage($field['label'])
                    );
                }
            }
            if (isset($field['maxlength']) && $field['maxlength'] && $field['value']) {
                if (mb_strlen($field['value']) > $field['maxlength']) {
                    $errors[$field['name']] = sprintf(
                        Localization::getMessage('field_is_too_long'),
                        Localization::getMessage($field['label']),
                        $field['maxlength']
                    );
                }
            }
        }
        return $errors;
    }
}