<?php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$start = microtime(true);

$timeout = $loop->addTimer(3, function () use ($start) {
    $intervalo = sprintf('%0.2f s', microtime(true) - $start);
    echo "[{$intervalo}] Timeout veio\n";
});

$interval = $loop->addPeriodicTimer(2, function () use ($start) {
    $intervalo = sprintf('%.2f s', microtime(true) - $start);
    echo "[{$intervalo}] Interval veio\n";
});

$loop->addTimer(15, function () use ($loop, $interval, $start) {
    if ($loop->isTimerActive($interval)) {
        $interval->cancel(); // Alias para $loop->cancelTimer($interval)
        $intervalo = sprintf('%.2f s', microtime(true) - $start);

        echo "[{$intervalo}] Interval infinito cancelado.\n";
    }
});

$loop->run();

/*
Saída esperada:
[2.00 s] Interval veio
[3.00 s] Timeout veio
[4.00 s] Interval veio
[6.00 s] Interval veio
[8.00 s] Interval veio
[10.00 s] Interval veio
[12.00 s] Interval veio
[14.00 s] Interval veio
[15.00 s] Interval infinito cancelado.
*/

/**
 * Analisando o código acima, deverá ser impresso uma única vez a frase "Timeout veio" 
 * após 3 segundos do início da execução do programa. E deverá ser impressa infinitamente a 
 * frase "Interval veio" a cada 2 segundos. Após 15 segundos de execução do programa 
 * realizamos uma ordem de cancelamento para o intervalo infinito caso ele esteja ativo 
 * perante o EventLoop. O EventLoop, portanto, não possuirá mais itens na fila de execução 
 * e encerra o programa na próxima iteração. 
 */

?>