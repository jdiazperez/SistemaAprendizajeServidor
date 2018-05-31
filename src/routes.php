<?php
/**
 * PHP version 7.2
 * src\routes.php
 */

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\Usuarios\Entity\Usuario;
use TDW18\Usuarios\Messages;

require_once __DIR__ . '/../bootstrap.php';

require __DIR__ . '/routes_user.php';

/**  @var \Slim\App $app */
/** @noinspection PhpUnusedParameterInspection */
$app->get(
    '/',
    function (Request $request, Response $response): Response {
        // Log message
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => 0, 'status' => 302]
        );

        // Redirect index view
        return $response
            ->withRedirect('/login.html');
    }
);

/**
 * POST /login
 *
 * @SWG\Post(
 *     method      = "POST",
 *     path        = "/login",
 *     tags        = { "login" },
 *     summary     = "Returns TDW Users api token",
 *     description = "Returns TDW Users api token.",
 *     operationId = "tdw_post_login",
 *     parameters  =  {
 *          {
 *          "name":             "username",
 *          "in":               "formData",
 *          "description":      "User name",
 *          "allowEmptyValue":  false,
 *          "required":         true,
 *          "type":             "string"
 *          },
 *          {
 *          "name":             "password",
 *          "in":               "formData",
 *          "description":      "User password",
 *          "allowEmptyValue":  false,
 *          "required":         true,
 *          "type":             "string",
 *          "format":           "password"
 *          }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "TDW Users api token",
 *          @SWG\Header(
 *              header      = "X-Token",
 *              description = "api token",
 *              type        = "string",
 *          ),
 *          @SWG\Schema(
 *              type        = "object",
 *              example     = {
 *                  "X-Token": "<JSON web token>"
 *              }
 *          )
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          ref         = "#/responses/404_Resource_Not_Found_Response"
 *     )
 * )
 */
/** @noinspection PhpUnusedParameterInspection */
$app->post(
    $_ENV['RUTA_LOGIN'],
    function (Request $request, Response $response): Response {

        /** @var TDW18\Usuarios\Entity\Usuario $user */
        $user = null;
        if (isset($_POST['nombreUsuario'], $_POST['contrasenia'])) {
            $user = getEntityManager()
                ->getRepository(Usuario::class)
                ->findOneBy(['nombreUsuario' => $_POST['nombreUsuario']]);

            if (null === $user || !$user->validatePassword($_POST['contrasenia'])) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['status' => 404]
                );
                return $response
                    ->withJson(
                        [
                            'code' => 404,
                            'message' => Messages::MESSAGES['tdw_post_login_404']
                        ],
                        404
                    );
            } elseif (!$user->isActivo()) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['status' => 403]
                );
                return $response
                    ->withJson(
                        [
                            'code' => 403,
                            'message' => 'No tiene permiso de acceso'
                        ],
                        403
                    );
            } else {
                $json_web_token = \TDW18\Usuarios\Utils::getToken(
                    $user->getId(),
                    $user->getNombreUsuario(),
                    $user->isMaestro()
                );
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $user->getId(), 'status' => 200]
                );

                /*                return $response
                                    ->withJson(['X-Token' => $json_web_token])
                                    ->withAddedHeader('X-Token', $json_web_token);*/
                return $response->withJson(['jwt' => $json_web_token, 'usuario' => $user]);
            }

        } else {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $user->getId(), 'status' => 422]
            );
            return $response
                ->withJson(
                    [
                        'code' => 422,
                        'message' => 'Faltan datos del usuario'
                    ],
                    422
                );
        }


    }
)->setName('tdw_post_login');

/**
 * Summary: Devuelve un código de status indicando si un usuario existe
 * basándose en su nombre de usuario
 */

$app->get('/api/users/nombreUsuario/{nombreUsuario:.+}',
    function (Request $request, Response $response, $args): Response {

        /** @var Usuario $usuario */
        $usuario = getEntityManager()
            ->getRepository(Usuario::class)
            ->findOneBy(['nombreUsuario' => $args['nombreUsuario']]);

        if ($usuario === null) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => 0, 'status' => 404]
            );

            return $response->withJson(
                [
                    'code' => 404,
                    'message' => 'El usuario no existe'
                ],
                404
            );
        } else {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => 0, 'status' => 200]
            );
            return $response->withStatus(200);
        }

    }
)->setName('tdw_get_users_nombreUsuario');

/**
 * POST /registro
 */

$app->post('/api/registro',
    function (Request $request, Response $response): Response {

        if (!isset($_POST['nombreUsuario'], $_POST['contrasenia'],
            $_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['telefono'])) {
            return $response
                ->withJson(
                    [
                        'code' => 422,
                        'message' => 'Faltan datos del usuario'
                    ],
                    422
                );
        }
        $entityManager = getEntityManager();
        /** @var Usuario $usuario */
        $usuario = new Usuario(
            $_POST['nombreUsuario'],
            $_POST['contrasenia'],
            $_POST['nombre'],
            $_POST['apellidos'],
            $_POST['correo'],
            $_POST['telefono']);

        $entityManager->persist($usuario);
        $entityManager->flush();

        return $response->withStatus(201);
    }
)->setName('tdw_post_registro');


