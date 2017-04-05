<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Black Box - Rion Brattig Correia</title>

	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<style>		
		table#system tr td.gru { background-color:#ee4035; }
		table#system tr td.pty { background-color:#e5c700; }
		table#system tr td.jfk { background-color:#816c8e; }
		table#system tr td.lax { background-color:#7bc043; }
		table#system tr td.las { background-color:#0392cf; }


		.label-lg { font-size: 30px; }
		
		table#system tr td { font-size:15px; padding:4px 0 0 0; margin:0; }
	</style>


</head>
<body>
	
	<div class="container">

	<h1>The Black Box</h1>
	
	<div class="alert alert-warning">
		<p>
			The box is a 20 by 20 matrix of cells. In the input box you can enter the number of cycles you want the <strong>system</strong> to run each time.<br/>
		</p>
		<p>
			Study the <strong>behavior</strong> of the system. Propose a <strong>model</strong> of what it does.
		</p>
	</div>

<!--
BLACK BOX CODE STARTS HERE
-->
<?php
$ncol = 20;
$nrow = 20;
$debug = False;
/*
if (isset($_GET['cycles'])) {
	$cycles = $_GET['cycles'];
} else {
	$cycles = 1;
}
if (isset($_GET['cycles'])) {
	$step = $_SESSION['step'];
} else {
	$step = 1;
}
*/

/*
// Reset Blackbox
*/
if ($_GET['reset'] == 1) {
	unset($_SESSION['blackbox']);
	unset($_SESSION['blackbox_saved']);
	unset($_SESSION['step']);
	unset($_SESSION['step_saved']);
}

/*
// If Blackbox is not set, Init Blackbox
*/
if (!isset($_SESSION['blackbox'])) {
	//
	$step = 1;
	$cycles = 0;
	//
	$state = array();
	// Initiate Array with Random Numbers
	for ($i=0; $i<$nrow; $i++) {	
		$state[$i] = array();
		for ($j=0; $j<$ncol; $j++) {
			if ($debug) {
				$state[$i][$j] = 1; # Initiate with Zero
			} else {
				$state[$i][$j] = rand(0,4); # Initiate with Random			
			}			
		
		}
	}
// If blackbox is set, but needs to be reverted
} else if (isset($_GET['revert'])) {
	$revert = True;
	$state = $_SESSION['blackbox_saved'];
	$state_saved = $_SESSION['blackbox'];
	$step = $_SESSION['step_saved'];
	$step_saved = $_SESSION['step'];
// 
} else if ($_GET['cycles']) {
	$state = $_SESSION['blackbox'];
	$state_saved = $state;
	$step = $_SESSION['step'];
	$step_saved = $step;
	//
	$cycles = $_GET['cycles'];
} else {
	// Nothing to perform. Just print the Table
	$state = $_SESSION['blackbox'];
	$state_saved = $_SESSION['blackbox_saved'];
	$step = $_SESSION['step'];
	$step_saved = $_SESSION['step_saved'];
}

/*
// The Number Of Cycles to Perform
*/
if (!$revert) {
	for ($cycle=0; $cycle<$cycles; $cycle++) {
		$step += 1;	
		/*
		// Majority Rule
		*/
		// x = from 0 to ($nrow)/2
		// y = from 0 to ($ncol)/2
		$x = rand(0, ($ncol-1) );
		$y = rand(0, ($nrow-1) );
		$noise = rand(0,99);
		//print $noise;
	
		$maj_count = array_fill(0,4,0);

		$xp1 = $x + 1;
		$xm1 = $x - 1;
		$yp1 = $y + 1;
		$ym1 = $y - 1;
		if($x==0) { $xm1 = $ncol-1; }
		if($x==$nrow-1) { $xp1 = 0; }
		if($y==0) { $ym1 = $nrow-1; }
		if($y==$ncol-1) { $yp1 = 0; }

		$maj_count[ $state[$y][$x] ]++;
		$maj_count[ $state[$ym1][$x] ]++;
		$maj_count[ $state[$ym1][$xp1] ]++;
		$maj_count[ $state[$y][$xp1] ]++;
		$maj_count[ $state[$yp1][$xp1] ]++;
		$maj_count[ $state[$yp1][$x] ]++;
		$maj_count[ $state[$yp1][$xm1] ]++;
		$maj_count[ $state[$y][$xm1] ]++;
		$maj_count[ $state[$ym1][$xm1] ]++;
		

		$maj = 0;
		$newstate = $state[$y][$x];
		
		// Makes the order of the array chance, thus giving more change to higher numbers too;
		if($step%2==0) {
			krsort($maj_count);
		}

		foreach ($maj_count as $key => $value) {
			if ($value > $maj){
				$maj = $value;
				$newstate = $key;
			}
		}
		
		// Adds random noise.	
		if ($noise<5) {
			$oldstate = $state[$y][$x];
			$newrandstate = rand(0,4);
			while (($newrandstate == $oldstate) or ($newrandstate == $newstate)) {
				$newrandstate = rand(0,4);
			}
			$state[$y][$x] = $newrandstate;
		} else {
			$state[$y][$x] = $newstate;
		}
		
		


	} //end cycles
} //end !revert
/*
// Save State to SESSION
*/
$_SESSION['blackbox'] = $state;
$_SESSION['blackbox_saved'] = $state_saved;
$_SESSION['step'] = $step;
$_SESSION['step_saved'] = $step_saved;

