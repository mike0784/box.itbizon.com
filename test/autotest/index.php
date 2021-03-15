<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

class Validator
{
    protected $plan;
    protected $fact;

    /**
     * Validator constructor.
     * @param $plan
     */
    final public function __construct($plan)
    {
        $this->plan = $plan;
    }

    /**
     * @param $fact
     * @return mixed
     */
    final public function test($fact)
    {
        $this->fact = $fact;
        return static::validate();
    }

    /**
     * @return mixed
     */
    protected function validate()
    {
        return ($this->plan === $this->fact);
    }

    /**
     * @return mixed
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return mixed
     */
    public function getFact()
    {
        return $this->fact;
    }
}

class ValidatorCountLetter extends Validator
{
    /**
     * @return mixed
     */
    protected function validate()
    {
        if(!is_array($this->fact))
            return true;

        if(count($this->fact) !== count($this->plan))
            return false;

        if(!empty(array_diff_assoc ($this->plan, $this->fact)))
            return false;

        return true;
    }
}

class TestResult
{
    protected $result;
    protected $log;

    /**
     * TestResult constructor.
     * @param bool $result
     * @param array $log
     */
    public function __construct(bool $result, array $log)
    {
        $this->result = $result;
        $this->log    = $log;
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }
}

class TestCase
{
    protected $funcName;
    protected $messages;

    /**
     * TestCase constructor.
     * @param string $funcName
     */
    public function __construct(string $funcName)
    {
        $this->funcName = $funcName;
        $this->messages = [];
    }

    /**
     * @param Validator $validator
     * @param mixed ...$args
     * @return TestResult
     */
    public function test(Validator $validator, ...$args) : TestResult
    {
        try
        {
            $this->clearMessages();
            if(!function_exists($this->funcName))
                throw new Exception('Функция `'.$this->funcName.'` не определена');
            $this->addMessage('Эталон: <pre>'.var_export($validator->getPlan(), true).'</pre>');
            $result = call_user_func($this->funcName, ...$args);
            $this->addMessage('Результат: <pre>'.var_export($result, true).'</pre>');
            $testResult = $validator->test($result);
            if($testResult)
                $this->addMessage('Тест пройден');
            else
                $this->addMessage('Тест не пройден');
            return new TestResult($testResult, $this->getMessages());
        }
        catch(Exception $e)
        {
            $this->addMessage('Исключительная ситуация: '.$e->getMessage());
            return new TestResult(false, $this->getMessages());
        }
    }

    /**
     * @param string $message
     */
    protected function addMessage(string $message)
    {
        $this->messages[] = $message;
    }

    /**
     *
     */
    protected function clearMessages()
    {
        $this->messages = [];
    }

    /**
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getFuncName() : string
    {
        return $this->funcName;
    }
}

try
{
    if (!function_exists('array_key_first')) {
        function array_key_first(array $arr) {
            foreach($arr as $key => $unused) {
                return $key;
            }
            return NULL;
        }
    }
    if (! function_exists("array_key_last")) {
        function array_key_last($array) {
            if (!is_array($array) || empty($array)) {
                return NULL;
            }

            return array_keys($array)[count($array)-1];
        }
    }

    /* BEGIN OF USER CODE SECTION */
    function weekend(string $begin, string $end) : int {
        $sub = 0;  $vos = 0;

        $interval =((strtotime($end) - strtotime($begin)) / 86400);
        $dayNumber = date("N", strtotime($begin));


        if ($interval >= 0) {

            if (($interval + $dayNumber) >= 6) {
                if ($dayNumber == 7) {
                    $sub = intdiv(($interval + 1), 7);
                } else {
                    $sub = 1 + intdiv(($interval - (6 - $dayNumber)), 7);
                }
            }
            if (($interval + $dayNumber) >= 7) {
                $vos = 1 + intdiv(($interval - (7 - $dayNumber)), 7);

            } else {
                $vos = 0;
            }

        } else {
            return 0;
        }
        return ($sub + $vos);
    }
    function rgb(int $r, int $g, int $b):int {
        return $b * 65536 + $g * 256 + $r ;
    }
    function fiborow(int $limit) : string {
        $f1 = 0;
        $f2 = 1;
        $f3 = 0;
        $string = "0";
        do {

            $string .= " ".($f1 + $f2);
            $f3 = $f1 + $f2;
            $f1 = $f2;
            $f2 = $f3;
        } while (($f1+$f2) < $limit);

        return $string;
    }

