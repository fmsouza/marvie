<?php

class Style {

	private $name;
	private $type;


	public $attributes;
	public $event;

    public function __construct($name, $type='tag'){
        $this->name = $name;
        
        switch($type){
            case "class":
                $this->type = ".";
                break;
            case "id":
                $this->type = "#";
                break;
            case "tag":
                $this->type = "";
                break;
            default:
                $this->type = "";
                break;
        }
    }

	public function add(StyleAttribute $a) {
        $this->attributes[] = $a;
	}


	public function setEvent(Event $e) {
        $this->event = $e;
	}


	public function createAttribute($name,$type) {
        $this->add(new StyleAttribute($name, $type));
	}
    
    public function compose(){
        $return = "{$this->type}{$this->name}{";
        foreach($this->attributes as $att) $return .= "{$att->getName()}:{$att->getValue()};";
        $return .= "}";
        return $return; 
    }

}