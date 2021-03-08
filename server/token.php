<?php

require("Gaze.php");

$gaze = new Gaze();
echo json_encode(["token" => $gaze->generateClientToken(["admin"]) ]);