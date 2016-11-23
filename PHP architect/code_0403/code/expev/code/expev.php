<?php

$variables = '';

$precedence = array
(
  '='   =>    0,
  '+'   =>    10,
  '-'   =>    10,
  '*'   =>    20,
  '/'   =>    20,
);

$command = '\;';
$identifier = '([a-z|A-Z|\_][a-z|A-Z|0-9|\_]*)';
$integer = '([0-9]+)';
$float = '([0-9]*\.[0-9]+)';
$operator = '(\+|\-|\/|\*|\=)';
$whitespace = ':space:|\n|\i';
$open_bracket = '\(';
$closed_bracket = '\)';
$echo = "{$whitespace}echo{$whitespace}";

define ("COMMAND", 1);
define ("IDENTIFIER", 2);
define ("INTEGER", 3);
define ("FLOAT", 4);
define ("OPERATOR", 5);
define ("WHITESPACE", 6);
define ("AECHO", 7);
define ("OPEN_BRACKET", 8);
define ("CLOSED_BRACKET", 9);

$findspec = "/$echo|$identifier|$float|$integer|$operator|($whitespace)|$command|$open_bracket|$closed_bracket/";
$elements = array
(
  AECHO   =>  $echo,
  COMMAND     =>  $command,
  IDENTIFIER  =>  $identifier,
  INTEGER     =>  $integer,
  FLOAT       =>  $float,
  OPERATOR    =>  $operator,
  WHITESPACE  =>  $whitespace,
  OPEN_BRACKET=>  $open_bracket,
  CLOSED_BRACKET=>$closed_bracket
);

function get_token_name ($token)
{
  switch ($token)
  {
    case  COMMAND :
      echo "COMMAND";
      break;
      
    case  IDENTIFIER:
      echo "IDENTIFIER";
      break;
      
    case  INTEGER:
      echo "INTEGER";
      break;
      
    case  FLOAT:
      echo "FLOAT";
      break;
      
    case  OPERATOR:
      echo "OPERATOR";
      break;
      
    case  WHITESPACE:
      echo "WHITESPACE";
      break;
      
    case  AECHO:
      echo "ECHO";
      break;
      
    case  OPEN_BRACKET:
      echo "OPEN BRACKET";
      break;
      
    case  CLOSED_BRACKET:
      echo "CLOSED BRACKET";
      break;

    default:
      echo "UNKNOWN";
  }
}

function dump_tokens ($tokens)
{
  foreach ($tokens as $tok)
  {
    echo get_token_name ($tok[0]) . " := $tok[1]\n";
  }
}

function tokenize ($expr)
{
  global $findspec;
  global $elements;
  
  preg_match_all ($findspec, $expr, $linedata);
  
  $result = array();
  
  foreach ($linedata[0] as $v)
  {
    if (strlen ($v))
    {
      $err = 1;
      foreach ($elements as $k => $d)
        if (preg_match ("/$d/", $v))
        {
          $result[] = array ($k, $v);
          $err = 0;
          break;
        }
      
      if ($err)
        die ("Syntax error: unknown token '$v'\n");
    }
  }
  
  return $result;
}

function evaluate ($tokens)
{
  global $precedence;

  $op_stack = array();
  $rpn_stack = array();

  $result = array();
  
  $bracket_level = 0;

  foreach ($tokens as $token)
  {
    switch ($token[0])
    {
      case  IDENTIFIER  :
      case  INTEGER     :
      case  FLOAT       :

        array_push ($rpn_stack, $token);
        break;
        
      case  OPERATOR    :
      
        while ((count ($op_stack)) && ($op_stack[count ($op_stack) - 1][0] !=
        OPEN_BRACKET) && ($precedence[$op_stack[count ($op_stack) - 1][1]] > 
        $precedence[$token[1]])) 
          array_push ($rpn_stack, array_pop ($op_stack));
          
        array_push ($op_stack, $token);
        
        break;
        
      case  OPEN_BRACKET :
      
        $bracket_level++;
        array_push ($op_stack, $token);
        
        break;
        
      case  CLOSED_BRACKET:
      
        while ((count ($op_stack)) && ($op_stack[count ($op_stack) - 1][0] != OPEN_BRACKET))
          array_push ($rpn_stack, array_pop ($op_stack));
          
        array_pop ($op_stack);
          
        $bracket_level--;
          
        break;
        
      case  AECHO       :
      
        $in_echo = $token;
        break;
        
      case  COMMAND     :
      
        if ($bracket_level)
          die ("Unexpected end of line\n");
           
        while (count ($op_stack))
          array_push ($rpn_stack, array_pop ($op_stack));
      
        if ($in_echo)
        {
          array_push ($rpn_stack, array (INTEGER, 0));
          array_push ($rpn_stack, $in_echo);
          $in_echo = false;
        }

        $result[] = $rpn_stack;
          
        $op_stack = array();
        $rpn_stack = array();
      
        break;
    }    
  }
  
  return $result;
}

function get_value ($token)
{
  global $variables;  

  switch ($token[0])
  {
    case  IDENTIFIER  :
    
      return get_value ($variables[$token[1]]);
      
    default:
    
      return $token[1];
  }
}

function execute (&$tokens)
{
  global $variables;  
  
  $operator = array_pop ($tokens);

  if ($tokens[count ($tokens) - 1][0] == OPERATOR)
    $right = execute ($tokens);
  else
    $right = array_pop ($tokens);
    
  if ($tokens[count ($tokens) - 1][0] == OPERATOR)
    $left = execute ($tokens);
  else
    $left = array_pop ($tokens);
   
  switch ($operator[0])
  {
    case  OPERATOR  :
  
      echo "OPERATION: $left[1] $operator[1] $right[1]\n";
  
      switch (trim ($operator[1]))
      {
        case  '='   :
        
          if ($left[0] != IDENTIFIER)
          {
            die ("Expecting identifier\n");
          }
          
          $variables[$left[1]] = $right;
          
          break;
        
        case  '+'   :
          
          return array (FLOAT, get_value ($left) + get_value ($right));      
          break;
        
        case  '-'   :
          
          return array (FLOAT, get_value ($left) - get_value ($right));      
          break;
        
        case  '/'   :
          
          return array (FLOAT, get_value ($left) / get_value ($right));      
          break;
        
        case  '*'   :

          return array (FLOAT, get_value ($left) * get_value ($right));      
          break;
      }
      
      break;
      
    case  AECHO  :
    
      echo get_value ($left) . "\n";
      break;
      
    default :
    
      die ("Execution error\n");
  }
}

$script = <<<EOT

a = 10;
b = 100;
c = 2;

abc = (a - c) * b;

echo (abc);

EOT;

$data = evaluate (tokenize ($script));

foreach ($data as $line)
  execute ($line);

?>
