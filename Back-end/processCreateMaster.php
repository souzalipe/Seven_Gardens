<?php
require '../Front-end/PHP/connect.php';

try {
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Insere o usuário Master
  $stmt = $pdo->prepare("INSERT INTO usuario
        (idUsuario, nome_completo, data_nascimento, sexo, nome_materno, cpf, email, telefone_celular, user_name, senha, tipo_usuario, ativado)
        VALUES
        (1, 'Admin Master', '2000-01-01', 'N', 'Nome Materno Padrão', '00000000000', 'admin@example.com', '0000000000', 'adminM', :senha, 'Master', 1)");
  $senhaHash = password_hash('senhamas', PASSWORD_DEFAULT); // Gera uma senha hash segura
  $stmt->bindParam(':senha', $senhaHash);
  $stmt->execute();

  // Insere o endereço do usuário Master
  $stmtEndereco = $pdo->prepare("INSERT INTO endereco_completo
        (idUsuario, logradouro, numero, complemento, bairro, cidade, estado, cep)
        VALUES
        (1, 'Rua Exemplo', '123', 'Apt 456', 'Centro', 'Cidade Exemplo', 'EX', '12345-678')");
  $stmtEndereco->execute();

  // Ajusta o AUTO_INCREMENT para que o próximo usuário comece com idUsuario = 2
  $pdo->exec("ALTER TABLE usuario AUTO_INCREMENT = 2");

  echo "Usuário Master registrado com sucesso!";
} catch (PDOException $e) {
  echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
