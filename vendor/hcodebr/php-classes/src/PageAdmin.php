<?php

namespace Hcode;


//                                        configuração do template do administrador


class PageAdmin extends Page
{

    public function __construct($opts = array(),$tpl_dir = "/views/admin/")     
    {

        parent::__construct($opts,$tpl_dir);   //  método construtor da classe pai(Page), com os parametros desta classe;

    }

}

?>