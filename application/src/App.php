<?php

namespace application\src;

use core\control\System;
use core\view\html\GenericElement;

/**
 * Application's main class
 * Method App::main() must always exist.
 */
class App{
    
    /**
    * Write here your application's logic
    * @return void
    */
    public static function main(){
        System::display(GenericElement::layoutInflater("hello.html"));
    }
}