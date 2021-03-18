<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

\Bitrix\Main\UI\Extension::load('ui.bootstrap4');

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
            $argList = func_get_args();
            array_shift($argList);
            $temp = [];
            foreach($argList as $arg) {
                if(is_array($arg)) {
                    $temp[] = '['.implode(', ', $arg).']';
                } else {
                    $temp[] = $arg;
                }
            }
            $this->addMessage('<b>Вход: </b>'.implode(', ', $temp));
            $this->addMessage('<b>Эталон: </b> '.var_export($validator->getPlan(), true));
            $result = call_user_func($this->funcName, ...$args);
            $this->addMessage('<b>Результат: </b> '.var_export($result, true));
            $testResult = $validator->test($result);
            if($testResult)
                $this->addMessage('<span class="text-success">Тест пройден</span>');
            else
                $this->addMessage('<span class="text-danger">Тест не пройден</span>');
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

    if(isset($_GET['test'])) {
        $test = preg_replace('/[^a-zA-z0-9]/', '', strval($_GET['test']));
        require_once (__DIR__.'/include/'.$test.'.php');

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
                    $case->test(new Validator(0x0), 0, 0, 0),
                    $case->test(new Validator(0xFF0000), 0, 0, 255),
                    $case->test(new Validator(0xFFFF00), 0, 255, 255),
                    $case->test(new Validator(0xFFFFFF), 255, 255, 255),
                    $case->test(new Validator(0x800000), 0, 0, 128),
                    $case->test(new Validator(0x808000), 0, 128, 128),
                    $case->test(new Validator(0x808080), 128, 128, 128),
                    $case->test(new Validator(0xFF8080), 128, 128, 255),
                    $case->test(new Validator(0xFFFF80), 128, 255, 255),
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
        $maxScore = 10; //TODO
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
            ?>
            <h1>Тестирование функции: <?= $case->getFuncName() ?></h1>
            <table class="table table-sm table-striped">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Результат</th>
                    <th>Лог</th>
                </tr>
                </thead>
                <tbody>
                <? foreach($results as $result): ?>
                    <tr>
                        <td><?= $testNum ?></td>
                        <td><?= (($result->getResult()) ? 'OK' : 'FAIL') ?></td>
                        <td><?= implode(' ', $result->getLog()) ?></td>
                    </tr>
                    <?
                    if($result->getResult())
                        $successCnt++;
                    $testNum++;
                    ?>
                <? endforeach; ?>
                <?
                $prc = $successCnt/$countTests*100;
                $score = $ballCnt/100 * $prc;
                $totalScore += $score;
                ?>
                <tr>
                    <td></td>
                    <td>%</td>
                    <td><?= sprintf('%.2f', $prc) ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Балл</td>
                    <td><?= sprintf('%.2f', $score) ?></td>
                </tr>
                </tbody>
            </table>
            <?php
        }
        echo '<h1>Итоговый балл: '.sprintf('%.2f / %.2f', $totalScore, $maxScore).'</h1>';
    }
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");