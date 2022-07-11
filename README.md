<p align="center">
  <a href="#">
    <img src="https://i.ibb.co/HxZ2qQj/Omie-Export-Page-Captura.png">
  </a>
</p>
O plugin  Omie  Export  é  integrado  ao  plugin  Dokan e  API  Pagar.me  para  permitir  a exportação das ordens de  
venda e  vendedores cadastrados na  plataforma.  A exportação é  feita via  arquivo  Excel (xlsx). Os  arquivos  
exportados  devem  ser usados  na importação manual dos  respectivos  dados  dentro do ERP  Omie.

## Instalação
1.  Acesse o painel de administrador do WordPress.  
2.  Na barra lateral direita, vá para: Plugins > Adicionar novo.  
3.  Encontre e click  no botão  "Enviar Novo".  
4.  Selecione  o arquivo zip contendo  nosso plugin.  
5.  Por fim, clique no botão "Ativar" após a instalação.

## Requisitos
•  Gateway  de pagamento Pagar.me ativo.  
•  Credenciais de acesso para API  Pagar.me.  
•  Função  exec  do  PHP ativado no servidor.  
•  Plugin "WooCommerce  e  Dokan" ativado e instalado.  
•  Versão mínima do PHP  7.4.

## Acesso ao plugin
1.  Acesse o painel de administrador do WordPress.  
2.  Na barra lateral  esquerda, vá para:  Ferramentas  >  Omie Export.  
3.  Verifique  se a tela de exportação foi  aberta com sucesso.

## Configuração
Na tela de configurações do plugin insira as demais informações:

•  **Conteúdo exportado**  –  É  possível  selecionar entre  Ordem de serviços,  usuários da  plataforma  ou  ambos.  O conteúdo é exportado em um  zip contendo os  respectivos  
arquivos xlsx.

•  **Agrupamento de ordens**  –  Possibilita  agrupar diversos  pedidos em um  único  lançamento de ordem de serviço.  Um agrupamento corresponde a uma linha no  arquivo exportado.

•  **Nome do arquivo exportado**  –  O  nome do arquivo  zip  gerado na exportação dos  dados.  Por padrão é a data do dia no formato dd-mm-yy.zip.

•  **Filtrar por vendedor**  –  Possibilita  especificar um id  numérico  ou uma lista de ids  separados por  vírgula.  A exportação dos dados se limita  aos  usuários  especificados  pelos seus respectivos ids.

•  **Filtrar por data**  -  Possibilita especificar um  intervalo de datas.  Na exportação o  intervalo de data inicial e final correspondem a data de criação  das  ordens  pagas.  Use um range de datas para exportar dados de um  determinado  período,  por  exemplo  no  início  e final do  mês.  Novos  vendedores cadastrados nesse  período  serão  delimitados  para  a  exportação  de  usuários.

•  **Exportar dados**  –  Botão  para iniciar o processo de exportação de dados. Ao clicar  o  download do arquivo zip contendo os dados exportados será iniciado.

## Processo  de exportação
O processo  de exportação descreve as etapas  necessárias  para  a obtenção dos dados  de ordens  de serviço e  
usuários/vendedores  da  plataforma.

## Cenário típico
•  O  administrador  do site entra no exportador Omie.  
•  O  administrador  do site  específica  o conteúdo a ser exportado.  
•  O  administrador  do site  específica  um intervalo de dadas. Ex: 01/06/2022 a  31/06/2022.  
•  O  administrador  faz o download do arquivo  e envia para seu ERP ou contabilidade.