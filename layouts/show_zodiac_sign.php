<?php
include('header.php');

// Debugging - remova após testar
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data_nascimento = DateTime::createFromFormat('Y-m-d', $_POST['data_nascimento']);
if(!$data_nascimento) {
    die("<p>Data é inválida</p> <a href='index.php'>Voltar</a>");
}

$signos = simplexml_load_file(__DIR__ . '/signos.xml');
if ($signos === false) {
    die('Erro ao carregar o arquivo XML');
}

function verificar_signo($data, $inicio, $fim){
    $ano = $data->format('Y');  // Mudado de 'y' para 'Y' para ano com 4 dígitos
    $mes_dia = $data->format('d/m');
    
    // Para debugging
    error_log("Verificando data: " . $data->format('d/m/Y'));
    error_log("Início: $inicio, Fim: $fim");
    
    $data_inicio = DateTime::createFromFormat('d/m/Y', "$inicio/$ano");
    $data_fim = DateTime::createFromFormat('d/m/Y', "$fim/$ano");
    
    if ($data_inicio === false || $data_fim === false) {
        error_log("Erro ao criar datas de comparação");
        return false;
    }
    
    if($data_inicio > $data_fim) {
        if($data->format('m') == '01') {
            $data_inicio->modify('-1 year');
        } else {
            $data_fim->modify('+1 year');
        }
    }
    
    return ($data >= $data_inicio && $data <= $data_fim);
}

$signo_encontrado = null;

foreach($signos->signo as $signo){
    // Note a mudança aqui para usar dataInicio e dataFim ao invés de data_inicio e data_fim
    if(verificar_signo($data_nascimento, (string)$signo->dataInicio, (string)$signo->dataFim)){
        $signo_encontrado = $signo;
        break;
    }
}
?>

<body>
    <div class="container-fluid main-container mt-4 d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="content-wrapper text-center p-5" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); border-radius: 15px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); max-width: 500px;">
            <?php if ($signo_encontrado): ?>
                <div class="signo-result p-4" style="background: linear-gradient(to bottom right, #ffffff, #f8f9fa); border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h1 class="text-primary mb-4" style="color: #1976d2;">Seu signo é: <?= $signo_encontrado->signoNome ?></h1>
                    <p class="text-muted mb-5" style="color: #424242;"><?= $signo_encontrado->descricao ?></p>
                </div>
            <?php else: ?>
                <p class="text-danger mb-5">Não foi possível encontrar um signo correspondente para a data <?= $data_nascimento->format('d/m/Y') ?>.</p>
            <?php endif; ?>
            <div class="mt-4">
                <a href='index.php' class="btn btn-primary px-4 py-2" style="background-color: #1976d2; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.3s ease;">Voltar</a>
            </div>
        </div>
    </div>
</body>