<?php

function get_battle_status($status_no)
{
	$status = "";

	switch ($status_no) {
		case 0:
			$status = 'Awaiting Player 2 Acceptance';
			break;
		case 1:
			$status = 'Awaiting Player 1 Acceptance';
			break;
		case 2:
			$status = 'Battle Live';
			break;
		case 3:
			$status = 'Battle Finished';
			break;
		case 4:
			$status = 'Battle Cancelled';
			break;
	}

	return $status;
}

function search_array(&$item, $item_key, $key_to_match) {
	$item = isset($item[$key_to_match]) ? $item[$key_to_match] : 0;
}