<?php

namespace TDW18\Usuarios\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Razonamiento
 *
 * @ORM\Table(name="razonamientos", indexes={@ORM\Index(name="FK_IdSolucion", columns={"idSolucion"}), @ORM\Index(name="FK_IdUsuario_Razonamiento", columns={"idUsuario"})})
 * @ORM\Entity
 */
class Razonamiento implements \JsonSerializable
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
     * @ORM\Column(name="texto", type="text", length=65535, nullable=false)
     */
    private $texto;

    /**
     * @var bool
     *
     * @ORM\Column(name="justificado", type="boolean", nullable=false)
     */
    private $justificado;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="text", length=65535, nullable=false)
     */
    private $error;

    /**
     * @var bool
     *
     * @ORM\Column(name="propuestoPorAlumno", type="boolean", nullable=true)
     */
    private $propuestoPorAlumno;

    /**
     * @var \Solucion
     *
     * @ORM\ManyToOne(targetEntity="Solucion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSolucion", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $idSolucion;

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
     * Razonamiento constructor.
     * @param string $texto
     * @param bool $justificado
     * @param string $error |null
     * @param bool $propuestoPorAlumno
     * @param Solucion $idSolucion
     * @param Usuario $idUsuario
     */
    public function __construct(string $texto, bool $justificado, string $error, bool $propuestoPorAlumno, Solucion $idSolucion, Usuario $idUsuario)
    {
        $this->texto = $texto;
        $this->justificado = $justificado;
        $this->error = $error;
        $this->propuestoPorAlumno = $propuestoPorAlumno;
        $this->idSolucion = $idSolucion;
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
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @return bool
     */
    public function isJustificado(): bool
    {
        return $this->justificado;
    }

    /**
     * @param bool $justificado
     */
    public function setJustificado(bool $justificado): void
    {
        $this->justificado = $justificado;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isPropuestoPorAlumno(): bool
    {
        return $this->propuestoPorAlumno;
    }

    /**
     * @param bool $propuestoPorAlumno
     */
    public function setPropuestoPorAlumno(bool $propuestoPorAlumno): void
    {
        $this->propuestoPorAlumno = $propuestoPorAlumno;
    }

    /**
     * @return Solucion
     */
    public function getIdSolucion(): Solucion
    {
        return $this->idSolucion;
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
            'texto' => $this->getTexto(),
            'justificado' => $this->isJustificado(),
            'error' => $this->getError(),
            'propuestoPorAlumno' => $this->isPropuestoPorAlumno(),
            'idSolucion' => $this->getIdSolucion(),
            'idUsuario' => $this->getIdUsuario()
        ];
    }
}
