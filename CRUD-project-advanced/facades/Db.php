<?php

class Db extends Facade {
    
    protected static function getFacadeAccesor() {
        return 'db';
    }

}