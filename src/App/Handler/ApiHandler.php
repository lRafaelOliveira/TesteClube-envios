<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;

use function time;

class ApiHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dados = json_decode(file_get_contents('php://input'));
        if (!isset($dados)) {
            return new JsonResponse(['code' => 303, 'message' => 'Você Precisa Fornercer um Objeto com os requerimentos', 'data' => ['requerimentos' => ['cep_origem', 'cep_destino', 'peso', 'altura', 'largura', 'comprimento', 'valor_declarado']]]);
        }
        $cep_origem = $dados->cep_origem ?? false;
        $cep_destino = str_replace('-', "", $dados->cep_destino) ?? false;
        $peso = $dados->peso ?? false;
        $altura = $dados->altura ?? false;
        $largura = $dados->largura ?? false;
        $comprimento = $dados->comprimento ?? false;
        $valor_declarado = $dados->valor_declarado ?? false;
        if (!isset($cep_origem) || !isset($cep_destino) || !isset($peso) || !isset($altura) || !isset($largura) || !isset($comprimento) || !isset($valor_declarado)) {
            return new JsonResponse(['code' => 303, 'message' => 'Você Precisa Fornercer um Objeto com os requerimentos', 'data' => ['requerimentos' => ['cep_origem', 'cep_destino', 'peso', 'altura', 'largura', 'comprimento', 'valor_declarado']]]);
        }
        $adapter = new Adapter([
            'driver'   => 'Mysqli',
            'database' => 'frete',
            'username' => 'root',
            'password' => '',
        ]);
        $parameters = [
            'cep_inicio'   => $cep_destino,
            'cep_final'   => $cep_destino,
            'peso_inicial' =>  $peso,
            'peso_final' =>  $peso
        ];
        $sql = "SELECT * FROM `cotacao` INNER JOIN servicos ON cotacao.id_servico = servicos.id INNER JOIN transportadoras ON servicos.id_transportadora = transportadoras.id WHERE cep_inicio<=? AND cep_final>= ? AND peso_inicial <=? AND peso_final >=?";
        $statement = $adapter->createStatement($sql, $parameters);
        $result    = $statement->execute();
        $respostaApi = [
            "cotacao" => []
        ];
        if (!isset($result)) {
            return new JsonResponse(['code' => 404, 'message' => 'Não foi encontrado resultado com para o cep informado', 'data' => ['dados_enviados' => $dados]]);
        }

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet;
            $resultSet->initialize($result);
            foreach ($resultSet as $row) {
                $respostaApi['cotacao'][] = [
                    "servico" => $row->nm_servico,
                    "transportadora" => $row->nm_transportadora,
                    "prazo" => ($row->prazo_entrega > 1) ? "$row->prazo_entrega dias uteis" : "$row->prazo_entrega dia util",
                    "valor_frete" => $row->valor
                ];
            }
        }
        return new JsonResponse($respostaApi);
    }
}
