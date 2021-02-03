<?php

return [
	"csv" => [
		"folder" => './logs',
		"header" => '"date";"message";"class";"function"',
		"pattern_file" => "/{TYPE}/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}.csv",
		"pattern_row" => '"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"',
	],
	"simple" => [
		"folder" => './logs',
		"pattern_file" => "/{TYPE}/{YEAR}-{MONTH}-{DAY}.log",
		"pattern_row" => "{DATE} {MESSAGE}",
	],
];
