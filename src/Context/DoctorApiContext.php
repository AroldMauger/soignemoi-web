<?php
namespace App\Context;

use App\Entity\Doctors;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class DoctorApiContext
{
    private ?Doctors $doctor = null;

    public function setDoctors(Doctors $doctor): void
    {
        $this->doctor = $doctor;
    }

    public function getDoctors(): Doctors
    {
        if ($this->doctor === null) {
            throw new UnauthorizedHttpException("No doctor found in the context");
        }
        return $this->doctor;
    }
}
