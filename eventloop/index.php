<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use Lipe\Php\EventLoop;
use Lipe\Php\Promise;

function async (callable $function) {
    return EventLoop::add($function());
}

function xsleep($seconds) {
    $endTime = $seconds + time();
    while(time() <= $endTime)
        yield;
}

function await(Promise $promise) {
    return (function () use ($promise) {
        while(EventLoop::hasDone($promise->getId()))
            yield;
    })();
}

function generatorA () {
    echo "(A) Iniciando generator A" . PHP_EOL;
    yield;
    
    yield from xsleep(4);

    echo "(A) Fim da generator A" . PHP_EOL;
    yield;

    return "(A) GenA Done";
}

function generatorB () {
    echo "(B) Iniciando generator B" . PHP_EOL;
    yield;

    echo "(B) esperando generator generatorA" . PHP_EOL;
    yield;

    $returnGenA = yield from generatorA(); // espera o geradorA terminar

    echo $returnGenA . PHP_EOL;
    yield;

    echo "(B) depois de esperar a generator: id generatorA" . PHP_EOL;
    yield;

    echo "(B) Encerrando generator B" . PHP_EOL;
}

function generatorC () {
    echo "(C) Iniciando C" . PHP_EOL;
    yield;
    foreach (range(0,15) as $i => $value) {
        echo "(C) $value" . PHP_EOL;
        yield;
    }
    echo "(C) Encerrando C" . PHP_EOL;
}

EventLoop::run(generatorB(), generatorC());



/* O codigo assima é igual ao codigo abaixo

$promise = async(function () {
    // outros codigos terão yield para liberar a execução dos proximos codigos dentro do EV;
    echo "qualquer coisa";
    yield;
    // Codigo com await
    yield from await(); // Esperaria o id 1 terminar
});

*/

/**
 * Para que isso aconteça eu preciso identificar as funcções com async/await e parsear para gerador
 * antes de executar o EV (Event Loop)
 * 
 * Possíveis soluções:
 * 1 - Criar um transpiler de funcao anonima para Generator análogo ao Typescript;
 * 2 - Em tempo de execução: identificar as funções async ao executar o event loop, teria um custo
 * de tempo maior no inicio do programa, pois teria que traduzir o codigo para gerador, mas posteriormente ficaria em memória
 * economizando tempo de execução e cachenado o resultado.
 * 
*/