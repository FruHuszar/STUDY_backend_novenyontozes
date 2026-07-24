<?php

declare(strict_types=1);

final class PlantController
{
    private const MAX_NICKNAME_LENGTH = 100;
    private const MAX_LOCATION_LENGTH = 100;
    private const MAX_NOTE_LENGTH = 1000;

    public function __construct(private readonly PlantService $plants)
    {
    }

    public function index(Request $request): Response
    {
        return Response::json(200, PlantResource::collection($this->plants->list($request->queryInt('userId'))));
    }

    public function due(Request $request): Response
    {
        return Response::json(200, PlantResource::collection($this->plants->listDue($request->queryInt('userId'))));
    }

    public function show(Request $request): Response
    {
        return Response::json(200, PlantResource::make($this->plants->find($request->id())));
    }

    public function store(Request $request): Response
    {
        $validator = $request->validator();

        $data = [
            'nickname' => $validator->requiredString('nickname', self::MAX_NICKNAME_LENGTH),
            'userId' => $validator->requiredId('userId'),
            'speciesId' => $validator->requiredId('speciesId'),
            'location' => $validator->optionalString('location', self::MAX_LOCATION_LENGTH),
            'note' => $validator->optionalString('note', self::MAX_NOTE_LENGTH),
            'wateringIntervalHours' => $validator->optionalPositiveInt('wateringIntervalHours'),
        ];

        $validator->assertValid();

        $plant = $this->plants->create($data);

        return Response::created('/api/plants/' . $plant->getPlant()->getId(), PlantResource::make($plant));
    }

    public function update(Request $request): Response
    {
        $validator = $request->validator();
        $changes = [];

        if ($validator->has('nickname')) {
            $changes['nickname'] = $validator->requiredString('nickname', self::MAX_NICKNAME_LENGTH);
        }

        if ($validator->has('location')) {
            $changes['location'] = $validator->optionalString('location', self::MAX_LOCATION_LENGTH);
        }

        if ($validator->has('note')) {
            $changes['note'] = $validator->optionalString('note', self::MAX_NOTE_LENGTH);
        }

        if ($validator->has('needsAttention')) {
            $changes['needsAttention'] = $validator->boolean('needsAttention') ? 1 : 0;
        }

        if ($validator->has('wateringIntervalHours')) {
            $changes['wateringIntervalHours'] = $validator->optionalPositiveInt('wateringIntervalHours');
        }

        if ($validator->has('speciesId')) {
            $changes['speciesId'] = $validator->requiredId('speciesId');
        }

        $validator->assertValid();

        return Response::json(200, PlantResource::make($this->plants->update($request->id(), $changes)));
    }

    public function destroy(Request $request): Response
    {
        $this->plants->delete($request->id());

        return Response::noContent();
    }
}
