<?php

declare(strict_types=1);

final class MyPlantController
{
    private PlantRepository $plants;
    private SpeciesRepository $species;
    private UserRepository $users;

    public function __construct(PlantRepository $plants, SpeciesRepository $species, UserRepository $users)
    {
        $this->plants = $plants;
        $this->species = $species;
        $this->users = $users;
    }

    public function index(Request $request): void
    {
        $userId = $request->queryInt('userId');

        $plants = $userId === null
            ? $this->plants->findAll()
            : $this->plants->findAllByUser($userId);

        Response::json(200, $this->withSpecies($plants));
    }

    public function due(): void
    {
        Response::json(200, $this->withSpecies($this->plants->findDue()));
    }

    public function show(int $id): void
    {
        $plant = $this->plants->findById($id);

        if ($plant === null) {
            Response::error(404, 'Plant not found.');

            return;
        }

        Response::json(200, $this->withSpecies([$plant])[0]);
    }

    public function store(Request $request): void
    {
        $nickname = $request->input('nickname');
        $userId = $request->input('userId');
        $speciesId = $request->input('speciesId');

        if (!is_string($nickname) || $nickname === '' || !is_numeric($userId) || !is_numeric($speciesId)) {
            Response::error(422, 'The nickname, userId and speciesId fields are required.');

            return;
        }

        if ($this->users->findById((int) $userId) === null) {
            Response::error(422, 'Unknown userId.');

            return;
        }

        $species = $this->species->findById((int) $speciesId);

        if ($species === null) {
            Response::error(422, 'Unknown speciesId.');

            return;
        }

        $override = $request->input('wateringIntervalHours');

        if ($override !== null && (!is_numeric($override) || (int) $override < 1)) {
            Response::error(422, 'The wateringIntervalHours field must be a positive number.');

            return;
        }

        $plant = new MyPlantModel(
            null,
            $nickname,
            $this->optionalString($request->input('location')),
            $override !== null ? (int) $override : null,
            'NOW',
            false,
            $this->optionalString($request->input('note')),
            (int) $userId,
            (int) $speciesId
        );

        $created = $this->plants->create($plant, $override !== null ? (int) $override : $species->getWateringIntervalHours());

        Response::created('/api/plants/' . $created->getId(), $this->withSpecies([$created])[0]);
    }

    public function update(int $id, Request $request): void
    {
        if ($this->plants->findById($id) === null) {
            Response::error(404, 'Plant not found.');

            return;
        }

        $changes = [];

        if ($request->has('nickname')) {
            $nickname = $request->input('nickname');

            if (!is_string($nickname) || $nickname === '') {
                Response::error(422, 'The nickname field cannot be empty.');

                return;
            }

            $changes['nickname'] = $nickname;
        }

        if ($request->has('location')) {
            $changes['location'] = $this->optionalString($request->input('location'));
        }

        if ($request->has('note')) {
            $changes['note'] = $this->optionalString($request->input('note'));
        }

        if ($request->has('needsAttention')) {
            $changes['needs_attention'] = $request->input('needsAttention') ? 1 : 0;
        }

        if ($request->has('wateringIntervalHours')) {
            $override = $request->input('wateringIntervalHours');

            if ($override !== null && (!is_numeric($override) || (int) $override < 1)) {
                Response::error(422, 'The wateringIntervalHours field must be a positive number.');

                return;
            }

            $changes['watering_interval_hours'] = $override !== null ? (int) $override : null;
        }

        if ($request->has('speciesId')) {
            $speciesId = $request->input('speciesId');

            if (!is_numeric($speciesId) || !$this->species->exists((int) $speciesId)) {
                Response::error(422, 'Unknown speciesId.');

                return;
            }

            $changes['species_id'] = (int) $speciesId;
        }

        $updated = $this->plants->update($id, $changes);

        Response::json(200, $this->withSpecies([$updated])[0]);
    }

    public function destroy(int $id): void
    {
        if (!$this->plants->delete($id)) {
            Response::error(404, 'Plant not found.');

            return;
        }

        Response::noContent();
    }

    private function withSpecies(array $plants): array
    {
        if ($plants === []) {
            return [];
        }

        $speciesIds = array_values(array_unique(array_map(
            static fn (MyPlantModel $plant): int => $plant->getSpeciesId(),
            $plants
        )));

        $species = $this->species->findByIds($speciesIds);

        return array_map(
            static fn (MyPlantModel $plant): MyPlantModel => isset($species[$plant->getSpeciesId()])
                ? $plant->withSpecies($species[$plant->getSpeciesId()])
                : $plant,
            $plants
        );
    }

    private function optionalString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
