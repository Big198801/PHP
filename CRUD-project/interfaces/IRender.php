<?php

namespace app\interfaces;

interface IRender
{
    public function renderTemplate($template, $params = []);
}