<?php
/**
 * Form Email Class
 *
 * PHP 5.2 min (See checkInput() method and filter_var() for this requirement.)
 *
 * @author David Miles <david@amereservant.com>
 */
class emailForm {

    // Email recipient
    private $recipient;
    
    // Email from address
    private $emailFrom;
    
    // Email subject
    private $subject = "iServOthers";
    
    // Current Form Fields
    private $fields = array();
    
    // Form errors
    public $errors;
    
    // Form name - Used as both part of the email subject and the table name in the database
    private $formname;
    
    // Log File
    private $logfile;
    
    // Sqlite Database File
    private $sqliteFile;
    
    // Sqlite Error Log
    private $sqliteErrorLog;
    
    public function __construct($formname)
    {
        $this->recipient = RECIPIENT;
        $this->emailFrom = SENDER;
        $this->logfile = FORMLOG;
        $this->sqliteFile = SQLITEFILE;
        $this->sqliteErrorLog = SQLITELOG;
        $this->formname = str_replace(' ', '_', trim($formname));
        $this->subject .= " - $formname";
    }
    
    /**
     * Add Form Field
     *
     * This method is used to add form fields to the form and the values
     * are also used to generate the form field syntax as well as also validate
     * the user's input when the form is submitted.
     *
     * null/false parameters are optional.
     *
     * $datatype parameter allows you to specify if the field should be 'email', 'letters',
     * 'alphanumeric', 'numbers', 'phone', or 'any'. 
     * Emails will be validated and alpha-numeric will only match letters and numbers but
     * not any special characters.
     *
     * If using textarea as the type, the $size parameter will take the value of the
     * "cols" and $rows will be the "rows".  
     * Otherwise $rows isn't used in any other field type regardless if given a value or not.
     * 
     */
    public function addField($type, $name, $id=null, $required=null, $maxlength=null, 
        $size=null, $rows=null, $datatype=null, $value=null)
    {
        // Assign default values for empty parameters
        if(!$required) { $required = false; }
        if(!$datatype) { $datatype = 'any'; }
        
        if(empty($type) || empty($name)) {
            trigger_error("First two parameters in addField() method are required!", E_USER_ERROR);
            return false;
        }
        // Make sure field type is valid
        $valids = array("textarea", "text", "submit", "hidden");
        // Clean $type name
        $type = strtolower(trim($type));
        if(!in_array($type, $valids)) {
            trigger_error("You didn't specify a valid field type.", E_USER_ERROR);
            return false;
        }
        // Check the user hasn't already specified this field name
        if(isset($this->fields[$name])) {
            trigger_error("You have already specified that field name!", E_USER_ERROR);
            return false;
        }
        // Now append data to $fields array assuming the rest of the input is correct.
        $this->fields[$name]['type'] = $type;
        $this->fields[$name]['name'] = $name;
        $this->fields[$name]['id'] = $id;
        $this->fields[$name]['required'] = $required;
        $this->fields[$name]['maxlength'] = $maxlength;
        $this->fields[$name]['size'] = $size;
        $this->fields[$name]['rows'] = ($type == 'textarea' ? $rows:null);
        $this->fields[$name]['datatype'] = $datatype;
        $this->fields[$name]['value'] = $value;
        
        return true;
    }
    
    /**
     * Get Field By Name
     *
     * This function is used after the fields have been added with the addField()
     * method to retrieve the field's syntax.
     */
    public function getField($name) {
        if(!isset($this->fields[$name]))
        {
            echo '<h4>ERROR: Field ' . $name . ' hasn\'t been added yet!</h4>';
            exit();
        }
        // Create shorter variables
        foreach($this->fields[$name] as $key => $val):
            $$key = $val;
        endforeach;
        
        if($type == 'textarea')
        {
            $field = '<textarea ';
        }
        elseif($type == 'text')
        {
            $field = '<input type="text" ';
        }
        elseif($type == 'submit')
        {
            $field = '<input type="submit" ';
        }
        elseif($type == 'hidden')
        {
            $field = '<input type="hidden" ';
        }
        // Add Name   
        $field .= 'name="' . $name . '"';
        // Add ID if given
        $field .= ($id ? ' id="' . $id . '"':'');
        // Add MaxLength if given
        $field .= ($maxlength && $type != 'hidden' && $type != 'submit' ? ' maxlength="' . 
            $maxlength . '"':'');
        // Add Size if given ( only applies for text and textarea types. )
        // If $type is textarea and no $size given, it defaults to cols="30".
        $field .= ($type == 'textarea' || $type == 'text' && $size ? ($type == 'textarea' ?
            ' cols="' . ($size ? $size:'30') . '"':($type == 'text' ? ' size="' . $size . '"':'')):'');
        // Add rows for text area - all other types ignore this field.
        // Defaults to rows="6" if no rows are specified for a textarea.
        $field .= ($type == 'textarea' ? ' rows="' . ($rows ? $rows:'6') . '"':'');
        // Add Value if given, except for Textarea
        $field .= ($value && $type != 'textarea' ? ' value="' . $value . '"':'');
        // Now add closing tags.
        $field .= ($type != 'textarea' ? ' />':'>' . ($value ? $value:'') . '</textarea>');
        
        // Output field
        echo $field;
    }
    
