<?php

include ('puzzle.php');

//verifica se o estado atual é maior que a profundidade, retorna o resultado e incrementa os passos
//se o estado atual for for igual ao teste do objetivo retorna o estado atual,
//verifica se o movimento é possivel e retorna o resultado 
function limite_profundidade(Puzzle $estado_atual, $max_profundidade, &$passos) {

    $resultado = false;
    if ($estado_atual->profundidade > $max_profundidade) {
        return $resultado;
    }
    $passos++;
    if ($estado_atual->objetivoTeste() == TRUE) {
        return $estado_atual;
    }

    $movimentos = $estado_atual->possivelMovimentos();
    foreach ($movimentos as $mover) {

        $resultado = limite_profundidade($mover, $max_profundidade, $passos);

        if ($resultado != FALSE) {
            return $resultado;
        }
    }

    return $resultado;
}

$tempo_inicio = microtime(true);

//posição inicial
$pos_inicial = array(
    array(7, 8, 4),
    array(0, 5, 2),
    array(3, 1, 6)
);

//posição objetivo
$pos_objetivo = array(
    array(1, 2, 3),
    array(4, 5, 6),
    array(7, 8, 0)
);

$estado_inicial = new Puzzle();
$estado_inicial->setInicial($pos_inicial);
$estado_inicial-> setObjetivo($pos_objetivo);

// max_ profundidade para achar a solucão com o maximo de movimentos que você preferir
$max_profundidade = 20;
$passos = 1;
$resultado = limite_profundidade($estado_inicial, $max_profundidade, $passos);
if ($resultado != FALSE) {
    print "Solucao encontrada avaliando $passos nos <br><br>";
    $resultado->imprimeSequencia();
} else {
    print "Solucao nao encontrada em $max_profundidade movimentos, avaliando $passos nos";
}
print ("<br>Maximo de memoria usada " . memory_get_peak_usage());
print ("<br>Memoria atual usada " . memory_get_usage());
$tempo_final = microtime(true);
$tempo_exec = $tempo_final - $tempo_inicio;
print("<br>Tempo de execucao usado = " . $tempo_exec);