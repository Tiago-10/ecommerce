<?php 

session_start();

require_once("vendor/autoload.php");     // autoload das dependências do projeto e tambem das classes(no padrão psr-4)

use\Slim\Slim;        // para parte de rotas
use\Hcode\Page;			// classe do template 
use\Hcode\PageAdmin;      // classe do template do administrador do site
use \Hcode\Model\User;      // classe model do usuario

$app = new \Slim\Slim();  

$app->config('debug', true);

$app->get('/', function() {      // rota index 
	
	$page = new Page;
	$page->setTpl("index");

	
});

$app->get('/admin', function() {  // rota admin 
	
	User::verifyLogin();

	$page = new PageAdmin();    
	$page->setTpl("index"); 

	
});

$app->get('/admin/login', function() {       //rota login do admin
	
	$page = new PageAdmin([
		"header"=>false,      // desabilitando o header e    footer pois o login não tem
		"footer"=>false
	]);
	$page->setTpl("login");         // (método setTpl da classe page)

	
});

$app->post('/admin/login', function() {   
	
	User::login($_POST["login"],$_POST["password"]); // passa os dados do formulario para o método login da classe User
	
	header("location: /admin");  // redirecione para a rota admin
	exit; 
});

$app->get('/admin/logout', function() {   
	
	User::logout(); // método da classe User  que destroi a sessão 

	header("location /admin/login");
	exit;
	
});

$app->get("/admin/users", function(){  // rota para lista de usuários

	User::verifyLogin();       // verifica se etá logado

	$users = User::listALL(); // método da classe sql que retorna um select dos usuarios;

	$page = new PageAdmin();

	$page->setTpl("users",array(  // cria uma variavel para campo do registro
		"users"=>$users
	));


});

$app->get("/admin/users/create", function(){ // /rota para cadastrar novos usuários

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");


});

$app->get("/admin/users/:iduser/delete",function($iduser){// excluir usuarios

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("location: /admin/users");
	exit;

});


$app->get("/admin/users/:iduser", function($iduser){ // rota para editar usuarios cadastrados

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update",array(
		"user"=>$user->getValues()
	));


});

$app->post("/admin/users/create",function(){ //para salvar  os dados inseridos no banco

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST); // cria automaticamente as variaveis com nome do campo

	$user->save();

	header("location: /admin/users");
	exit;


});

$app->post("/admin/users/:iduser",function($iduser){//para salvar  os dados editados no banco

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("location: /admin/users");
	exit;

});

$app->get("/admin/forgot",function(){ // rota para tela inicial do esqueci a senha

	$page = new PageAdmin([
		"header"=>false,      
		"footer"=>false
	]);
	
	$page->setTpl("forgot");         

});

$app->post("/admin/forgot", function(){

	
	$user = User::getForgot($_POST["email"]);  // método para pegar o email que está na classe User

});

$app->run(); // finaliza

 ?>