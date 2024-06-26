<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Entity\Candidate;
use App\Entity\Company;
use App\Entity\Employer;
use App\Entity\Submission;
use App\Entity\Vacancy;

use App\Repository\CandidateRepository;
use App\Repository\CompanyRepository;
use App\Repository\EmployerRepository;
use App\Repository\SubmissionRepository;
use App\Repository\VacancyRepository;

class VacancyService
{

    /** @var CandidateRepository $candidateRepository */
    private $candidateRepository;
    /** @var CompanyRepository $companyRepository */
    private $companyRepository;
    /** @var EmployerRepository $employerRepository */
    private $employerRepository;
    /** @var SubmissionRepository $submissionRepository */
    private $submissionRepository;
    /** @var VacancyRepository $vacancyRepository */
    private $vacancyRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->candidateRepository = $em->getRepository(Candidate::class);
        $this->companyRepository = $em->getRepository(Company::class);
        $this->employerRepository = $em->getRepository(Employer::class);
        $this->submissionRepository = $em->getRepository(Submission::class);
        $this->vacancyRepository = $em->getRepository(Vacancy::class);
    }

    public function fetchVacancy($id = null)
    {
        if (is_null($id)) return (null);
        return ($this->vacancyRepository->find($id));
    }

    public function fetchRecentVacancies($limit = null)
    {
        if (is_null($limit)) return ($this->vacancyRepository->findAll());
        return (
            $this->vacancyRepository->createQueryBuilder('e')->orderBy('e.date', 'DESC')->setMaxResults($limit)->getQuery()->getResult()
        );
    }

    public function fetchEmployerVacancies($id = null)
    {
        if (is_null($id)) return (null);
        return ($this->employerRepository->find($id)->getVacancies());
    }

    public function fetchRelatedVacancies($id = null)
    {
        if (is_null($id)) return (null);
        $employers = $this->vacancyRepository->find($id)->getEmployer()->getCompany()->getEmployers();
        $array = array();
        foreach ($employers as $employer) {
            $vacancies = $employer->getVacancies();
            foreach ($vacancies as $vacancy)  {
                if ($vacancy->getId() != $id) array_push($array, $vacancy);
            }
        }
        return($array);
    }
}
