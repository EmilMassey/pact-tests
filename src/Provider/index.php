<?php

use DI\Container;
use Emil\PactTests\Domain\Model\Address;
use Emil\PactTests\Provider\Database;
use Emil\PactTests\Provider\Model\RichAddress;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once '../../vendor/autoload.php';

$container = new Container();
$container->set('serializer', new Serializer([new ObjectNormalizer()], [new JsonEncoder()]));
$container->set('database', new Database());

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/address', function (RequestInterface $request, ResponseInterface $response) {
    /** @var Serializer $serializer */
    $serializer = $this->get('serializer');
    /** @var Database $database */
    $database = $this->get('database');

    $response->getBody()->write($serializer->serialize(array_values($database->addresses), 'json'));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/address/{id}', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    /** @var Serializer $serializer */
    $serializer = $this->get('serializer');
    /** @var Database $database */
    $database = $this->get('database');

    $id = $request->getAttribute('id');
    $address = $database->getAddress($id);

    if ($address !== null) {
        $richAddress = RichAddress::createFromAddress($address, 'John', 'Kowalski');
        $response->getBody()->write($serializer->serialize($richAddress, 'json'));
    } else {
        $response = $response->withStatus(404);
        $response->getBody()->write($serializer->serialize([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'Resource not found',
            'status' => 404,
            'detail' => "Address \"$id\" does not exist",
        ], 'json'));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// @see https://docs.pact.io/getting_started/provider_states/
if (getenv('APP_ENV') === 'test') {
    $app->post('/setup-pact-state', function (RequestInterface $request, ResponseInterface $response): ResponseInterface {
        /** @var Database $database */
        $database = $this->get('database');

        $database->clear();
        $state = json_decode($request->getBody()->getContents(), true)['state'];

        switch ($state) {
            case 'There is at least one address':
            case 'Address 52d4387f-e880-4dbc-8c28-5f5d27aee05f exists':
            case 'Address "non-existent" does not exist':
                $database->addAddress('52d4387f-e880-4dbc-8c28-5f5d27aee05f', new Address('street', 'city', '11-111'));
                break;
            default:
                $response->getBody()->write("State '$state' is unknown");

                return $response->withStatus(400);
        }

        return $response->withStatus(204);
    });
}

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();