?>

<?php
/*
// Colors
*/
$c = array();
$c[0] = 'gru';
$c[1] = 'pty';
$c[2] = 'jfk';
$c[3] = 'lax';
$c[4] = 'las';
?>

<div class="container">
	<!-- Table -->
	<div class="col-md-7">
		<div class="panel panel-default">

<?php
/*
// Print Table
*/
print "\t\t\t<table id='system' class='table table table-bordered' width='620' height='620'>\n";
for ($i=0; $i<$nrow; $i++) {	
	print "\t\t\t\t<tr>\n";
	for ($j=0; $j<$ncol; $j++) {
		$classes = $c[$state[$i][$j]];

		// Quadrant Separating Lines
		if ($i == $nrow/2-1) {
			$classes .= ' rowline';
		}
		if ($j == $ncol/2-1) {
			$classes .= ' colline';
		}
		
		print "\t\t\t\t\t<td class='".$classes." text-center' width='31' height='31'>";
		
		if (($i==$y) and ($j==$x) and (isset($x)) and (isset($y))) {
			print '<mark>';
		}
		print $state[$i][$j];

		if (($i==$y) and ($j==$x) and (isset($x)) and (isset($y))) {
			print '</mark>';
		}
		print "</td>\n";
	}
	print "\t\t\t\t</tr>\n";
}
print "\t\t\t</table>\n";
//
if (isset($_GET['cycles_input'])) {
	$cycles = $_GET['cycles_input'];
} else if (!isset($cycles)) {
	$cycles = 1;
}
?>
		</div>
	</div>
	
	<!-- Controls -->
	<div class="col-md-5">
		<br><br>
		
		<div class="container">
			<div class="col-sm-6">
				<h3>Current cycle: <span class="label label-primary label-lg"><?=$step?></span></h3>
			</div>
		</div>
		
		<br><br>
		<hr>

		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Cycles to run:</label>
				<div class="col-sm-6">
					<input type="text" id="cycles" class="form-control form-lg" name="cycles" value="<?=$cycles?>"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-success btn-lg"">Go!</button>
				</div>
			</div>
		</form>
		
		<br>
		<hr>
		
		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="revert" value="1" />
			<input type="hidden" name="cycles_input" value="<?php print $cycles; ?>" />
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Previous state:</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-warning btn-lg"">Revert!</button>
				</div>
			</div>
		</form>
		
		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="reset" value="1" />
			<input type="hidden" name="cycles_input" value="<?php print $cycles; ?>" />
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Reset system:</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-danger btn-lg"">Reset!</button>
				</div>
			</div>
		</form>
		
	</div>		
</div>
	
<?php

/*
// Print Array of Numbers to D3
*/
/*
print 'state = [';
for ($i=0; $i<$nrow; $i++) {	
	print '[';
	for ($j=0; $j<$ncol; $j++) {
		print $state[$i][$j];
		if ($j < $ncol-1) {
			print ',';
		}
	}
	print ']';
	if ($i < $nrow-1) {
		print ',';
	}
}
print '];';
*/

?>

<!--
BLACKBOX CODE ENDS HERE
-->

	</div>

	<script src="../js/bootstrap.min.js"></script>
</body>
</html>
