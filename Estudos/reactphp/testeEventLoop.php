<?php

/**
 * EventLoop nada mais é que um laço infinito (infinito até que seja interrompido 
 * ou que não possua mais processos a executar) de repetição que organiza e elege blocos 
 * de código (como são as funções) para execução.
 */

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$numeros = range(0, 2);
$letras = range('A', 'C');

$callback01 = function () use (&$numeros) {
 echo current($numeros).' ';
 next($numeros);
};

$callback02 = function () use (&$letras) {
 echo current($letras).' ';
 next($letras);
};

$controle = function () use (&$numeros, &$letras, $loop) {
 // Se lemos o ultimo número e a ultima letra, pare
 if (!current($numeros) && !current($letras)) {
   $loop->stop();
 }
};

$loop->addPeriodicTimer(1, $callback01);
$loop->addPeriodicTimer(1, $callback02);
$loop->addPeriodicTimer(1, $controle);

// Inicia e executa o EventLoop
$loop->run();

// Saída esperada: 0 A B 1 C 2

/**
 * Acima o EventLoop deverá ter executado, a cada um segundo, os processos $callback01, 
 * $callback02 e $controle, sendo que este último identifica que o programa encerrou as 
 * leituras necessárias e solicita o fim da execução. É importante ressaltar que $callback01, 
 * $callback02 e _$controle _não executaram em paralelo, mas o tempo de processamento destes é 
 * tão ínfimo que podemos ter esta impressão. Experimente mudar o tempo, em segundos, dos 
 * timers (linhas 23 a 25) para visualizar melhor como o EventLoop organiza e elege os 
 * processos a serem executados.  
 */

?>