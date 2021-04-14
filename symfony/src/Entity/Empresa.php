<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EmpresaRepository::class)
 * @UniqueEntity(
 *      fields={"nombre"},
 *      message="Este valor ya se utilizo")
 */
class Empresa
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "El campo Nombre no puede estar en blanco")
     * @Assert\Length(
     *      min = 1,
     *      max = 100,
     *      minMessage = "El Nombre debe tener al menos {{ limit }} caracteres",
     *      maxMessage = "El Nombre no puede ser más largo que {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Email(
     *     message = "'{{ value }}' no es un email válido."
     * )
     */
    private $email;

     /**
     * @ORM\ManyToOne(targetEntity=Sector::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sector;

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

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }
    
}
