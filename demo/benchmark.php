<?php

require 'comparison.php';

/*
 * ============================================================================
 *  BENCHMARK
 * ============================================================================
 */
function singlePass()
{
    $hoursPlayed = 46.8;
    $lifetimeMoney = 15800;
    $lifetimeBounty = 600;
    $numQuestsComplete = 22;
    $numQuestsFailed = 3;
    $numDied = 28; // noob!
    $kmWalked = 112;
    $kmSwim = 9;
    $favWeapon = 'Daedric Sword';
    $favSpell = 'Healing';

    // -----------------------------------------------
    //  CLASSICAL IMPLEMENTATION

    $classicStart = microtime(true);
    $classic = new PlayerStatsClassic(
        $hoursPlayed,
        $lifetimeMoney,
        $lifetimeBounty,
        $numQuestsComplete,
        $numQuestsFailed,
        $numDied,
        $kmWalked,
        $kmSwim,
        $favWeapon,
        $favSpell
    );

    $classic->getHoursPlayed();
    $classic->getLifetimeMoney();
    $classic->getLifetimeBounty();
    $classic->getNumQuestsComplete();
    $classic->getNumQuestsFailed();
    $classic->getNumDied();
    $classic->getKmWalked();
    $classic->getKmSwim();
    $classic->getFavSpell();
    $classic->getFavWeapon();

    $classicTime = microtime(true) - $classicStart;

    // -----------------------------------------------
    //  STRUCT IMPLEMENTATION

    $structStart = microtime(true);
    $struct = new PlayerStatsStruct([
        'hoursPlayed' => $hoursPlayed,
        'lifetimeMoney' => $lifetimeMoney,
        'lifetimeBounty' => $lifetimeBounty,
        'numQuestsComplete' => $numQuestsComplete,
        'numQuestsFailed' => $numQuestsFailed,
        'numDied' => $numDied,
        'kmWalked' => $kmWalked,
        'kmSwim' => $kmSwim,
        'favWeapon' => $favWeapon,
        'favSpell' => $favSpell,
    ]);

    $struct->hoursPlayed;
    $struct->lifetimeMoney;
    $struct->lifetimeBounty;
    $struct->numQuestsComplete;
    $struct->numQuestsFailed;
    $struct->numDied;
    $struct->kmWalked;
    $struct->kmSwim;
    $struct->favWeapon;
    $struct->favSpell;

    $structTime = microtime(true) - $structStart;

    return $structTime - $classicTime;
}

/**
 * ============================================================================
 *  RESULTS
 * ============================================================================
 */

$numPasses = 100;

$times = [];
foreach (range(1, $numPasses) as $i) {
    $times[] = singlePass();
}

printf("\nAverage time difference: %.8f\n", array_sum($times) / $numPasses);
