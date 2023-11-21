<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

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

class SubmissionService
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

    public function fetchSubmission($id = null)
    {
        if (is_null($id)) return (null);
        return ($this->submissionRepository->find($id));
    }

    public function fetchRecentSubmissions($limit = null)
    {
        if (is_null($limit)) return ($this->submissionRepository->createQueryBuilder('e')->orderBy('e.date', 'DESC')->getQuery()->getResult());
        return (
            $this->submissionRepository->createQueryBuilder('e')->orderBy('e.date', 'DESC')->setMaxResults($limit)->getQuery()->getResult()
        );
    }

    public function fetchRecentUserSubmissions($candidateId, $limit = null)
    {
        if (is_null($limit)) return (
            $this->submissionRepository
                ->createQueryBuilder('e')
                ->where('e.candidate = ?1')
                ->orderBy('e.date', 'DESC')
                ->setParameter(1, $candidateId)
                ->getQuery()
                ->getResult());
        return (
            $this->submissionRepository
                ->createQueryBuilder('e')
                ->where('e.candidate = ?1')
                ->orderBy('e.date', 'DESC')
                ->setMaxResults($limit)
                ->setParameter(1, $candidateId)
                ->getQuery()
                ->getResult()
        );
    }

    public function fetchRecentVanacySubmissions($vacancyId, $limit = null)
    {
        if (is_null($limit)) return (
            $this->submissionRepository
                ->createQueryBuilder('e')
                ->where('e.vacancy = ?1')
                ->orderBy('e.date', 'DESC')
                ->setParameter(1, $vacancyId)
                ->getQuery()
                ->getResult());
        return (
            $this->submissionRepository
                ->createQueryBuilder('e')
                ->where('e.vacancy = ?1')
                ->orderBy('e.date', 'DESC')
                ->setMaxResults($limit)
                ->setParameter(1, $vacancyId)
                ->getQuery()
                ->getResult()
        );
    }

    public function findSubmission($candidateId, $vacancyId)
    {
        return (
            $this->submissionRepository
                ->createQueryBuilder('e')
                ->where('e.candidate = ?1 AND e.vacancy = ?2')
                ->setParameter(1, $candidateId)
                ->setParameter(2, $vacancyId)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }
}
