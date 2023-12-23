<?php

$region_city = [
    'Московская' => [
        'Москва',
        'Зеленоград',
        'Клин'
    ],
    'Ленинградская область' => [
        'Санкт-Петербург',
        'Всеволожск',
        'Павловск',
        'Кронштадт'
    ],
    'Чувашская Республика' => [
        'Чебоксары',
        'Цивильск',
        'Канаш',
        'Шумерля'
    ]
];

function sum(int $arg1, int $arg2): int
{
    return $arg1 + $arg2;
}

function diff(int $arg1, int $arg2): int
{
    return $arg1 - $arg2;
}

function prod(int $arg1, int $arg2): int
{
    return $arg1 * $arg2;
}

function quot(float $arg1, float $arg2): float|string
{
    return ($arg2 == 0) ? "Ошибка деления на ноль" : round($arg1 / $arg2, 2);
}

function with_operation(int $arg1, int $arg2, string $operator): int|string|float
{
    return match ($operator) {
        "+" => sum($arg1, $arg2),
        "-" => diff($arg1, $arg2),
        "*" => prod($arg1, $arg2),
        "/" => quot($arg1, $arg2),
        default => "Неправильный оператор"
    };
}

function transliteration(string $str): string
{
    $translate = [
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];

    $check_upper = false;
    $result = "";

    for ($i = 0; $i < strlen($str); $i++) {
        $char = mb_substr($str, $i, 1);
        if ($char == mb_strtoupper($char, 'UTF-8')) {
            $char = mb_strtolower($char, 'UTF-8');
            $check_upper = true;
        }
        foreach ($translate as $key_valid => $value) {
            if ($char === $key_valid) {
                if ($check_upper) {
                    $result .= ucfirst($value);
                } else {
                    $result .= $value;
                }
                $check_upper = false;
                continue(2);
            }
        }
        $check_upper = false;
        $result .= $char;
    }

    return $result;
}

function power(int $val, int $pow): int
{
    if ($pow === 0) {
        return 1;
    }
    return $val * power($val, $pow - 1);
}

function validate_time(int $hours, int $minutes): string
{
    $hours_out = get_validate_time($hours, "час", "часа", "часов");
    $minutes_out = get_validate_time($minutes, "минута", "минуты", "минут");
    return "$hours $hours_out $minutes $minutes_out";
}

