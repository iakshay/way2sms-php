<?php
function create_query_string($dom){
  $params = Array();
  //$dom = file_get_contents("dom.txt");

  $doc = new DOMDocument();
  // A corrupt HTML string
  libxml_use_internal_errors(true);
  $doc->loadHTML($dom);
  $form = $doc->getElementsByTagName('form')->item(0);
  //var_dump($form); 
  $input = $form->getElementsByTagName('input');

  //echo "\n Input \n";

  foreach($input as $node) {
        //var_dump($node->attributes);
        if($node->hasAttribute('name')){
          //echo $node->getAttribute('name'). " : ". $node->getAttribute('value') . "\n";
          $value = strlen($node->getAttribute('value')) ? $node->getAttribute('value') : 'Mobile';
          $params[$node->getAttribute('name')] = $value;
        }
  }

  //echo "\n Textarea \n";

  $textarea = $form->getElementsByTagName('textarea');
  foreach($textarea as $node) {
        if($node->hasAttribute('name')){
          //echo $node->getAttribute('name'). " : ". $node->nodeValue . "\n";
          $params[$node->getAttribute('name')] = $node->nodeValue;
        }
  }

  //echo "\n Select \n";
  $select = $form->getElementsByTagName('select');
  foreach($select as $node) {
        //var_dump($node->attributes);
        if($node->hasAttribute('name')){
          $options = $node->getElementsByTagName('option');
          foreach ($options as $option) {
             if($option->hasAttribute('selected')){
                  $value = $option->getAttribute('value');
             }
          }
          if(!$value){
              $value = $options->item(0)->getAttribute('value');
          }
        }
        //echo $node->getAttribute('name'). " : ". $value . "\n";
        $params[$node->getAttribute('name')] = $value;
  }

  //echo "\n Script tags \n";
  $script = $form->getElementsByTagName('script')->item(0);
  preg_match_all('/Attribute\("name", "([A-Za-z0-9.]+)"/si', $script->nodeValue, $names);
  preg_match_all('/Attribute\("value", "([A-Za-z0-9.]+)?"/si', $script->nodeValue, $values);
    for ($i=0; $i < sizeof($names[1]); $i++) { 
      //echo $names[1][$i]. " : ". $values[1][$i]. "\n";
      $params[$names[1][$i]] = $values[1][$i];
    }

  return $params;
};
