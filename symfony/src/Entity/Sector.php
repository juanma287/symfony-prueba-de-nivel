<?php

namespace App\Entity;

use App\Repository\SectorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SectorRepository::class)
 * @UniqueEntity(
 *      fields={"nombre"},
 *      message="Este valor ya se utilizo")
 */
class Sector
{
    const ERROR_REGISTRO_ASOCIADO = 'Error: El sector no puede ser eliminado por que tiene empresas asociadas.';
    const EXITO_ELININACION = 'Sector eliminado correctamente.';
    const ERROR_ELININACION = 'Error: Problema al eliminar el sector.';
    const EXITO_CREACION = 'Sector creado correctamente.';
    const ERROR_CREACION = 'Error: Problema al eliminar el sector.';
    const EXITO_EDICION = 'Sector editado correctamente.';
    const ERROR_EDICION = 'Error: Problema al editar el sector.';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "El Nombre no puede estar en blanco")
     * @Assert\Length(
     *      min = 1,
     *      max = 100,
     *      minMessage = "El Nombre debe tener al menos {{ limit }} caracteres",
     *      maxMessage = "El Nombre no puede ser más largo que {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNombre();
    }
}
