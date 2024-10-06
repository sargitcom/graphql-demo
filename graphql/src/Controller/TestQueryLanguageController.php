<?php

namespace App\Controller;

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/', methods: ["POST"])]
class TestQueryLanguageController
{
    public function __construct() {}

    public function __invoke(Request $request): Response
    {
        try {
            $queryType = new ObjectType([
                'name' => 'myData',
                'fields' => [
                    // '__typename' => Type::string(),
                    'hello' => [
                        'type' => Type::string(),
                        'resolve' => fn () => 'Hello World!',
                    ],
                    'hero' => [
                        'type' => Type::string(),
                        'args' => [
                            'episode' => [
                                'type' => Type::string(),
                            ],
                        ],
                        'resolve' => fn ($rootValue, array $args): string => $this->getHero($args['episode'] ?? null),
                    ]
                ]
            ]);

            $schema = new Schema((new SchemaConfig())->setQuery($queryType));

            $rawInput = file_get_contents('php://input');

            //var_dump($rawInput);
            //die;

            //var_dump($rawInput);
            //die;

            if ($rawInput === false) {
                throw new \RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);

            $query = $input['query'];

            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'I say: '];
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        // curl --data '{"query": "query { hello, hero(episode: \"TEST\") }" }' --header "Content-Type: application/json" http://localhost:1234/index.php/test-2

        return new JsonResponse($result->toArray());
    }

    public function getHero(string $hero): string
    {
        return "this is hero: $hero";
    }
}
