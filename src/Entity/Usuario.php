<?php

namespace TDW18\Usuarios\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuario
 *
 * @ORM\Table(name="usuarios", uniqueConstraints={@ORM\UniqueConstraint(name="nombreUsuario", columns={"nombreUsuario"})})
 * @ORM\Entity
 */
class Usuario implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreUsuario", type="string", length=20, nullable=false)
     */
    private $nombreUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="contrasenia", type="string", length=80, nullable=false)
     */
    private $contrasenia;

    /**
     * @var bool
     *
     * @ORM\Column(name="maestro", type="boolean", nullable=false)
     */
    private $maestro;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=20, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=40, nullable=false)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=20, nullable=false)
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=20, nullable=false)
     */
    private $telefono;

    /**
     * Usuario constructor.
     * @param string $nombreUsuario
     * @param string $contrasenia
     * @param string $nombre
     * @param string $apellidos
     * @param string $correo
     * @param string $telefono
     */
    public function __construct(string $nombreUsuario, string $contrasenia, string $nombre, string $apellidos, string $correo, string $telefono)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->setContrasenia($contrasenia);
        $this->maestro = false;
        $this->activo = false;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->correo = $correo;
        $this->telefono = $telefono;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNombreUsuario(): string
    {
        return $this->nombreUsuario;
    }

    /**
     * @param string $nombreUsuario
     */
    public function setNombreUsuario(string $nombreUsuario): void
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    /**
     * @return string
     */
    public function getContrasenia(): string
    {
        return $this->contrasenia;
    }

    /**
     * @param string $contrasenia
     */
    public function setContrasenia(string $contrasenia): void
    {
        $this->contrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);
    }

    /**
     * @return bool
     */
    public function isMaestro(): bool
    {
        return $this->maestro;
    }

    /**
     * @param bool $maestro
     */
    public function setMaestro(bool $maestro): void
    {
        $this->maestro = $maestro;
    }

    /**
     * @return bool
     */
    public function isActivo(): bool
    {
        return $this->activo;
    }

    /**
     * @param bool $activo
     */
    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    /**
     * @param string $apellidos
     */
    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return string
     */
    public function getCorreo(): string
    {
        return $this->correo;
    }

    /**
     * @param string $correo
     */
    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    /**
     * @return string
     */
    public function getTelefono(): string
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'nombreUsuario' => $this->getNombreUsuario(),
            'contrasenia' => $this->getContrasenia(),
            'maestro' => $this->isMaestro(),
            'activo' => $this->isActivo(),
            'nombre' => $this->getNombre(),
            'apellidos' => $this->getApellidos(),
            'correo' => $this->getCorreo(),
            'telefono' => $this->getTelefono()
        ];
    }

    public function validatePassword($contrasenia)
    {
        return password_verify($contrasenia, $this->contrasenia);
    }
}
