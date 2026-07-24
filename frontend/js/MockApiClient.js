export default class MockApiClient {
  #plants;

  constructor(plants) {
    this.#plants = plants.map((plant) => ({ ...plant }));
  }

  async getPlants() {
    return this.#plants.map((plant) => ({ ...plant }));
  }

  async water(plantId) {
    const plant = this.#plants.find((candidate) => candidate.id === plantId);

    if (!plant) {
      throw new Error("404 Not Found");
    }

    plant.nextWatering = new Date(
      Date.now() + (plant.wateringIntervalHours || 72) * 3600000,
    );
    plant.needsAttention = false;

    return { ...plant };
  }
}
