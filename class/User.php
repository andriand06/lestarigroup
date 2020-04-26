<?php
class User{
  private $_db = null;
  private $_formItem = [];

  public function validasiInsert($formMethod){
    $validate = new Validate($formMethod);

      $this->_formItem['username'] = $validate->setRules('username','Username',[
        
        'required' => true,
        'min_char' => 4,
        'regexp' => '/^[A-Za-z0-9]+$/',
        'unique' => ['tbladmin','username'],
      ]);

      $this->_formItem['password'] = $validate->setRules('password','Password',[
        
        'required' => true,
        'min_char' => 6,
        'regexp' => '/[A-Za-z]+[0-9]|[0-9]+[A-Za-z]/'
      ]);

      $this->_formItem['ulangi_password'] =
        $validate->setRules('ulangi_password','Ulangi password',[
        
        'required' => true,
        'matches' => 'password'
      ]);

      $this->_formItem['email'] = $validate->setRules('email','Email',[
        
        'required' => true,
        'email' => true
      ]);

      if(!$validate->passed()) {
        return $validate->getError();
      }
  }

  public function getItem($item){
    return isset($this->_formItem[$item]) ? $this->_formItem[$item] : '';
  }

  public function insert(){
    $this->_db = DB::getInstance();
    $newUser = [
      'username' => $this->getItem('username'),
      'password' => password_hash($this->getItem('password'),PASSWORD_DEFAULT),
      'email' => $this->getItem('email')
    ];
    return $this->_db->insert('tbladmin',$newUser);
  }

  public function validasiLogin($formMethod){
    $validate = new Validate($formMethod);

      $this->_formItem['username'] = $validate->setRules('username','Username',[
        
        'required' => true
      ]);

      $this->_formItem['password'] = $validate->setRules('password','Password',[
        
        'required' => true
      ]);

      if(!$validate->passed()) {
        return $validate->getError();
      } else {
        $this->_db = DB::getInstance();
        $this->_db->select('password');
        $result = $this->_db->getWhereOnce('tbladmin',['username','=',
                  $this->_formItem['username']]);

        if(empty($result) || !password_verify($this->_formItem['password'],
        $result->password)) {
          $pesanError[] = 'Maaf, username / password salah';
          return $pesanError;
        }
      }
  }

  public function login(){
    $_SESSION["username"] = $this->getItem('username');
    header("Location:index.php");

  }

  public function cekUserSession(){
    if (!isset($_SESSION["username"])) {
      header("Location: login.php");
   }
  }

  public function logout(){
    unset($_SESSION["username"]);
    header("Location: login.php");
  }

  public function generate($username){
    $this->_db = DB::getInstance();
    $result = $this->_db->getWhereOnce('tbladmin',['username','=',$username]);
    foreach ($result as $key => $val) {
      $this->_formItem[$key] = $val;
    }
  }

  public function validasiUbahPassword($formMethod){
    $validate = new Validate($formMethod);

      $this->_formItem['password_lama'] = $validate->
      setRules('password_lama','Password lama',[
        
        'required' => true,
      ]);

      $this->_formItem['password_baru'] = $validate->
      setRules('password_baru','Password baru',[
      
        'required' => true,
        'min_char' => 6,
        'regexp' => '/[A-Za-z]+[0-9]|[0-9]+[A-Za-z]/'
      ]);

      $this->_formItem['ulangi_password_baru'] = $validate->
      setRules('ulangi_password_baru','Ulangi password baru',[
        
        'required' => true,
        'matches' => 'password_baru'
      ]);

      if(!$validate->passed()) {
        return $validate->getError();
      } else {
        $this->_db = DB::getInstance();
        $this->_db->select('password');
        $result = $this->_db->getWhereOnce('tbladmin',['username','=',
                                            $this->_formItem['username']]);

        if(empty($result) || !password_verify($this->_formItem['password_lama'],
        $result->password)) {
          $pesanError[] = 'Maaf, password lama anda tidak sesuai';
          return $pesanError;
        }
      }
  }

  public function ubahPassword(){
    $newUserPassword = [
      'password' => password_hash($this->getItem('password_baru'),
                    PASSWORD_DEFAULT)
    ];
    $this->_db->update('tbladmin',$newUserPassword,['username','=',
    $this->_formItem['username']]);
  }

}
