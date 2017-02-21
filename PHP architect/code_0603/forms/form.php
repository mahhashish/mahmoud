<?php

function render_pre_form(&$form)
{
  echo '<table>';
}

function render_post_form(&$form)
{
  echo '</table>';
}

function render_pre_element_text(&$form, &$element)
{
  if ($element->type !== 'submit')
    echo '<tr><td valign=top>';
}

function render_post_element_text(&$form, &$element)
{
  if ($element->type !== 'submit')
    echo '</td>';
}

function render_pre_element(&$form, &$element)
{
  if ($element->type === 'submit')
    echo '<tr><td colspan=2 align=center valign=top>';
  else 
    echo '<td>';
}

function render_post_element(&$form, &$element)
{
  echo '</td></tr>';
}

function render_pre_error(&$form, &$element)
{
  echo '<br><font color=red><b>';
}

function render_post_error(&$form, &$element)
{
  echo '</b></font>';
}

class CForm
{
  var $elements;
  var $name;
  var $submitted;
  var $action;
  var $method;
  var $error;
  
  function CForm($action, $method='post', $name='form')
  {
    $this->elements = array();
    $this->name = $name;
    $this->submitted = isset ($_REQUEST["____{$this->name}_submitted"]);
    $this->action = $action;
    $this->method = $method;
    $this->error = false;
  }
  
  function AddElement (&$element)
  {
    if (isset ($this->elements[$element->name]))
    {
      user_error ("Duplicate element {$element->name} added to form {$this->name}");
      return false;
    }
    
    $this->elements[$element->name] = $element;
  }
  
  function Load()
  {
    $keys = array_keys ($this->elements);
    foreach ($keys as $key)
      $this->elements[$key]->Load();
      
    if ($this->submitted)
      $this->Validate();
  }
  
  function Validate()
  {
    $keys = array_keys ($this->elements);
    foreach ($keys as $key)
    {
      if (!$this->elements[$key]->Validate())
        $this->error = true;
    }
  }
  
  function GetElementValue ($name)
  {
    if ($this->submitted)
      return (!strcasecmp ($this->method, 'post') ? $_POST[$name] : $_GET[$name]);
    else 
      return null;
  }
  
  function Render()
  {
    render_pre_form ($this);
    echo "<form name='{$this->name}' method='{$this->method}' action='{$this->action}'>";
    echo "<input type='hidden' name='____{$this->name}_submitted' value='1'>";
    foreach ($this->elements as $element)
      $element->Render();
    echo "</form>";
    render_post_form ($this);
  }
}

class CElement
{
  var $name;
  var $formname;
  var $value;
  var $form;
  var $value;
  var $defaultvalue;
  var $text;
  var $type;
  var $error;
  var $required;
  
  function CElement ($name, $text, $formname = 'null')
  {
    $this->name = $name;
    $this->formname = ($formname ? $formname : $name);
    $this->text = $text;
    $this->defaultvalue = null;
  }
  
  function Render()     // abstract
  {
    user_error ("Abstract function Render() called for element {$this->name}");
  }
  
  function RenderError()
  {
    if ($this->error)
    {
      render_pre_error ($this->form, $this);
      echo $this->error;
      render_post_error ($this->form, $this);
    }
  }
  
  function Load()
  {
    $this->value = trim ($this->form->GetElementValue ($this->formname));
  }
  
  function Validate()
  {    
    if (($this->required) && (!strcmp ('', $this->value)))
    {
      $this->error .= 'This value must be specified<br>';
      return false;
    }
    else 
      return true;
  }
  
  function SetValue($value)
  {
    $this->value = $value;
  }
  
  function SetForm (&$form)
  {
    $this->form = $form;
  }
}

class CSubmitElement extends CElement
{
  function CSubmitElement ($name, $text, &$form, $formname = null, $value = null)
  {
   parent::CElement($name, $text, $formname);

   if ($value)
     parent::SetValue ($value);
     
   $this->SetForm($form);
   $this->type = 'submit';
  }
  
  function Render()
  {
    render_pre_element ($this->form, $this);
    echo "<input type='submit' name='{$this->formname}' value='{$this->value}>";
    render_post_element ($this->form, $this);
    parent::RenderError();
  }
}

class CTextElement extends CElement
{
  var $size;
  var $maxlength;
  
  function CTextElement ($name, $text, &$form, $formname = null, $size = 15, $maxlength = 15, $defaultvalue = null)
  {
    parent::CElement($name, $text, $formname);
    $this->size = $size;
    $this->maxlength = $maxlength;
    $this->defaultvalue = $defaultvalue;
    $this->SetForm($form);
    $this->type = 'text';
  }  
  
  function Load()
  {
    parent::Load();
    
    if (!$this->value && !$this->submitted)
      $this->value = $this->defaultvalue;
  }
  
  function Render()
  {
    render_pre_element_text($this->form, $this);
    echo $this->text;
    render_post_element_text($this->form, $this);
    render_pre_element ($this->form, $this);
    echo "<input type='text' name='{$this->formname}' value='{$this->value}' size='{$this->size}' maxlength='{$this->maxlength}'>";
    parent::RenderError();
    render_post_element ($this->form, $this);
  }
}

class CPasswordElement extends CTextElement
{
  function CPasswordElement($name, $text, &$form, $formname = null, $size = 15, $maxlength = 15, $defaultvalue = null)
  {
    parent::CTextElement($name, $text, $form, $formname, $size, $maxlength, $defaultvalue);
    $this->type = 'password';
  }
  
  function Render()
  {
    render_pre_element_text($this->form, $this);
    echo $this->text;
    render_post_element_text($this->form, $this);
    render_pre_element ($this->form, $this);
    echo "<input type='password' name='{$this->formname}' size='{$this->size}' maxlength='{$this->maxlength}'>";
    parent::RenderError();
    render_post_element ($this->form, $this);
  }
}

class CDropdownElement extends CElement
{
  var $items;
  var $keyname;
  var $valuename;
  
  function CDropdownElement ($name, $text, $form, $items, $keyname = 0, $valuename = 1, $formname = null, $defaultvalue = null)
  {
    parent::CElement($name, $text, $formname);
    $this->SetForm($form);
    $this->defaultvalue = $defaultvalue;
    $this->items = $items;
    $this->keyname = $keyname;
    $this->valuename = $valuename;
  }
  
  function Load()
  {
    parent::Load();
    
    $success = false;
    
    foreach ($this->items as $item)
      if ($item[$valuename] === $this->value)
      {
        $success = true;
        break;
      }
    
    if (!$success && !$this->submitted)
      $this->value = $this->defaultvalue;
  }
  
  function Render()
  {
    render_pre_element_text($this->form, $this);
    echo $this->text;
    render_post_element_text($this->form, $this);
    render_pre_element($this->form, $this);
    echo "<select name='{$this->name}'>";
    foreach ($this->items as $item)
      echo "<option value={$item[$this->valuename]}>{$item[$this->keyname]}";
    echo "</select>";
    render_post_element($this->form, $this);
  }
}

?>