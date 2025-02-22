<?php

class TreasureHunt
{
    private $record;   //記録
    private $gold;     //所持ゴールド
    private $position; //現在地
    private $turn;     //ターン数

    function __construct() {
        $this->record   = "";
        $this->gold     = 0;
        $this->position = 0;
        $this->turn     = 0;
    }
    public function execute($number, $arrInput): string
    {
        for ($i = 0; $i < strlen($arrInput); $i++) {
            //ターンを進める
            $this->turn++;
            //現在地の島クラスのインスタンスを取得する
            $land = LandControllerFactory::getController($this->position);
            //ダイスの出目を取得する
            $dice = $land->getDice($arrInput, $i);
            //記録内容を更新する
            $prefix = empty($this->record) ? "" : ", ";
            $this->record .= $land->getCurrentTurnRecord($dice, $prefix);
            //次のターンの現在地を取得する
            $this->position = $land->getNextPosition($dice, $this->gold);

            //ゴールに着いたら残りターン数があってもループを抜ける
            if ($this->position == Land::LandGoal) {
                $this->record .= ", G";
                break;
            }
        }

        return $number.', "'.$arrInput.'", '.$this->turn.", ".$this->gold.", ".'"'.$this->record.'"';
    }
}

class LandControllerFactory
{
    static public function getController($position){
        switch ($position) {
            case Land::LandA :
                return new LandA('A');
            case Land::LandB :
                return new LandB('B');
            case Land::LandC :
                return new LandC('C');
            case Land::LandD :
                return new LandD('D');
            case Land::LandE :
                return new LandE('E');
            default :
                return new LandStart('S');
        }
    }
}
class Land
{
    protected $landName; //島名

    const LandStart = 0;
    const LandGoal = 99;
    const LandA = 1;
    const LandB = 2;
    const LandC = 3;
    const LandD = 4;
    const LandE = 5;

    function __construct($landName)
    {
        $this->landName = $landName;
    }
    function getDice($arrInput, &$i): string
    {
        return $arrInput[$i];
    }
    function getCurrentTurnRecord($dice, $prefix): string
    {
        return $prefix . $this->landName . " " . $dice;
    }
    function getNextPosition($dice, &$gold): int
    {
        return 0;
    }
    function isGusu($dice): bool
    {
        return (($dice % 2) == 0) ? true : false;
    }
}
class LandStart extends Land
{
    function getNextPosition($dice, &$gold): int
    {
        switch ($dice) {
            case '1':
                return Land::LandA;
            case '2':
                return Land::LandB;
            case '6':
                return Land::LandC;
            default :
                return Land::LandStart;
        }
    }
}
class LandA extends Land
{
    function getNextPosition($dice, &$gold): int
    {
        switch ($dice) {
            case '3':
                return Land::LandB;
            case '4':
                return Land::LandC;
            default :
                $gold += 100;
                return Land::LandStart;
        }
    }
}
class LandB extends Land
{
    function getNextPosition($dice, &$gold): int
    {
        if (parent::isGusu($dice) === true) {
            return Land::LandE;
        }

        return Land::LandD;
    }
}
class LandC extends Land
{
    function getDice($arrInput, &$i): string
    {
        //サイコロ2回分の出目を取得する
        $dice = parent::getDice($arrInput, $i);
        $i++;
        $dice .= parent::getDice($arrInput, $i);
        return $dice;
    }
    function getNextPosition($dice, &$gold): int
    {
        //サイコロ2回分の出目を加算する
        $dice = (int)$dice[0] + (int)$dice[1];
        if (parent::isGusu($dice) === true) {
            $gold += 100;
            return Land::LandE;
        }
        $gold += 200;
        return Land::LandD;
    }
}
class LandD extends Land
{
    function getNextPosition($dice, &$gold): int
    {
        switch ($dice) {
            case '4':
            case '5':
                return Land::LandE;
            default :
                return Land::LandStart;
        }
    }
}
class LandE extends Land
{
    function getNextPosition($dice, &$gold): int
    {
        if ($dice == '6' && $gold >= 500) {
            return Land::LandGoal;
        }
        $gold += 100;
        return Land::LandC;
    }
}