<?php
require("SimpelNLParser.php");

header('Content-type: application/json');

if (isset($_GET['username']) && isset($_GET['password']))
{
  $parser = new SimpelNLParser();
  $parser->doLogin($_GET['username'], $_GET['password']);
  $usage = $parser->getUsage();
  $parser->doLogout();

  print json_encode($usage);
}
else {
  print json_encode(array('error' => 'username or password missing'));
}