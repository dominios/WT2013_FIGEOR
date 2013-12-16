<?php

// inicializacia

error_reporting(0);
require_once 'init.php';
include_once 'plugins/jpgraph/jpgraph.php';
include_once 'plugins/jpgraph/jpgraph_line.php';

// data - premenne - vypocty

$width = 750;
$height = 350;

$project = new Figeor\Models\Project($_GET['project']);
$projectDeadline = $project->getDeadline();
$projectDeadlieFormated = $project->getDeadline('d.m.Y H:i:s');
$projectPoints = $project->getPointsOverall();
$projectTasks = $project->getAllTaskCount();
$dStart = new DateTime("@$projectDeadline");
$dEnd = new DateTime("@" . time());
$dDiff = $dStart->diff($dEnd);
$projectDaysLeft = $dDiff->days;
$projectAvgBurnout = $projectPoints / $project->getOverallDurationDays();
$projectBurntPoints = $project->getBurntPoints();

$range = $project->getOverallDurationDays();
$scale = 1;
$density = $range / $scale;


$optimalData = array();
$diffData = array();
$tmp = $projectPoints;
for ($i = 1; $i <= $project->getOverallDurationDays(); $i+=$scale) {
    $tmp -= $projectAvgBurnout;
    $optimalData[] = $tmp;
    $daysData[] = $i;
    $diffData[] = null;
}

foreach ($project->getTasks() as $task) {
    $finishDay = $task->getFinishDay();
    if ($finishDay !== null) {
        $diffData[--$finishDay] += $task->getPoints();
    }
    foreach ($task->getSubTasks() as $sub) {
        $finishDay = $sub->getFinishDay();
        if ($finishDay !== null) {
            $index = --$finishDay;
            if ($index < 0)
                $index = 0;
            $diffData[$index] += $sub->getPoints();
        }
    }
}

$pts = $project->getPointsOverall();
$realData = array();
$i = 0;
foreach ($diffData as $diff) {

    if (($i + 1) > $project->getCurrentDay()) {
        $realData[$i++] = null;
        continue;
    }

    if ($diff > 0) {
        $pts -= $diff;
    }
    $realData[$i++] = $pts;
}

$realData[$project->getCurrentDay()] = $projectPoints - $projectBurntPoints;

// vytvorenie a vykreslenie grafu

$graph = new Graph($width, $height);
$graph->img->SetAntiAliasing(true);
$graph->SetScale('intint');
$graph->title->Set('GRAF SPAĽOVANIA');
$graph->xaxis->title->Set('deň');
$graph->yaxis->title->Set('body');
$graph->yaxis->HideZeroLabel();
$graph->xaxis->SetTickLabels($daysData);

$lineplotOptimal = new LinePlot($optimalData);
$lineplotOptimal->SetLegend('Optimálny priebeh');
$graph->Add($lineplotOptimal);

$lineplotReal = new LinePlot($realData);
$lineplotReal->SetLegend('Reálny priebeh');
$graph->Add($lineplotReal);
$graph->Stroke();

// debugging
die;
echo '<pre>';
print_r($daysData);
print_r($diffData);
print_r($realData);
echo '</pre>';