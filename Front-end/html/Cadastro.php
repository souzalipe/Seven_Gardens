<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seven Gardens</title>
  <link rel="shortcut icon" href="../img/logoatual.svg" type="image/x-icon" />
  <link rel="stylesheet" href="../css/Cadastro.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="../css/header.css" />
  <link rel="stylesheet" href="../css/modalEstilos.css" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
  <script src="../js/acessibilidade.js"></script>
</head>

<body>
  <?php include('../../header.php'); ?>



  <?php
  include('../PHP/connect.php'); // Assegure-se de que este caminho está correto.

  $erroUserName = "";
  $erroCpf = "";
  $erroEmail = "";
  $mensagemSucesso = "";
  $showAlert = false;
  $showAlertError = false;



  // Funções para verificar se o email, cpf e login já existem no banco de dados.
  function emailExists($email, $pdo)
  {
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() ? true : false;
  }

  function cpfExists($cpf, $pdo)
  {
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE cpf = ?");
    $stmt->execute([$cpf]);
    return $stmt->fetchColumn() ? true : false;
  }

  function user_nameExists($user_name, $pdo)
  {
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE user_name = ?");
    $stmt->execute([$user_name]);
    return $stmt->fetchColumn() ? true : false;
  }

  // Essa função vai inserir o usuário no banco de dados
  function createUser($data, $pdo)
  {
    $sql = "INSERT INTO usuario (nome_completo, data_nascimento, sexo, nome_materno, cpf, email, telefone_celular, user_name, senha, tipo_usuario, ativado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT); // Hashing da senha
    $stmt->execute([
      $data['nome_completo'], $data['data_nascimento'], $data['sexo'], $data['nome_materno'], $data['cpf'],
      $data['email'], $data['telefone_celular'], $data['user_name'], $senhaHash, 'Cliente', 1
    ]);
    return $pdo->lastInsertId();
  }

  // Função para inserir o endereço no banco de dados
  function createAddress($data, $userId, $pdo)
  {
    $sql = "INSERT INTO endereco_completo (idUsuario, logradouro, numero, complemento, bairro, cidade, estado, cep)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      $userId, $data['logradouro'], $data['numero'], $data['complemento'], $data['bairro'],
      $data['cidade'], $data['estado'], $data['cep']
    ]);
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
      'nome_completo' => $_POST['nome_completo'],
      'data_nascimento' => $_POST['data_nascimento'],
      'sexo' => $_POST['genero'],
      'nome_materno' => $_POST['nome_materno'],
      'cpf' => $_POST['cpf'],
      'email' => $_POST['email'],
      'telefone_celular' => $_POST['telefone_celular'],
      'user_name' => $_POST['login'],
      'senha' => $_POST['senha'],
      'logradouro' => $_POST['rua'],
      'numero' => $_POST['numero'],
      'complemento' => $_POST['complemento'],
      'bairro' => $_POST['bairro'],
      'cidade' => $_POST['cidade'],
      'estado' => $_POST['estado'],
      'cep' => $_POST['cep']
    ];


    // Verifica se o email, CPF ou login já existem
    if (emailExists($data['email'], $pdo)) {
      // Se o email já existir, define a mensagem de erro correspondente
      $erroEmail = "Este E-mail já foi cadastrado! Por favor, tente outro E-mail.";
      // Define a flag $showAlertError como verdadeira para indicar que ocorreu um erro
      $showAlertError = true;
    }

    if (cpfExists($data['cpf'], $pdo)) {
      // Se o CPF já existir, define a mensagem de erro correspondente
      $erroCpf = "Este CPF já foi cadastrado! Por favor, tente outro CPF.";
      // Define a flag $showAlertError como verdadeira para indicar que ocorreu um erro
      $showAlertError = true;
    }

    if (user_nameExists($data['user_name'], $pdo)) {
      // Se o nome de usuário já existir, define a mensagem de erro correspondente
      $erroUserName = "Este login de usuário já foi cadastrado! Por favor, tente outro nome de login.";
      // Define a flag $showAlertError como verdadeira para indicar que ocorreu um erro
      $showAlertError = true;
    }

    // Se nenhum erro ocorreu (ou seja, nenhum dos campos já existe no banco de dados)
    if (!$showAlertError) {
      // Cria o usuário no banco de dados
      $userId = createUser($data, $pdo);
      // Cria o endereço do usuário no banco de dados
      createAddress($data, $userId, $pdo);
      // Define a mensagem de sucesso
      $mensagemSucesso = "sucesso!";
      // Define a flag $showAlert como verdadeira para indicar que a operação foi bem-sucedida
      $showAlert = true;
    }
  }

  // Encerra a conexão
  $pdo = null;
  ?>

  <!-- quando o usuario for cadastrar jogar direto pra página de login. -->
  <?php if ($showAlert) : ?>
    <script>
      setTimeout(function() {
        window.location.href = "Login.php";
      }, 3000); // 3000 milissegundos = 3 segundos
    </script>
  <?php endif; ?>


  <!-- Alerta de sucesso -->
  <div class="container-alert" style="<?php echo $showAlert ? 'display: block;' : 'display: none;'; ?>">
    <div class="alert alert-success">
      Usuário cadastrado com <?php echo $mensagemSucesso; ?>
    </div>
  </div>

  <section class="formulario">

    <form id="formulario" method="POST">
      <div id="mensagemform"></div>
      <label class="label_login" for="nome" id="labelNome"> Nome:</label>
      <input class="input_login" type="text" id="nome" name="nome_completo" maxlength="80" autofocus placeholder="Digite o seu nome " value="<?php echo isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : ''; ?>" />
      <div id="mensagemNome"></div>
      <div id="mensagemform"></div>

      <label class="label_login" for="nome" id="labelNome">
        Nome da mãe:</label>
      <input class="input_login" type="text" id="nomeDamae" name="nome_materno" maxlength="80" autofocus placeholder="Digite o seu nome " value="<?php echo isset($_POST['nome_materno']) ? htmlspecialchars($_POST['nome_materno']) : ''; ?>" />
      <div id="mensagemNomeMae"></div>

      <label for="nasc" class="label_login"> Data de nascimento: </label>
      <input class="input_login" type="date" id="data" name="data_nascimento" value="<?php echo isset($_POST['data_nascimento']) ? htmlspecialchars($_POST['data_nascimento']) : ''; ?>" />
      <div id="mensagemnascim"></div>

      <div class="genero">
        <label for="sexo" class="label_login"> Genêro:</label>
        <div class="input-group">
          <input type="radio" id="mcisgênero" class="genero" name="genero" value="Mulher cisgênero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "Mulher cisgênero" ? "checked" : ""; ?> />
          <label class="model" for="mcisgênero">Mulher cisgênero</label>
        </div>
        <div class="input-group">
          <input type="radio" id="mtransgênero" class="genero" name="genero" value="Mulher transgênero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "Mulher transgênero" ? "checked" : ""; ?> />
          <label class="model" for="mtransgênero">Mulher transgênero</label>
        </div>
        <div class="input-group">
          <input type="radio" id="hcisgênero" name="genero" class="genero" value="Homem cisgênero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "Homem cisgênero" ? "checked" : ""; ?> />
          <label class="model" for="hcisgênero">Homem cisgênero</label>
        </div>
        <div class="input-group">
          <input type="radio" id="htransgênero" name="genero" class="genero" value="Homem transgênero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "Homem transgênero" ? "checked" : ""; ?> />
          <label class="model" for="htransgênero">Homem transgênero</label>
        </div>
        <div class="input-group">
          <input type="radio" id="binário" name="genero" value="não-binário" class="genero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "não-binário" ? "checked" : ""; ?> />
          <label class="model" for="binário">não-binário</label>
        </div>
        <div class="input-group">
          <input type="radio" id="outro" name="genero" value="outro" class="genero" <?php echo isset($_POST['genero']) && $_POST['genero'] === "outro" ? "checked" : ""; ?> />
          <label class="model" for="outro">Outros</label>
        </div>
      </div>

      <div id="mensagemgenero"></div>
      <label for="cpf" id="labelCpf" class="label_login">CPF:</label>
      <input class="input_login" type="text" id="cpf" name="cpf" maxlength="11" placeholder="Digite seu cpf" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" />
      <div id="mensagemCPF" style="color:red"><?php echo $erroCpf ?></div>


      <label for="cel" class="label_login" id="labelCel">
        Telefone Celular:</label>
      <input class="input_login" type="tel" id="numero" name="telefone_celular" placeholder="(xx) xxxxx-xxxx" maxlength="14" value="<?php echo isset($_POST['telefone_celular']) ? htmlspecialchars($_POST['telefone_celular']) : ''; ?>" />

      <label class="label_login" for="ende" class="form" id="labelCep">
        Cep:</label>
      <input class="input_login" type="text" maxlength="9" id="cep" name="cep" placeholder="Digite seu cep" value="<?php echo isset($_POST['cep']) ? htmlspecialchars($_POST['cep']) : ''; ?>" />
      <div id="mensagemCep"></div>

      <label for="estado" class="label_login">Estado:</label>
      <select class="input_login" id="estado" name="estado" required>
        <option value="">Selecionar</option>
        <option value="AC" <?php echo isset($_POST['estado']) && $_POST['estado'] === "AC" ? "selected" : ""; ?>>Acre</option>
        <option value="AL" <?php echo isset($_POST['estado']) && $_POST['estado'] === "AL" ? "selected" : ""; ?>>Alagoas</option>
        <option value="AP" <?php echo isset($_POST['estado']) && $_POST['estado'] === "AP" ? "selected" : ""; ?>>Amapá</option>
        <option value="AM" <?php echo isset($_POST['estado']) && $_POST['estado'] === "AM" ? "selected" : ""; ?>>Amazonas</option>
        <option value="BA" <?php echo isset($_POST['estado']) && $_POST['estado'] === "BA" ? "selected" : ""; ?>>Bahia</option>
        <option value="CE" <?php echo isset($_POST['estado']) && $_POST['estado'] === "CE" ? "selected" : ""; ?>>Ceará</option>
        <option value="DF" <?php echo isset($_POST['estado']) && $_POST['estado'] === "DF" ? "selected" : ""; ?>>Distrito Federal</option>
        <option value="ES" <?php echo isset($_POST['estado']) && $_POST['estado'] === "ES" ? "selected" : ""; ?>>Espírito Santo</option>
        <option value="GO" <?php echo isset($_POST['estado']) && $_POST['estado'] === "GO" ? "selected" : ""; ?>>Goiás</option>
        <option value="MA" <?php echo isset($_POST['estado']) && $_POST['estado'] === "MA" ? "selected" : ""; ?>>Maranhão</option>
        <option value="MT" <?php echo isset($_POST['estado']) && $_POST['estado'] === "MT" ? "selected" : ""; ?>>Mato Grosso</option>
        <option value="MS" <?php echo isset($_POST['estado']) && $_POST['estado'] === "MS" ? "selected" : ""; ?>>Mato Grosso do Sul</option>
        <option value="MG" <?php echo isset($_POST['estado']) && $_POST['estado'] === "MG" ? "selected" : ""; ?>>Minas Gerais</option>
        <option value="PA" <?php echo isset($_POST['estado']) && $_POST['estado'] === "PA" ? "selected" : ""; ?>>Pará</option>
        <option value="PB" <?php echo isset($_POST['estado']) && $_POST['estado'] === "PB" ? "selected" : ""; ?>>Paraíba</option>
        <option value="PR" <?php echo isset($_POST['estado']) && $_POST['estado'] === "PR" ? "selected" : ""; ?>>Paraná</option>
        <option value="PE" <?php echo isset($_POST['estado']) && $_POST['estado'] === "PE" ? "selected" : ""; ?>>Pernambuco</option>
        <option value="PI" <?php echo isset($_POST['estado']) && $_POST['estado'] === "PI" ? "selected" : ""; ?>>Piauí</option>
        <option value="RJ" <?php echo isset($_POST['estado']) && $_POST['estado'] === "RJ" ? "selected" : ""; ?>>Rio de Janeiro</option>
        <option value="RN" <?php echo isset($_POST['estado']) && $_POST['estado'] === "RN" ? "selected" : ""; ?>>Rio Grande do Norte</option>
        <option value="RS" <?php echo isset($_POST['estado']) && $_POST['estado'] === "RS" ? "selected" : ""; ?>>Rio Grande do Sul</option>
        <option value="RO" <?php echo isset($_POST['estado']) && $_POST['estado'] === "RO" ? "selected" : ""; ?>>Rondônia</option>
        <option value="RR" <?php echo isset($_POST['estado']) && $_POST['estado'] === "RR" ? "selected" : ""; ?>>Roraima</option>
        <option value="SC" <?php echo isset($_POST['estado']) && $_POST['estado'] === "SC" ? "selected" : ""; ?>>Santa Catarina</option>
        <option value="SP" <?php echo isset($_POST['estado']) && $_POST['estado'] === "SP" ? "selected" : ""; ?>>São Paulo</option>
        <option value="SE" <?php echo isset($_POST['estado']) && $_POST['estado'] === "SE" ? "selected" : ""; ?>>Sergipe</option>
        <option value="TO" <?php echo isset($_POST['estado']) && $_POST['estado'] === "TO" ? "selected" : ""; ?>>Tocantins</option>
      </select>

      <label class="label_login" for="ende" class="form" id="labelCidade">
        Cidade:</label>
      <input class="input_login" type="text" id="cid" name="cidade" placeholder="Digite sua cidade" value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>" />

      <label id="labelBairro" for="ende" class="label_login"> Bairro:</label>
      <input class="input_login" type="text" id="bairro" name="bairro" placeholder="Digite seu bairro" value="<?php echo isset($_POST['bairro']) ? htmlspecialchars($_POST['bairro']) : ''; ?>" />

      <label id="labelRua" for="ende" class="label_login"> Rua:</label>
      <input class="input_login" type="text" id="rua" name="rua" placeholder="Digite sua rua" value="<?php echo isset($_POST['rua']) ? htmlspecialchars($_POST['rua']) : ''; ?>" />

      <label id="labelN" for="ende" class="label_login"> Nº:</label>
      <input class="input_login" type="number" id="num" name="numero" placeholder="Digite o número da casa" value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>" />

      <label for="ende" class="label_login"> Complemento:</label>
      <input class="input_login" type="text" id="comple" name="complemento" placeholder="Digite o complemento" value="<?php echo isset($_POST['complemento']) ? htmlspecialchars($_POST['complemento']) : ''; ?>" />

      <label class="label_login" id="labelLogin" for="login" class="form">Login:</label>
      <input class="input_login" type="text" id="login" name="login" placeholder="Digite um login" maxlength="6" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" />
      <div id="mensagemLogin" style="color:red"><?php echo $erroUserName ?></div>

      <label class="label_login" id="labelEmail" for="login" class="form">
        E-mail:</label>
      <input class="input_login <?php echo $erroEmail ? 'com_erro' : ''; ?>" type="email" id="email" name="email" placeholder="Digite um e-mail" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
      <div id="mensagemEmail" style="color:red"><?php echo $erroEmail ?></div>

      <label class="label_login" id="labelSenha" for="senha">
    Senha:
