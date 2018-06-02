<?php

namespace TDW18\Usuarios\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cuestion
 *
 * @ORM\Table(name="cuestiones", indexes={@ORM\Index(name="FK_idUsuario", columns={"idUsuario"})})
 * @ORM\Entity
 */
class Cuestion implements \JsonSerializable
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
     * @ORM\Column(name="enunciado", type="text", length=65535, nullable=false)
     */
    private $enunciado;

    /**
     * @var bool
     *
     * @ORM\Column(name="disponible", type="boolean", nullable=false)
     */
    private $disponible;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUsuario", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $idUsuario;

    /**
     * Cuestion constructor.
     * @param string $enunciado
     * @param bool $disponible
     * @param Usuario $idUsuario
     */
    public function __construct(string $enunciado, bool $disponible, Usuario $idUsuario)
    {
        $this->enunciado = $enunciado;
        $this->disponible = $disponible;
        $this->idUsuario = $idUsuario;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEnunciado(): string
    {
        return $this->enunciado;
    }

    /**
     * @param string $enunciado
     */
    public function setEnunciado(string $enunciado): void
    {
        $this->enunciado = $enunciado;
    }

    /**
     * @return bool
     */
    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    /**
     * @param bool $disponible
     */
    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }

    /**
     * @return Usuario
     */
    public function getIdUsuario(): Usuario
    {
        return $this->idUsuario;
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
            'enunciado' => $this->getEnunciado(),
            'disponible' => $this->isDisponible(),
            'idUsuario' => $this->getIdUsuario()
        ];
    }
}
