<?php

interface AbleToLunch
{
    public function goToLunch();
}

interface AccessibleToRoom
{
    public function checkAccess();
}

class Student implements AbleToLunch, AccessibleToRoom {
    public function goToLunch()
    {
        // TODO: Implement goToLunch() method.
    }

    public function checkAccess()
    {
        // TODO: Implement checkAccess() method.
    }
}