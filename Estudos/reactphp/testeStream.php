<?php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

// Iniciando um server em localhost, porta 7171
$serverSock = stream_socket_server('tcp://127.0.0.1:7171');

// Aqui dizemos que ele não bloqueia execução
stream_set_blocking($serverSock, 0);

// Lista contendo todos os clientes conectados
$clients = array();

// Adicionamos um "leitor" que chamará o callback sempre que
// $serverSock estiver pronto para leitura (quando alguém se conectar, neste caso)
$loop->addReadStream($serverSock, function ($serverSock, $loop) use (&$clients) {
    $clientSock = stream_socket_accept($serverSock);
    stream_set_blocking($clientSock, 0);

    // Vamos identificar nossas conexões para entender melhor...
    $username = false;

    // Emite uma mensagem ao $clientSock que acabou de se conectar
    fwrite($clientSock, "Diga-nos seu nome: ");

    // Criamos também um buffer de leitura para o $clientSock
    // Este executa a cada mensagem enviada pelo $clientSock
    $loop->addReadStream($clientSock, function ($clientSock, $loop) use (&$username, &$clients) {

        // $username == false -> Ainda não autenticou-se
        if (!$username && $username = fgets($clientSock)) {
            $username = trim($username);
            fwrite($clientSock, "Bem-vindo, {$username}. Você está no chat maroto!\n\n");

            // Adiciona à lista de clients conhecidos
            $clients[] = $clientSock;
        }

        // Se já se identificou e enviou alguma mensagem, repasse
        if ($username && $text = fgets($clientSock)) {

            // Busco TODOS os clients conhecidos, e redistribuo
            // a mensagem $text para todos que não o remetente
            foreach ($clients as $client) {
                if ($client !== $clientSock) {
                    fwrite($client, "[{$username}] {$text}");
                }
            }
        }
    });
});

$loop->run();

?>