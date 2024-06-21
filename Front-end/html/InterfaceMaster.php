<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Interface Master</title>
  <link rel="shortcut icon" href="../img/logoatual.svg" type="image/x-icon" />
  <link rel="stylesheet" href="../css/InterfaceMaster.css" />
  <link rel="stylesheet" href="../css/headerMaster.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body>
  <?php include('../../headerMaster.php'); ?>

  <!-- Diálogo Modal para Adicionar Produto -->
  <dialog id="modalAdicionarProduto">
    <div class="form-header">
      <h2 class="title">Adicione um produto</h2>
    </div>
    <form id="formAdicionarProduto" action="../../Back-end/cadastrar_produto.php" method="POST" enctype="multipart/form-data">
      <div class="input-box">
        <label for="nomeProduto">Nome do Produto:</label>
        <input type="text" id="nomeProduto" name="nomeProduto" required>
      </div>

      <div class="input-box">
        <label for="precoProduto">Preço:</label>
        <input type="number" id="precoProduto" name="precoProduto" min="0" step="0.01" required>
      </div>

      <div class="input-box">
        <label for="descricaoProduto">Descrição:</label>
        <textarea id="descricaoProduto" name="descricaoProduto" rows="4" required></textarea>
      </div>

      <div class="input-box">
        <label for="categoriaProduto">Categoria:</label>
        <select id="categoriaProduto" name="categoriaProduto" required>
          <option value="Enxertos">Enxertos</option>
          <option value="Naturais (De semente)">Naturais (De semente)</option>
          <option value="Especiais">Especiais</option>
          <option value="Insumos">Insumos</option>
        </select>
      </div>

      <div class="input-box">
        <label for="subcategoriaProduto">Subcategoria:</label>
        <select id="subcategoriaProduto" name="subcategoriaProduto" required>
          <option value="Multipétalas">Multipétalas</option>
          <option value="Dobradas">Dobradas</option>
          <option value="Singelas">Singelas</option>
          <option value="Fertilizante">Fertilizante</option>
        </select>
      </div>

      <div class="input-box">
        <label for="quantidadeProduto">Quantidade em Estoque:</label>
        <input type="number" id="quantidadeProduto" name="quantidadeProduto" min="0" required>
      </div>

      <div class="input-box">
        <label for="disponivelVenda">Disponível para venda?</label>
        <select id="disponivelVenda" name="disponivelVenda" required>
          <option value="1">Sim</option>
          <option value="0">Não</option>
        </select>
      </div>


      <div class="input-box">
        <label for="imagemProduto">Imagem do Produto:</label>
        <div class="custom-file-input">
          <input type="file" id="imagemProduto" name="imagemProduto" accept="image/*" onchange="previewImg(event)" required>
          <button type="button" onclick="document.getElementById('imagemProduto').click()">Escolher arquivo</button>
        </div>
      </div>

      <!-- Área de pré-visualização da imagem -->
      <div id="previewContainer">
        <img id="previewImage" src="#" alt="Imagem de pré-visualização" style="display:none;">
      </div>

      <!-- Elemento para exibir a mensagem de resposta -->
      <div id="responseMessageProduto" style="display:none;"></div>

      <!-- Seus botões de envio e cancelamento -->
      <div class="button-box">
        <button type="submit" id="add_produto">Adicionar</button>
        <button type="button" onclick="fecharModalAdicionarProduto()" class="cancelar_add">Cancelar</button>
      </div>
    </form>
  </dialog>

  <!--cards -->
  <section class="card-container">
    <div class="card">
      <h3>Cadastrar Produto</h3>
      <img src="../img/addProduto.svg" alt="Ícone de adição de produtos para cadastrar novos produtos" class="iconCard" />
      <p>Adicione novos produtos ao seu catálogo.</p>
      <button onclick="abrirModalAdicionarProduto()">Cadastrar</button>
    </div>
    <!-- Demais cards aqui -->
  </section>

  <!-- ACESSIBILIDADES -->
  <section id="accessibility-section">
    <i class="fas fa-universal-access" id="accessibility-icon"></i>
    <div id="other-things">
      <i class="fas fa-moon" id="dark-mode-toggle"></i>
      <i class="fas fa-sun" id="light-mode-toggle"></i>
      <img class="img_letra" src="../img/aumentartext_1.svg" alt="" srcset="" id="increase-font"></i>
      <img class="img_letra" src="../img/diminuirtext_1.svg" alt="" srcset="" id="decrease-font"></i>
    </div>
  </section>

  <footer>
    <br>
    <div class="social-icons">
      <p> Siga-nos nas nossas redes sociais:</p>
      <a href="https://www.facebook.com/profile.php?id=100063959239107" class="icon" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://www.instagram.com/polen_azul?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="icon" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://www.whatsapp.com/catalog/5521981510975/?app_absent=0" class="icon" target="_blank"><i class="fab fa-whatsapp"></i></a>
    </div>
  </footer>

  <script src="../js/acessibilidade.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalAdicionarProduto = document.getElementById('modalAdicionarProduto');
      const formAdicionarProduto = document.getElementById('formAdicionarProduto');
      const previewImage = document.getElementById('previewImage');
      const responseMessageElement = document.getElementById('responseMessageProduto');

      window.abrirModalAdicionarProduto = function() {
        modalAdicionarProduto.showModal();
      };

      window.fecharModalAdicionarProduto = function() {
        modalAdicionarProduto.close();
        formAdicionarProduto.reset();
        previewImage.src = "#";
        previewImage.style.display = 'none';
        responseMessageElement.style.display = 'none';
      };

      document.getElementById('imagemProduto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });

      formAdicionarProduto.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            responseMessageElement.textContent = data.message;
            responseMessageElement.style.display = 'block';
            responseMessageElement.style.color = data.success ? 'green' : 'red';
            if (data.success) {
              formAdicionarProduto.reset();
              previewImage.src = "#";
              previewImage.style.display = 'none';
              setTimeout(function() {
                fecharModalAdicionarProduto();
                window.location.href = '/Seven_Gardens/index.php'; // Redirecionamento após o cadastro bem-sucedido
              }, 1500); // Aguarda 1,5 segundos antes de fechar o modal e redirecionar
            }
          })
          .catch(error => {
            console.error('Erro:', error);
            responseMessageElement.textContent = 'Erro ao enviar o formulário.';
            responseMessageElement.style.display = 'block';
            responseMessageElement.style.color = 'red';
          });
      });
    });
  </script>
</body>

</html>