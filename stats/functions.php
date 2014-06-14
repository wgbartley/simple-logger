<?
function stats($oa) {
	// Uncleaned Averaged
	$oavg = array_sum($oa) / count($oa);

	// Get standard deviation uncleaned
	$t_array = array();
	foreach ($oa as $val)
		array_push($t_array, abs($oavg-$val));

	$ostd = array_sum($t_array) / count($t_array);

	// Remove anything outside of 2 standard deviations
	$na = array();
	foreach ($oa as $val)
		if (abs($val-$oavg) <= 2*$ostd)
			array_push($na, $val);

	// Cleaned average
	$navg = array_sum($na) / count($na);

	// Get standard deviation cleaned
	$t_array = array();
	foreach ($na as $val)
		array_push($t_array, abs($navg-$val));

	$nstd = array_sum($t_array) / count($t_array);

	// Return
	return array(
		'OriginalAvg' => $oavg,
		'OriginalStD' => $ostd,
		'CleanedAvg' => $navg,
		'CleanedStD' => $nstd
	);
}

?>