   /**
    * Validate Form Input
    *
    * This function is used during the processing of the form data to validate
    * each added field and the value contained in that field against the set datatypes, 
    * lengths, and if they are required.
    */
    private function checkInput($post)
    {
        if(empty($this->fields) || empty($post)) {
            trigger_error("Required data for validating missing.", E_USER_WARNING);
            return false;
        }
        
        // Loop over data and validate it
        foreach($post as $key => $val):
            /**
             * Make sure only defined fields were submitted an nothing else.
             * If this error keeps happening, make SURE you created all input fields
             * using the addField() method and not manually.
             */
            if(!isset($this->fields[$key])) {
                $this->errors[] = "The form you submitted doesn't appear to be valid.\n" . 
                    "Please notify an administrator if this problem continues. [$key]\n";
                return false;
            }
            // Verify required fields have been filled in
            if($this->fields[$key]['required'] === true && empty($val)) {
                $this->errors[] = "The \"$key\" field is required and cannot be empty!\n";
                return false;
            }
            // Make sure value isn't longer than the field maxlength
            if($this->fields[$key]['maxlength'] < strlen(trim($val)) && !empty($this->fields[$key]['maxlength'])) {
                $this->errors[] = "Somehow you entered more data than permitted for the " . 
                    "\"$key\" field!\n";
                return false;
            }
            // Validate field Datatype
            // Current types are 'email', 'letters','alphanumeric', 'numbers', 'phone', or 'any'
            /**
             * Check Email Address
             * Takes a given email address and split it into the username and domain,
             * then verifies the domain name is valid.  See functions.php for a more
             * comprehensive email validation function if this isn't sufficient.
             */
            if($this->fields[$key]['datatype'] == 'email' && !empty($val))
            {
                list($userName, $mailDomain) = explode("@", $val);
                if (!checkdnsrr($mailDomain, "MX") || strlen($userName) < 2) {
                    $this->errors[] = "Sorry, but your email address $val couldn't be validated.\n";
                    return false;
                }
            }
            // Check for letters, spaces, and '.', '-', '_' only.
            if($this->fields[$key]['datatype'] == 'letters' && !empty($val))
            {
                if(!preg_match('/^([a-zA-Z\.-_ ]+)$/', $val)) {
                    $this->errors[] = "The '$key' field only allows letters!  Please remove anything else.";
                    return false;
                }
            }
            // Check for numbers, spaces, and '.', '-', '_' only.
            if($this->fields[$key]['datatype'] == 'numbers' && !empty($val))
            {
                if(!preg_match('/^([0-9\.-_ ]+)$/', $val)) {
                    $this->errors[] = "The '$key' field only allows numbers!  Please remove anything else.";
                    return false;
                }
            }
            // Check for letters, numbers, spaces, and '.', '-', '_' only.
            if($this->fields[$key]['datatype'] == 'alphanumeric' && !empty($val))
            {
                if(!preg_match('/^([#0-9a-zA-Z\.-_ ]+)$/', $val)) {
                    $this->errors[] = "The '$key' field only allows letters and numbers!  Please remove anything else.";
                    return false;
                }
            }
            // Check Phone Number - Allows either 123-456-7890 or 456-7890 format
            if($this->fields[$key]['datatype'] == 'phone' && 
                !preg_match('/^([0-9]{3}-)?([0-9]{3})-([0-9]{4})$/', $val)  && !empty($val)) 
            {
                $this->errors[] = "The number $val is an invalid phone number format.\n" . 
                    "Make sure you enter a number in this format: 501-555-1234\n";
                return false;
            }
            /**
             * Clean the input data
             *
             * This part uses php's Filter extension which must be installed and
             * requires PHP version 5.2.
             * This function removes and converts potentially harmful characters from
             * post data such as <>, etc.
             */
            if(!function_exists('filter_list') || !in_array('string', filter_list())) {
                trigger_error("A required function doesn't exist!  Please notify the administrator.", E_USER_ERROR);
                exit();
            }
            $this->fields[$key]['value'] = filter_var($val, FILTER_SANITIZE_STRING);
        endforeach;
        // Return true on success
        return true;
    }
    
