<?php
function my_function(): void
{
    static $static_variable = 0;
    echo ++$static_variable; // значение увеличивается каждый раз при вывозе функции
}

for ($i = 0; $i < 5; $i++) {
    my_function();
}