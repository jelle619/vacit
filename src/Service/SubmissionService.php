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

    public function fetchRecentUserSubmissions($user, $limit = null)
    {
        if (is_null($limit)) return ($this->submissionRepository->createQueryBuilder('e')->where('e.candidate_id == $user')->orderBy('e.date', 'DESC')->getQuery()->getResult());
        return (
            $this->submissionRepository->createQueryBuilder('e')->where('e.candidate_id == $user')->orderBy('e.date', 'DESC')->setMaxResults($limit)->getQuery()->getResult()
        );
    }

    // private function fetchPoppodium($id) {
    //     return($this->poppodiumRepository->fetchPoppodium($id));
    // }

    // public function saveOptreden($params) {
    //     $data = [
    //       "id" => (isset($params["id"]) && $params["id"] != "") ? $params["id"] : null,
    //       "omschrijving" => $params["omschrijving"],
    //       "datum" => new \DateTime($params["datum"]),
    //       "prijs" => $params["prijs"],
    //       "ticket_url" => $params["ticket_url"],
    //       "afbeelding_url" => $params["afbeelding_url"],              
    //       "poppodium" => $this->fetchPoppodium($params["poppodium_id"]),
    //       "voorprogramma" => $this->fetchArtiest($params["voorprogramma_id"]),
    //       "hoofdprogramma" => $this->fetchArtiest($params["hoofdprogramma_id"])
    //     ];

    //     $result = $this->optredenRepository->saveOptreden($data);
    //     return($result);
    // }
}