   /**
    * Process Form
    *
    * This class is called to process the submitted form data.
    */
    public function submitForm($post)
    {
        if(empty($post)) {
            trigger_error("ERROR: A method was called with empty data.  Please notify Administrator",
                E_USER_ERROR);
            exit();
        }
        // Add values to fields in case an error occurs, the values are automatically populated.
        foreach($post as $key => $val):
            // Check if any additonal fields have also been submitted
            if(!isset($this->fields[$key])) {
                $this->errors[] = "The form you submitted doesn't appear to be valid.\n" . 
                    "Please notify an administrator if this problem continues.\n";
               $this->logErrors();
                return false;
            }
            $this->fields[$key]['value'] = $val;
        endforeach;
        if(!$this->checkInput($post)) {
            $this->logErrors();
            return false;
        }
        if(!$this->addToDatabase()) {
            echo 'Database Error.  See Log File.';
        }
        $this->sendEmail();
        return true;
    }
    
   /**
    * Log Errors
    *
    * This method is used to loop through the errors(if any) in the $errors property
    * and then write them to a log file specified in the $logfile property.
    */
    private function logErrors()
    {
        if(count($this->errors) < 1)
        {
            return;
        }
        $string = '';
        foreach($this->errors as $error):
            $string .= '[' . date('d-M-Y h:i:s') . '] ';
            $string .= $error;
        endforeach;
        file_put_contents($this->logfile, $string, FILE_APPEND);
    }
    
   /**
    * Add Message To Database
    *
    * This method adds the form data to a database.  You MUST create the database and
    * tables first, otherwise this method will fail.
    * By default, it uses a sqlite database, which is a file-based database and will
    * automatically create the file and tables if they don't exist.
    */
    private function addToDatabase()
    {
        if(count($this->fields) < 1) {
            return false;
        }
        $sql = "CREATE TABLE IF NOT EXISTS {$this->formname} (" . 
               "id INTEGER PRIMARY KEY NOT NULL," . 
               "date_time INTEGER NOT NULL,";
        foreach($this->fields as $key => $val):
            if($key == 'submit') { continue; }
            // Check data types.
            //'email', 'letters','alphanumeric', 'numbers', 'phone', or 'any'
            $datatype = ($val['datatype'] == 'email' || $val['datatype'] == 'letters' ||
                $val['datatype'] == 'any' && $val['type'] != 'textarea' ||
                $val['datatype'] == 'alphanumeric' || $val['datatype'] == 'numbers' ?
                'VARCHAR':($val['datatype'] == 'any' ? 'TEXT':'VARCHAR')); 
            // Add field length if VARCHAR type
            if($datatype == 'VARCHAR') { $datatype .= "({$val['maxlength']})"; }
            // Add to sql CREATE TABLE string.
            $sql .= "$key $datatype " . ($val['required'] ? "NOT NULL":"NULL") . ",";
        endforeach;
        $sql = rtrim($sql, ",") . ")";   
        //echo "<pre>$sql</pre><br />";
        $dbh = new PDO("sqlite:".$this->sqliteFile);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        
        // Try creating the table if it doesn't exist
        if($dbh) { $res = $dbh->exec($sql); }
        // Add any errors to a logfile.
        if($dbh == false || $res === false) {
            $errstring = '[' . date('d-M-Y h:i:s') . '] ' . implode(', ', $dbh->errorInfo()) . " LINE: " . 
                __LINE__ . " FILE: " . basename(__FILE__) . "\n"; 
            file_put_contents($this->sqliteErrorLog, $errstring, FILE_APPEND);
            return false;
        }
        // Form INSERT command to be executed.
        $sql = "INSERT INTO {$this->formname} ";
        $sqlfields = "(date_time";
        $sqlvalues = "('" . time() . "'";
        foreach($this->fields as $key => $val):
            if($key == 'submit') { continue; }
            $sqlfields .= ", {$key}";
            $sqlvalues .= ", '{$val['value']}'";
        endforeach;
        $sql .= $sqlfields . ") VALUES " . $sqlvalues . ")";
        
        if(!$dbh->exec($sql)) {
            $errstring = '[' . date('d-M-Y h:i:s') . '] ' . implode(', ', $dbh->errorInfo()) . "\n" . 
            file_put_contents($this->sqliteErrorLog, $errstring, FILE_APPEND);
            return false;
        }
        else
        {
            return true;
        }
    }
    
   /**
    * Email Form Submission
    *
    * This method is used to format the form data and send it to the defined recipient
    * in the $recipient property.
    */
    private function sendEmail()
    {
        if(count($this->fields) < 1) {
            return false;
        }
        $message = "New Message From " . $this->subject . " Form\r\n";
        $message .= "Message Submitted: " . date('l, M. d, Y g:iA T') . "\n\n";
        foreach($this->fields as $key => $val):
            if($key == 'submit') { continue; }
            $message .= strtoupper($key) . ":\t   \t" . $val['value'] . "\n";
        endforeach;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $this->emailFrom . "\r\n";
        @mail($this->recipient,$this->subject,$message,$headers);
    }
}
?>
