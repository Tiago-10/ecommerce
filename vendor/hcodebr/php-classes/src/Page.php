<?php 

//                                              CLASSE QUE FAZ A CONFIGURAÇÃO DO TEMPLATE;



namespace Hcode;  // namespace primcipal, definido no autoload do composer

use Rain\Tpl;  

class Page{

    private $tpl;         
    private $options =[];    
    private $defaults = [   // opções padrões
        "header"=>true,     // por padrão o header e o footer vai ser true 
        "footer"=>true,
        "data"=>[]         

    ];

    public function __construct($opts = array(),$tpl_dir = "/views/") 
    {

         $this->defaults["data"]["session"] = $_SESSION;

        $this->options = array_merge($this->defaults,$opts);    // o que for passado como parametro na vr $opts vai sobrescrever a vr $defauts

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,            // esta pasta vai ficar os arquivos html do template
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false
        );

        Tpl::configure( $config );

        $this->tpl = new Tpl;

        foreach ($this->options["data"] as $key => $value) {
           
            $this->tpl->assign($key,$value); // cria uma variavel, key nome da variavel value valor da variavel

        }

        if($this->options["header"] === true) $this->tpl->draw("header");    // se na vr $options o header for true exibe na tela o header
        
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
           
            $this->tpl->assign($key,$value);   

        }
    }


    public function setTpl($name,$data = array(),$returnHTML = false) // método para o conteudo html
    {
        $this->setData($data);                        
        return $this->tpl->draw($name,$returnHTML);  
    }    



    public function __destruct()       
    {

        if($this->options["footer"] === true)  $this->tpl->draw("footer");   // se na vr $options o FOOTER for true exibe na tela o FOOTER
        
    }




}


 ?>