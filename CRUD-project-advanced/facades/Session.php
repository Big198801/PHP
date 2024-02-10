<?php

class Session extends Facade {
    
    protected static function getFacadeAccesor() {
        return 'session';
    }

}