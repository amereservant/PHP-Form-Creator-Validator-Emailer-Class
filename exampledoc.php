<?php
require './form.class.php';
// Give the form a name.  This is used to identify which form is submitted in emails.
$form = new emailForm('iServe Signup');

/* Create form fields
 * The field parameters are in this order:
 * type, name, id(optional), required, maxlength, size(will be cols for textarea), rows(textarea only),
 * datatype(type of data the field should contain, see notes in form.class.php for types), value(optional)
 */
$form->addField('text', 'name', 'name', true, 50, 30);
$form->addField('text', 'address', 'address', false, 50, 30, '', 'alphanumeric');
$form->addField('text', 'city', 'city', false, 80, 30, '', 'letters');
$form->addField('text', 'state', 'state', false, 30, 30, '', 'letters');
$form->addField('text', 'zip', 'zip', false, 5, 30, '', 'numbers');
$form->addField('text', 'email', 'email', true, 75, 30, '', 'email');
$form->addField('text', 'phone', 'phone', false, 12, 30, '', 'phone');
$form->addField('text', 'cell', 'cell', false, 12, 30, '', 'phone');
$form->addField('text', 'best', 'best', false, 30, 30, '');
$form->addField('textarea', 'comments', '', false, '', 30, 6, '');
$form->addField('submit', 'submit', '', '', '', '', '', '', 'Submit');

// MUST be called after adding fields so it can be validated accordingly.
$res = false;
if(isset($_POST) && !empty($_POST)) {
    $res = $form->submitForm($_POST);
}
// Display result message
if($res) {
    echo 'Thank you for contacting us. We will be in touch with you very soon.';
}
else
{ 
    if(!$res && count($form->errors) > 0) {
        foreach($form->errors as $error) {
            echo '<div style="font-weight:bold;color:red">' . nl2br($error) . '</div>';
        }
    }
}
?>
<form name="contactform" method="post" action="">
<table width="450px">
</tr>
<tr>
  <td valign="top" class="formTitle">&nbsp;</td>
  <td valign="top" class="formHeader">Sign Up</td>
</tr>
<tr>
 <td valign="top" class="formTitle">
  <label for="name">Name</label></td>
 <td valign="top" class="formForms">
  <?php $form->getField('name'); ?> </td>
</tr>

<tr>
 <td valign="top" class="formTitle">
  <label for="address">Address</label></td>
 <td valign="top" class="formForms">
  <?php $form->getField('address'); ?> </td>
</tr>
<tr>
 <td valign="top" class="formTitle">
  <label for="city">City</label></td>
 <td valign="top" class="formForms">
  <?php $form->getField('city'); ?> </td>
</tr>
<tr>
 <td valign="top" class="formTitle">
  <label for="state">State</label></td>
 <td valign="top" class="formForms">
  <?php $form->getField('state'); ?> </td>
</tr>
<tr>
  <td valign="top" class="formTitle">Zip Code</td>
  <td valign="top" class="formForms"><?php $form->getField('zip'); ?></td>
</tr>
<tr>
  <td valign="top" class="formTitle">e-Mail</td>
  <td valign="top" class="formForms"><?php $form->getField('email'); ?></td>
</tr>
<tr>
  <td valign="top" class="formTitle">Phone</td>
  <td valign="top" class="formForms"><?php $form->getField('phone'); ?></td>
</tr>
<tr>
  <td valign="top" class="formTitle">Cell Phone</td>
  <td valign="top" class="formForms"><?php $form->getField('cell'); ?></td>
</tr>
<tr>
  <td valign="top" class="formTitle">Best Time and Way to Reach You</td>
  <td valign="top" class="formForms"><?php $form->getField('best'); ?></td>
</tr>
<tr>
 <td valign="top" class="formTitle">
  <label for="comments">Where and When You Can Serve</label></td>
 <td valign="top" class="formForms">
  <?php $form->getField('comments'); ?> </td>
</tr>
<tr>
 <td class="formTitle" style="text-align:center">&nbsp;</td>
 <td class="formForms" style="text-align:center"><div align="left">
   <?php $form->getField('submit'); ?>
 </div></td>
</tr>
</table>
</form>
