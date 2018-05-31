<?php
/**
 * PHP version 7.2
 * src\Utils.php
 */

namespace TDW18\Usuarios;

require_once __DIR__ . '/../bootstrap.php';

use Doctrine\ORM\Tools\SchemaTool;
use Firebase\JWT\JWT;
use TDW18\Usuarios\Entity\Usuario;

/**
 * Trait Utils
 *
 * @package TDW18\Usuario
 */
trait Utils
{
    /**
     * Get .env filename (.env.docker || .env || .env.dist)
     *
     * @param string $dir directory
     * @param string $filename filename
     *
     * @return string
     */
    public static function getEnvFileName(
        string $dir,
        string $filename = '.env'
    ): string
    {

        if (isset($_ENV['docker'])) {
            return $filename . '.docker';
        } elseif (file_exists($dir . '/' . $filename)) {
            return $filename;
        } else {
            return $filename . '.dist';
        }
    }

    /**
     * Load user data fixtures
     *
     * @param string $username user name
     * @param string $email user email
     * @param string $password user password
     * @param bool $isAdmin isAdmin
     *
     * @return void
     */
    public static function loadUserData(
        string $username,
        string $email,
        string $password,
        bool $isAdmin = false
    )
    {
        $user = new Usuario(
            $username,
            $email,
            $password,
            true,
            $isAdmin,
            $isAdmin
        );
        try {
            $e_manager = getEntityManager();
            $e_manager->persist($user);
            $e_manager->flush();
        } catch (\Doctrine\ORM\ORMException $e) {
            die('ERROR: ' . $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    /**
     * Update database schema
     *
     * @return void
     */
    public static function updateSchema()
    {
        $e_manager = getEntityManager();
        $metadata = $e_manager->getMetadataFactory()->getAllMetadata();
        $sch_tool = new SchemaTool($e_manager);
        $sch_tool->dropDatabase();
        $sch_tool->updateSchema($metadata, true);
    }

    /**
     * Get JWT token
     *
     * @param int $userId user id
     * @param string $username user name
     * @param bool $isAdmin isAdmin
     *
     * @return string JWT token
     */
    public static function getToken(
        int $userId,
        string $username,
        bool $isAdmin
    ): string
    {

        $current_time = time();
        $token = [
            'iat' => $current_time,
            'exp' => $current_time + 3600,    // expires in 60 minutes
            'user_id' => $userId,                 // user id.
            'username' => $username,               // user name
            'isMaestro' => $isAdmin,                // is Maestro?
            // 'scope' => ['read', 'write', 'delete']
        ];

        return JWT::encode($token, $_ENV['JWT_SECRET'], 'HS512');
    }
}
