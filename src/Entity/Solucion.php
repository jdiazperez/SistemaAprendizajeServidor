<?php

namespace TDW18\Usuarios\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solucion
 *
 * @ORM\Table(name="soluciones", indexes={@ORM\Index(name="FK_idCuestion", columns={"idCuestion"}), @ORM\Index(name="FK_idUsuario_Solucion", columns={"idUsuario"})})
 * @ORM\Entity
 */
class Solucion implements \JsonSerializable
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
     * @ORM\Column(name="respuesta", type="text", length=65535, nullable=false)
     */
    private $respuesta;

    /**
     * @var bool
     *
     * @ORM\Column(name="correcta", type="boolean", nullable=false)
     */
    private $correcta;

    /**
     * @var bool
     *
     * @ORM\Column(name="propuestaPorAlumno", type="boolean", nullable=false)
     */
    private $propuestaPorAlumno;

    /**
     * @var \Cuestion
     *
     * @ORM\ManyToOne(targetEntity="Cuestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCuestion", referencedColumnName="id")
     * })
     */
    private $idCuestion;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUsuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * Solucion constructor.
     * @param string $respuesta
     * @param bool $correcta
     * @param bool $propuestaPorAlumno
     * @param Cuestion $idCuestion
     * @param Usuario $idUsuario
     */
    public function __construct(string $respuesta, bool $correcta, bool $propuestaPorAlumno, Cuestion $idCuestion, Usuario $idUsuario)
    {
        $this->respuesta = $respuesta;
        $this->correcta = $correcta;
        $this->propuestaPorAlumno = $propuestaPorAlumno;
        $this->idCuestion = $idCuestion;
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
    public function getRespuesta(): string
    {
        return $this->respuesta;
    }

    /**
     * @param string $respuesta
     */
    public function setRespuesta(string $respuesta): void
    {
        $this->respuesta = $respuesta;
    }

    /**
     * @return bool
     */
    public function isCorrecta(): bool
    {
        return $this->correcta;
    }

    /**
     * @param bool $correcta
     */
    public function setCorrecta(bool $correcta): void
    {
        $this->correcta = $correcta;
    }

    /**
     * @return bool
     */
    public function isPropuestaPorAlumno(): bool
    {
        return $this->propuestaPorAlumno;
    }

    /**
     * @param bool $propuestaPorAlumno
     */
    public function setPropuestaPorAlumno(bool $propuestaPorAlumno): void
    {
        $this->propuestaPorAlumno = $propuestaPorAlumno;
    }

    /**
     * @return Cuestion
     */
    public function getIdCuestion(): Cuestion
    {
        return $this->idCuestion;
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
            'solucion' => [
                'id' => $this->getId(),
                'respuesta' => $this->getRespuesta(),
                'correcta' => $this->isCorrecta(),
                'propuestaPorAlumno' => $this->isPropuestaPorAlumno(),
                'idCuestion' => $this->getIdCuestion(),
                'idUsuario' => $this->getIdUsuario()
            ]
        ];
    }
}