    $cases = [
        [
            ($case = new TestCase('search')),
            [
                $case->test(new Validator(-1), [], 1),

                $case->test(new Validator(0), [-1, 0], -1),
                $case->test(new Validator(1), [-1, 0], 0),

                $case->test(new Validator(0), [-1, 0, 1], -1),
                $case->test(new Validator(1), [-1, 0, 1], 0),
                $case->test(new Validator(2), [-1, 0, 1], 1),

                $case->test(new Validator(0), [-123, -34, 0, 35], -123),
                $case->test(new Validator(1), [-123, -34, 0, 35], -34),
                $case->test(new Validator(2), [-123, -34, 0, 35], 0),
                $case->test(new Validator(3), [-123, -34, 0, 35], 35),

                $case->test(new Validator(0), [-123, -34, 0, 35, 89], -123),
                $case->test(new Validator(1), [-123, -34, 0, 35, 89], -34),
                $case->test(new Validator(2), [-123, -34, 0, 35, 89], 0),
                $case->test(new Validator(3), [-123, -34, 0, 35, 89], 35),
                $case->test(new Validator(4), [-123, -34, 0, 35, 89], 89),
            ],
            2
        ],
        [
            ($case = new TestCase('weekend')),
            [
                $case->test(new Validator(0), '01.06.2020', '04.06.2020'),
                $case->test(new Validator(0), '01.06.2020', '01.06.2020'),
                $case->test(new Validator(8), '01.06.2020', '30.06.2020'),
                $case->test(new Validator(2), '01.06.2020', '07.06.2020'),
                $case->test(new Validator(2), '06.06.2020', '07.06.2020'),
                $case->test(new Validator(2), '07.06.2020', '13.06.2020'),
                $case->test(new Validator(2), '12.06.2020', '15.06.2020'),
                $case->test(new Validator(2), '28.06.2020', '04.07.2020'),
                $case->test(new Validator(4), '13.06.2020', '21.06.2020'),
            ],
            3
        ],
        [
            ($case = new TestCase('rgb')),
            [
                $case->test(new Validator(16777215), 255, 255, 255),
                $case->test(new Validator(65535), 255, 255, 0),
                $case->test(new Validator(16711935), 255, 0, 255),
                $case->test(new Validator(0), 0, 0, 0),
            ],
            3
        ],
        [
            ($case = new TestCase('fiborow')),
            [
                $case->test(new Validator(''), -1),
                $case->test(new Validator(''), 0),
                $case->test(new Validator('0'), 1),
                $case->test(new Validator('0 1 1'), 2),
                $case->test(new Validator('0 1 1 2'), 3),
                $case->test(new Validator('0 1 1 2 3 5 8'), 10),
            ],
            2
        ],
    ];

    $totalScore = 0;
    foreach($cases as $item)
    {
        $case    = $item[0];
        $results = $item[1];
        $ballCnt = $item[2];
        /** @var TestCase $case **/
        /** @var TestResult[] $results **/

        $testNum = 1;
        $countTests = count($results);
        $successCnt = 0;

        echo '<h1>Тестирование функции: '.$case->getFuncName().'</h1>';
        foreach($results as $result)
        {
            echo '<p>Тест '.$testNum.'/'.$countTests.' ... '.(($result->getResult()) ? 'OK' : 'FAIL').'</p>';
            echo implode('<br>', $result->getLog());
            echo '<p>--------------------------------------------------------------</p>';
            if($result->getResult())
                $successCnt++;
            $testNum++;
        }
        $prc = $successCnt/$countTests*100;
        $score = $ballCnt/100 * $prc;
        $totalScore += $score;
        echo '<p><b>Результат: '.sprintf('%.2f', $prc).' % - '.sprintf('%.2f', $score).' баллов</b></p>';
    }
    echo '<h1>Итоговый балл: '.sprintf('%.2f', $totalScore).'</h1>';
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");