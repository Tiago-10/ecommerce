<?php

//                                                   CLASSE MODEL DO USUARIO


    namespace Hcode\Model;   // namespace dos models
    use \Hcode\DB\Sql;      // classe sql
    use \Hcode\Model;      // classe Model principal

    class User extends Model{

        const SESSION = "User";

        // const SECRET = "";

        public static function login($login,$password)        // método etático login
        {
        
            $sql = new Sql();   

            $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(":LOGIN" => $login)); 


            if (count($results) === 0){   
            
                throw new \Exception("Usuário inexistente ou senha inválida"); 
            
            }

            $data =  $results[0];  
            
            if (password_verify($password,$data["despassword"]) === true){   

                $user = new User(); 

                $user->setData($data); // método para  set criado na classe Model principal  user = set+nomedocampo ex: setdespassword

                $_SESSION[User::SESSION] = $user->getValues();// $_SESSION[User::SESSION] = user acesando getValues() que tem o nome dos campos

                return $user;//   


            }else {
                throw new \Exception("Usuário inexsistente ou senha inválida");  
                
            }





        }

        public  static function verifyLogin($inadmin = true) // metodo que valida se a sesão existe
        {
            if (
                !isset($_SESSION[User::SESSION])// se não existir
                ||
                !$_SESSION[User::SESSION] // se for nula
                ||
                !(int)$_SESSION[User::SESSION]["iduser"] > 0 // se não for maior que 0 
                ||
                (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin // e se for diferente de $inadmin
            ){
                header("Location: /admin/login"); // se estiver algo errado volte para a pagina de login
                exit;
            }
        }

        public static function logout()
        {
            $_SESSION[User::SESSION] =  NULL;
        }
 
        //fim da parte de login

        public static function listALL()
        {

            $sql = new Sql();

            return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.idperson");

        }

        public function save(){ // para salvar

            $sql = new Sql();

            // pdesperson VARCHAR(64), 
            // pdeslogin VARCHAR(64), 
            // pdespassword VARCHAR(256), 
            // pdesemail VARCHAR(128), 
            // pnrphone BIGINT, 
            // pinadmin TINYINT

           $results = $sql->select("CALL sp_users_save(:desperson,:deslogin, :despassword, :desemail, :nrphone, :inadmin)",
           array(
                 ":desperson"=> $this->getdesperson(),
                 ":deslogin"=>$this->getdeslogin(),
                 ":despassword"=>User::getPasswordHash($this->getdespassword()),
                 ":desemail"=> $this->getdesemail(),
                 ":nrphone"=> $this->getnrphone(),
                 ":inadmin"=> $this->getinadmin()));

                 $this->setData($results[0]);

        }

        public function get($iduser)
        {

            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson)  WHERE a.iduser = :iduser",array(":iduser"=>$iduser));

            $this->setData($results[0]);

        }

        public function update() // para editar
        {
            $sql = new Sql();

            // pdesperson VARCHAR(64), 
            // pdeslogin VARCHAR(64), 
            // pdespassword VARCHAR(256), 
            // pdesemail VARCHAR(128), 
            // pnrphone BIGINT, 
            // pinadmin TINYINT

           $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson,:deslogin, :despassword, :desemail, :nrphone, :inadmin)",
           array(
                 ":iduser" =>$this->getiduser(),  
                 ":desperson"=> $this->getdesperson(),
                 ":deslogin"=>$this->getdeslogin(),
                 ":despassword"=>User::getPasswordHash($this->getdespassword()),
                 ":desemail"=> $this->getdesemail(),
                 ":nrphone"=> $this->getnrphone(),
                 ":inadmin"=> $this->getinadmin()));

                 $this->setData($results[0]);

        }

        public function delete()  // método para excluir registros
        {
            $sql = new Sql();

            $sql->query("CALL sp_users_delete(:iduser)",array(
                ":iduser"=>$this->getiduser()
            ));
        }

        public static function getPasswordHash($password)  // método para cripitografar senhas
        {
            return password_hash($password,PASSWORD_DEFAULT,[
                'cost'=>12
            ]);
        }

        public static  function getForgot($email)
        {
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.
            desemail = :email ",array(":email"=>$email));

            if(count($results)===0)
            {
                throw new \Exception("Não foi possivel recuperar a senha");
            }else{

                $data = $results[0];

                $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser,:desip)",array(
                    ":iduser"=>$data["iduser"],":desip"=>$_SERVER["REMOTE_ADDR"]
                ));

                if(count($results2) === 0)
                {
                    throw new Exception("Não foi possivel recuperar a senha");
                    
                }else{

                    $dataRecovery = $results2[0];

                    $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,User::SECRET,$dataRecovery["idrecovery"],MCRYPT_MODE_ECB));

                    $link = "http://www.hcodecommerce.com..br/admin/forgot/reset?code=$code";




                }
            }
        }


        
            
        

    }

?>