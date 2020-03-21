<?php

use Dhii\Structs\Struct;
use Dhii\Structs\Ty;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * ============================================================================
 *  CLASSICAL IMPLEMENTATION
 * ============================================================================
 */
class PlayerStatsClassic
{
    /**
     * @var float
     */
    protected $hoursPlayed;

    /**
     * @var int
     */
    protected $lifetimeMoney;

    /**
     * @var int
     */
    protected $lifetimeBounty;

    /**
     * @var int
     */
    protected $numQuestsComplete;

    /**
     * @var int
     */
    protected $numQuestsFailed;

    /**
     * @var int
     */
    protected $numDied;

    /**
     * @var float
     */
    protected $kmWalked;

    /**
     * @var float
     */
    protected $kmSwim;

    /**
     * @var string
     */
    protected $favWeapon;

    /**
     * @var string
     */
    protected $favSpell;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param float  $hoursPlayed
     * @param int    $lifetimeMoney
     * @param int    $lifetimeBounty
     * @param int    $numQuestsComplete
     * @param int    $numQuestsFailed
     * @param int    $numDied
     * @param float  $kmWalked
     * @param float  $kmSwim
     * @param string $favWeapon
     * @param string $favSpell
     */
    public function __construct(
        float $hoursPlayed,
        int $lifetimeMoney,
        int $lifetimeBounty,
        int $numQuestsComplete,
        int $numQuestsFailed,
        int $numDied,
        float $kmWalked,
        float $kmSwim,
        string $favWeapon,
        string $favSpell
    ) {
        $this->hoursPlayed = $hoursPlayed;
        $this->lifetimeMoney = $lifetimeMoney;
        $this->lifetimeBounty = $lifetimeBounty;
        $this->numQuestsComplete = $numQuestsComplete;
        $this->numQuestsFailed = $numQuestsFailed;
        $this->numDied = $numDied;
        $this->kmWalked = $kmWalked;
        $this->kmSwim = $kmSwim;
        $this->favWeapon = $favWeapon;
        $this->favSpell = $favSpell;
    }

    /**
     * @since [*next-version*]
     *
     * @return float
     */
    public function getHoursPlayed() : float
    {
        return $this->hoursPlayed;
    }

    /**
     * @since [*next-version*]
     *
     * @return int
     */
    public function getLifetimeMoney() : int
    {
        return $this->lifetimeMoney;
    }

    /**
     * @since [*next-version*]
     *
     * @return int
     */
    public function getLifetimeBounty() : int
    {
        return $this->lifetimeBounty;
    }

    /**
     * @since [*next-version*]
     *
     * @return int
     */
    public function getNumQuestsComplete() : int
    {
        return $this->numQuestsComplete;
    }

    /**
     * @since [*next-version*]
     *
     * @return int
     */
    public function getNumQuestsFailed() : int
    {
        return $this->numQuestsFailed;
    }

    /**
     * @since [*next-version*]
     *
     * @return int
     */
    public function getNumDied() : int
    {
        return $this->numDied;
    }

    /**
     * @since [*next-version*]
     *
     * @return float
     */
    public function getKmWalked() : float
    {
        return $this->kmWalked;
    }

    /**
     * @since [*next-version*]
     *
     * @return float
     */
    public function getKmSwim() : float
    {
        return $this->kmSwim;
    }

    /**
     * @since [*next-version*]
     *
     * @return string
     */
    public function getFavWeapon() : string
    {
        return $this->favWeapon;
    }

    /**
     * @since [*next-version*]
     *
     * @return string
     */
    public function getFavSpell() : string
    {
        return $this->favSpell;
    }
}

/**
 * ============================================================================
 *  STRUCT IMPLEMENTATION
 * ============================================================================
 */

/**
 * @property-read float  hoursPlayed
 * @property-read int    lifetimeMoney
 * @property-read int    lifetimeBounty
 * @property-read int    numQuestsComplete
 * @property-read int    numQuestsFailed
 * @property-read int    numDied
 * @property-read float  kmWalked
 * @property-read float  kmSwim
 * @property-read string favWeapon
 * @property-read string favSpell
 */
class PlayerStatsStruct extends Struct
{
    /**
     * @inheritDoc
     */
    public function getPropTypes() : array
    {
        return [
            'hoursPlayed' => Ty::int(),
            'lifetimeMoney' => Ty::float(),
            'lifetimeBounty' => Ty::float(),
            'numQuestsComplete' => Ty::int(),
            'numQuestsFailed' => Ty::int(),
            'numDied' => Ty::int(),
            'kmWalked' => Ty::float(),
            'kmSwim' => Ty::float(),
            'favWeapon' => Ty::string(),
            'favSpell' => Ty::string(),
        ];
    }
}
