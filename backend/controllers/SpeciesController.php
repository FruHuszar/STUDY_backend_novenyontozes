<?php

declare(strict_types=1);

final class SpeciesController
{
    private SpeciesRepository $species;
    private FamilyRepository $families;
    private PhaseRepository $phases;

    public function __construct(SpeciesRepository $species, FamilyRepository $families, PhaseRepository $phases)
    {
        $this->species = $species;
        $this->families = $families;
        $this->phases = $phases;
    }

    public function index(Request $request): void
    {
        $month = $request->queryInt('bloomingIn');

        if ($month !== null) {
            if ($month < 1 || $month > 12) {
                Response::error(422, 'The bloomingIn parameter must be a month between 1 and 12.');

                return;
            }

            Response::json(200, $this->species->findBloomingInMonth($month));

            return;
        }

        Response::json(200, $this->species->findAll());
    }

    public function show(int $id): void
    {
        $species = $this->species->findById($id);

        if ($species === null) {
            Response::error(404, 'Species not found.');

            return;
        }

        Response::json(200, $species);
    }

    public function families(): void
    {
        Response::json(200, $this->families->findAll());
    }

    public function phases(): void
    {
        Response::json(200, $this->phases->findAll());
    }
}
