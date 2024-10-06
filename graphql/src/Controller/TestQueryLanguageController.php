<?php

namespace App\Controller;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Error\SyntaxError;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;
use ReflectionException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/')]
class TestQueryLanguageController
{
    public function __construct()
    {

    }

    /**
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws Error
     * @throws Exception
     */
    public function __invoke(Request $request): Response
    {
        $source = <<<SCHEMA
type Query {
    echo(message: String!): String! 
}

type Mutation {
    sum(x: Int!, y: Int!): Int!
}
SCHEMA;

        try {
            $schema = BuildSchema::build($source);
            $rootValue = [
                'echo' => static fn (array $rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                'sum' => static fn (array $rootValue, array $args): int => $args['x'] + $args['y'],
                'prefix' => 'You said: ',
            ];

            $rawInput = $request->getContent();

            /*
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            */

            $input = json_decode($rawInput, true);
            $query = $input['request'];
            $variableValues = $input['variables'] ?? null;

            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
        } catch (Throwable $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }


        return new JsonResponse($result->toArray());
    }
}