function get_validate_time(int $number, string $singular, string $plural1, string $plural2): string
{
    $last_digit = $number % 10;

    if ($number > 10 && $number < 20) {
        return $plural2;
    } elseif ($last_digit === 1) {
        return $singular;
    } elseif ($last_digit > 1 && $last_digit < 5) {
        return $plural1;
    } else {
        return $plural2;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>homework_2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="example center">
    <h1 class="example__title">Практическое задание</h1>
    <article class="example__box">
        <h2 class="example__task_condition"><b>1.</b>Реализовать основные 4 арифметические операции в виде функции с
            двумя параметрами – два параметра это числа. Обязательно использовать оператор return.
            Проверьте деление на ноль и верните текст, ошибка деления на ноль.</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <form class="form" action="index.php" method="post">
                <label for="number_1">
                    <input class="input" name="num1_ex01" id="number_1" type="number" placeholder="Число 1" required>
                </label>
                <label for="number_2">
                    <input class="input" name="num2_ex01" id="number_2" type="number" placeholder="Число 2" required>
                </label>
                <button class="button" type="submit">Посчитать</button>
            </form>
            <?php
            if (isset($_POST['num1_ex01']) && isset($_POST['num2_ex01'])):
                $num1_ex01 = (int)$_POST['num1_ex01'];
                $num2_ex01 = (int)$_POST['num2_ex01']; ?>
                <p class="result"><?php echo "Сумма чисел: " . sum($num1_ex01, $num2_ex01); ?></p>
                <p class="result"><?php echo "Разница чисел: " . diff($num1_ex01, $num2_ex01); ?></p>
                <p class="result"><?php echo "Произведение чисел: " . prod($num1_ex01, $num2_ex01); ?></p>
                <p class="result"><?php echo "Деление чисел: " . quot($num1_ex01, $num2_ex01); ?></p>
            <?php endif; ?>
        </details>
    </article>
    <article class="example__box">
        <h2 class="example__task_condition"><b>2.</b>Реализовать функцию с тремя параметрами: function
            mathOperation($arg1, $arg2, $operation), где $arg1, $arg2 – значения аргументов, $operation – строка с
            названием операции. В зависимости от переданного значения операции выполнить одну из арифметических операций
            и вернуть полученное значение (использовать switch). Используйте функции
            из п.1</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <form class="form" action="index.php" method="post">
                <label for="number_1">
                    <input class="input" name="num1_ex02" id="number_1" type="number" placeholder="Число 1" required>
                </label>
                <label for="number_2">
                    <input class="input" name="num2_ex02" id="number_2" type="number" placeholder="Число 2" required>
                </label>
                <label for="operator">
                    <input class="input" name="operate" id="operator" type="text" placeholder="+,-,*,/" required>
                </label>
                <button class="button" type="submit">Посчитать</button>
            </form>
            <?php
            if (isset($_POST['num1_ex02']) && isset($_POST['num2_ex02']) && isset($_POST['operate'])):
                $num1_ex02 = (int)$_POST['num1_ex02'];
                $num2_ex02 = (int)$_POST['num2_ex02'];
                $operator = $_POST['operate']; ?>
                <p class="result"><?php echo "Результат: " . with_operation($num1_ex02, $num2_ex02, $operator); ?></p>
            <?php endif; ?>
        </details>
    </article>
    <article class="example__box">
        <h2 class="example__task_condition"><b>3.</b>Объявить массив, в котором в качестве ключей будут использоваться
            названия областей, а в качестве значений – массивы с названиями городов из соответствующей области. Вывести
            в цикле значения массива, чтобы результат был таким:<br>
            Московская область: Москва, Зеленоград, Клин<br>
            Ленинградская область: Санкт-Петербург, Всеволожск, Павловск, Кронштадт<br>
            Рязанская область … (названия городов можно найти на maps.yandex.ru).</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <?php foreach ($region_city as $region => $citys): ?>
                <p class="result">
                    <?php echo $region;
                    if ($citys !== null):
                        echo ": ";
                        foreach ($citys as $city):
                            echo $city;
                            if (next($citys) !== false)
                                echo ", ";
                        endforeach;
                    endif; ?>
                </p>
            <?php endforeach; ?>
        </details>
    </article>
    <article class="example__box">
        <h2 class="example__task_condition"><b>4.</b>Объявить массив, индексами которого являются буквы русского языка,
            а значениями – соответствующие латинские буквосочетания (‘а’ => ’a’, ‘б’ => ‘b’, ‘в’ => ‘v’, ‘г’ => ‘g’, …,
            ‘э’ => ‘e’, ‘ю’ => ‘yu’, ‘я’ => ‘ya’). Написать функцию транслитерации строк.</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <form class="form" action="index.php" method="post">
                <label for="transliteration">
                    <input class="input input_text" name="transliteration" id="transliteration" type="text"
                           placeholder="Текст на русском" required>
                </label>
                <button class="button" type="submit">Перевести</button>
            </form>
            <?php
            if (isset($_POST['transliteration'])):
                $string = $_POST['transliteration']; ?>
                <p class="result"><?php echo "Результат: " . transliteration($string); ?></p>
            <?php endif; ?>
        </details>
    </article>
    <article class="example__box">
        <h2 class="example__task_condition"><b>5.</b>*С помощью рекурсии организовать функцию возведения числа в
            степень. Формат: function power($val, $pow), где $val – заданное число, $pow – степень.</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <form class="form" action="index.php" method="post">
                <label for="number_1">
                    <input class="input" name="num1_ex05" id="number_1" type="number" placeholder="Число" required>
                </label>
                <label for="number_2">
                    <input class="input" name="num2_ex05" id="number_2" type="number" placeholder="Степень" required>
                </label>
                <button class="button" type="submit">Посчитать</button>
            </form>
            <?php
            if (isset($_POST['num1_ex05']) && isset($_POST['num2_ex05'])):
                $num1_ex05 = (int)$_POST['num1_ex05'];
                $num2_ex05 = (int)$_POST['num2_ex05']; ?>
                <p class="result"><?php echo "Число $num1_ex05 в степни $num2_ex05: " . power($num1_ex05, $num2_ex05); ?></p>
            <?php endif; ?>
        </details>
    </article>
    <article class="example__box">
        <h2 class="example__task_condition"><b>6.</b>*Написать функцию, которая вычисляет текущее время и возвращает его
            в формате с правильными склонениями.</h2>
        <details open class="example__task_solution">
            <summary>Решение</summary>
            <form class="form" action="index.php" method="post">
                <label for="number_1">
                    <input class="input" name="num1_ex06" id="number_1" type="number" placeholder="Часы" required>
                </label>
                <label for="number_2">
                    <input class="input" name="num2_ex06" id="number_2" type="number" placeholder="Минуты" required>
                </label>
                <button class="button" type="submit">Посчитать</button>
            </form>
            <?php
            if (isset($_POST['num1_ex06']) && isset($_POST['num2_ex06'])):
                $num1_ex06 = (int)$_POST['num1_ex06'];
                $num2_ex06 = (int)$_POST['num2_ex06']; ?>
                <p class="result"><?php echo "Результат: " . validate_time($num1_ex06, $num2_ex06); ?></p>
            <?php endif; ?>
        </details>
    </article>
</div>
</body>
</html>
