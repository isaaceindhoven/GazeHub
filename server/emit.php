<?php

require("Gaze.php");

$gaze = new Gaze();
$gaze->emit("ProductCreated", ["id" => 1, "name" => "Shirt"]);

return "Done";