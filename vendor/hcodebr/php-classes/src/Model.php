<?php

//                       CLASSE MODEL PRINCIPAL


namespace Hcode; 

class Model{

    private $values = [];

    public function __call($name,$args)  // todavez que for chamado um método
    {
        $method = substr($name, 0, 3);    // set ou get 

        $fieldname = substr($name, 3, strlen($name));  // nome dos campos da tabela

        switch ($method)  
        {
            
            case "get"://caso for set: 
                return $this->values[$fieldname]; 
            break;
            
            case "set"://caso for set:
                $this->values[$fieldname]  = $args[0]; //$values recebe $args(que será os valores) ex: idusuario n5, a vr $args receberá o n5; 
            break;
        }

    }

    public function setData($data = array()) // método que cria set dinamicamente 
    {
        foreach ($data as $key => $value){ // na vr $data(que é a que está os dados do banco)
            
            $this->{"set".$key}($value);  // set + $key(key é o nome do campo ex: set+idusuario), o valor ex 5;

        }
    }

    public function getValues() // método que cria get dinamicamente 
    {
        return $this->values; // retorne a vr values(que vai estar o nome do campo)
    }



}
?>