</label>
<div class="password-container">
    <input class="input_login" type="password" id="senha" name="senha" placeholder="Digite sua senha" maxlength="8" autocomplete="new-password" value="<?php echo isset($_POST['senha']) ? htmlspecialchars($_POST['senha']) : ''; ?>" />
    <i id="toggleSenha" class="far fa-eye"></i>
</div>

<label class="label_login" id="labelConfirmacao" for="senha2">
    Confirmação de Senha:
</label>
<div class="password-container">
    <input class="input_login" type="password" id="senhaC" name="senha2" placeholder="Digite sua senha" maxlength="8" autocomplete="new-password" value="<?php echo isset($_POST['senha']) ? htmlspecialchars($_POST['senha']) : ''; ?>" />
    <i id="toggleConfirme" class="far fa-eye"></i>
</div>

      <div id="mensagem"></div>
      <div class="conta">
        <p>Já possui uma conta?</p>
        <a href="Login.php" class="sessao">Iniciar sessão</a>
      </div>

      <div class="buttons">
        <button class="btn_cadastrar" type="submit" id="cadastrar" name="cadastrar">
          Cadastrar
        </button>
        <button class="btn_limpar" id="limpar">Limpar</button>
      </div>
    </form>
  </section>


  <section id="accessibility-section">
    <i class="fas fa-universal-access" id="accessibility-icon"></i>
    <div id="other-things">
      <i class="fas fa-moon" id="dark-mode-toggle"></i>

      <i class="fas fa-sun" id="light-mode-toggle"></i>
      <img class="img_letra" src="../img/aumentartext_1.svg" alt="" srcset="" id="increase-font"></i>
      <img class="img_letra" src="../img/diminuirtext_1.svg" alt="" srcset="" id="decrease-font"></i>
    </div>
  </section>

  <!-- <div class="trilho" id="trilho">
      <div class="indicador"></div>
    </div> -->
  <footer>
    <br />

    <div class="social-icons">
      <p>Siga-nos nas nossas redes sociais:</p>

      <a href="#" class="icon"><i class="fab fa-facebook"></i></a>
      <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
      <a href="#" class="icon"><i class="fab fa-whatsapp"></i></a>
    </div>
  </footer>
  <script src="../js/Cadastro.js"></script>
  <script src="../js/carrinho.js"></script>
  <script src="../js/modal_Edit_Produtos.js"></script>


</body>

</html>